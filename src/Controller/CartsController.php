<?php
namespace App\Controller;
use Cake\Log\Log;
use Cake\Cache\Cache;
use App\Controller\AppController;

class CartsController extends AppController{

    public function initialize(){
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadModel('Carts');
        $this->loadModel('CartProducts');
        $this->loadModel('CartPersist');
    }

    public function add($id){
        $uid = $this->Auth->user('user_id');
        $cart_product = $this->CartProducts->newEntity();
        $log = $this->CartPersist->newEntity();
        $cart = $this->Carts
            ->findByUserId($uid)
            ->firstOrFail();
        $cart->items++;
        $cart_product = $this->CartProducts->patchEntity($cart_product, [
            'product_id' => $id,
            'cart_id' => $cart->cart_id,
        ]);
        $log = $this->CartPersist->patchEntity($log, [
            'cart_id' => $cart->cart_id,
            'product_id' => $id,
            'user_id' => $uid
        ]);
        if($this->Carts->save($cart)){
            if($this->CartProducts->save($cart_product))
            $this->CartPersist->save($log);
            Log::info("$uid added $id to cart", "cart");
            $this->set([
                'cart' => $cart, 
                'code' => 200, 
                '_serialize'=>['cart', 'code']
            ]);
        }
    }

    public function delete($id){
        $this->request->allowMethod(['post', 'delete']);
        $uid = $this->Auth->user('user_id');
        $cart = $this->Carts->findByUserId($uid)->firstOrFail();
        $cart_product = $this->CartProducts->findByCartId($cart->cart_id)->firstOrFail();
        if ($this->CartProducts->delete($cart_product)) {
            $cart->items--;
            if($this->Carts->save($cart)){
                if($this->CartProducts->save($cart_product)){
                    Log::info("$uid removed $id from cart", "cart");
                    $this->set(['cart' => $cart, 'code' => 200, '_serialize'=>['cart', 'code']]);
                }
            }
        } else {
            $this->set(['cart' => $cart, 'code' => 400, '_serialize'=>['cart', 'code']]);
        }
    }

    public function view(){
        $uid = $this->Auth->user('user_id');
        $cart = $this->Carts->findByUserId($uid)->firstOrFail();
        $cart_products = $this->CartProducts->findByCartId($cart->cart_id);
        $cart->products =[];
        foreach ($cart_products as $cart_product) {
            array_push($cart->products, $this->fetchProduct($cart_product->product_id));
        }
        Log::info("$uid called view cart", "cart");
        $this->set(['cart' => $cart, '_serialize' => ['cart']]);
    }

    function fetchProduct($id){
        $product = Cache::read($id);
        if($product) return $product[0];
        $url = "http://connexity-us.varnish.proxy.sem.infra/services/catalog/v1/api/product?apiKey=7169191781a650332d7d7e3b7cef1df5&publisherId=615979&placementId=1&format=json&offersOnly=true&sort=relevancy_desc&productIdType=SZOID&productId=$id";
        $ch = curl_init();
        // set URL and other appropriate options  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_HEADER, 0);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $output = json_decode($output);
        $result = $output->offers->offer[0];
        return [
            'id' => $result->id,
            'title' => $result->title,
            'description' => $result->description,
            'images' => $result->images,
            'originalPrice' => $result->originalPrice,
            'price' => $result->price
        ];
    }
}