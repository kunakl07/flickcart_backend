<?php
namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Log\Log;
use App\Controller\AppController;

class ProductsController extends AppController {
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['search', 'view']);
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Curl');
        $this->loadModel('Comments');
        $this->loadModel('Users');
    }

    function extractProduct($result){
        return [
            'id' => $result->id,
            'title' => $result->title,
            'description' => $result->description,
            'images' => $result->images,
            'originalPrice' => $result->originalPrice,
            'price' => $result->price
        ];
    }

    public function view($id) {
        $product = Cache::read($id);
        $message = 'OK';
        $httpcode=200;
        if( !$product || !$product[0]['id']){
            $response = $this->Curl->get("productIdType=SZOID&productId=$id");
            $result = $response[0];
            $httpcode = $response[1];
            if($httpcode !== 200) {
                $message = 'Error in api call';
                $httpcode= 404;
                Log::error("VPN not turned on", 'product');
            }
            else $message = 'OK';
            $product[0] = $this->extractProduct($result->offers->offer[0]);
            if($product[0]['id']) Cache::write($id, $product);
        }
        $this->loadModel('ProductsViewed');
        $uid = $this->request->header('id');
        if(!$uid) $uid=0;
        $exists = $this->existsInCart($uid, $id);
        $log = $this->ProductsViewed->newEntity();
        $log = $this->ProductsViewed->patchEntity($log, ['user_id' => $uid, 'product_id' => $id, ]);
        Log::info("$uid viewed $id", "product");
        $this->ProductsViewed->save($log);
        $comments = $this->Comments->findByProductId($id);
        foreach ($comments as $comment) {
            $user= $this->Users->findByUserId($comment->user_id)->firstOrFail();
            $comment->name = $user->name;
        }
        $this->set([
            'code' => $httpcode,
            'exists' => $exists,
            'result' => $product,
            'message' => $message,
            'comments' => $comments,
            '_serialize' => ['exists', 'comments', 'result', 'message', 'code']
        ]);
    }

    function existsInCart($uid, $pid){
        $this->loadModel('Carts');
        $this->loadModel('CartProducts');
        $cart = $this->Carts->findByUserId($uid)->firstOrFail();
        $cart_product = $this->CartProducts->findByCartId($cart->cart_id)->where(['product_id' => $pid])->count();
        if($cart_product===0) return false;
        else return true;
    }

    public function search($query) {
        $this->loadModel('Searches');
        $results=20;
        $start = $this->request->query('start');
        $products = Cache::read("$query/$start/$results");
        $httpcode=200;
        $message="Successful";
        if(!$products || !$products['includedResults']){
            $response = $this->Curl->get("keyword=$query&results=$results&start=$start");
            $result = $response[0];
            $httpcode = $response[1];
        
            if($httpcode !== 200) {
                $message = 'Error in api call'; 
                $httpcode= 404;
                Log::error("VPN not turned on", 'product');
            }
            else $message = 'OK';
            $products = [
                'priceSet' => $result->offers->priceSet,
                'includedResults' => $result->offers->includedResults,
                'totalResults' => $result->offers->totalResults
            ];
            $offers = $result->offers->offer;
            for($i=0; $i<$result->offers->includedResults; $i++){
                $products[$i] = $this->extractProduct($offers[$i]);
            }
            if($products['includedResults']) Cache::write("$query/$start/$results", $products);
        }
        $uid = $this->request->header('id');
        if(!$uid) $uid=0;
        $log = $this->Searches->newEntity();
        $query = preg_replace('/\+/', ' ', $query);
        $log = $this->Searches->patchEntity($log, ['user_id' => $uid, 'query' => $query, ]);
        $this->Searches->save($log);
        Log::info("$uid searched for $query", "product");
        $this->set([
            'code' => $httpcode,
            'result' => $products,
            'message' => $message,
            '_serialize' => ['result', 'message', 'code']
        ]);
    }

    public function comment(){
        if($this->request->is('post')) {
            $uid = $this->Auth->user('user_id');
            $pid = $this->request->getData('id');
            $comment = $this->Comments->newEntity();
            $comment = $this->Comments->patchEntity($comment, [
                'comment' => $this->request->getData('text'),
                'user_id' => $uid,
                'product_id' => $pid
            ]);
            if($this->Comments->save($comment)){
                Log::info("$uid commented on $pid", 'product');
                $this->set([
                    'code' => 200,
                    'comment' => $comment,
                    '_serialize' => [
                        'code', 
                        'comment'
                    ]
                ]);
            }
        }
    }

    public function order(){
        if($this->request->is('post')) {
            $this->loadModel('Orders');
            $order = $this->Orders->newEntity();
            $products = $this->request->getData('order');
            $address_id = $this->request->getData('address');
            $uid = $this->Auth->user('user_id');
            $date = strtotime("+7 day");
            $delivery = date('Y-m-d',$date);
            $this->updateCart($uid);
            $order = $this->Orders->patchEntity($order, [
                'user_id' => $uid,
                'address_id' => $address_id,
                'delivery_date' => $delivery,
                'products' => $products
            ]);
            $this->Orders->save($order);
            Log::info("$uid ordered $order->order_id", 'order');
            $this->set([
                'code' => 200,
                'order' => $order,
                '_serialize' => ['order', 'code']
            ]);
        }
    }

    function updateCart($uid){
        $this->loadModel('Carts');
        $this->loadModel('CartProducts');
        $cart = $this->Carts->findByUserId($uid)->firstOrFail();
        debug($cart);
        $cart_product = $this->CartProducts->findByCartId($cart->cart_id);
        foreach($cart_product as $product){
            $this->CartProducts->delete($product);
        }
        $cart->items=0;
        Log::info("$cart emptied", 'cart');
        $this->Carts->save($cart);
    }
}
