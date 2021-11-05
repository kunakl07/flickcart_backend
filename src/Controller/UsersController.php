<?php
namespace App\Controller;

use App\Model\Entity\Captcha;
use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\Auth\DefaultPasswordHasher;
use App\Controller\AppController;
use App\Controller\ProductsController;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController{
    public $secret = "6LfNy9EaAAAAACpT5xWYR6e6FzMq5io2X7YVeaZ9";

    public function initialize(){
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->Auth->allow(['login', 'signup', 'index']);
        $this->loadModel('Carts');
        $this->loadComponent('Curl');
    }

    public function index(){
        $keywords = [
            'Laptops'=> 'lenovo+laptop',
            'Clothes' => 'clothes',
            'Watches' => 'watches'
        ];
        $start=15;
        $results=5;

        $product = new ProductsController;
        foreach($keywords as $keyword => $data){
            $cache = Cache::read("$data/$start/$results");
            if($cache){
                $item[$keyword] = $cache;
            } else{
                $response = $this->Curl->get("keyword=$data&results=$results&start=$start");
                $products = $response[0];
                $offers = $products->offers->offer;
                for($i=0; $i<$products->offers->includedResults; $i++){
                    $result[$i] = $product->extractProduct($offers[$i]);
                }
                Cache::write("$data/$start/$results", $result);
                $item[$keyword] = $result;
            }

        }
        Log::info("Homepage called", "user");
        $this->set(['products' => $item, '_serialize' => ['products']]);
    }

    public function signup(){
        $api = new Captcha();
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $response = $this->request->getData('g-recaptcha-response');
            $captcha = $api->post('', "secret=$this->secret&response=$response");
            if ($captcha->success) {
                $user = $this->Users->patchEntity($user, $this->request->getData());
                if ($this->Users->save($user)) {
                    $cart = $this->Carts->newEntity();
                    $cart = $this->Carts->patchEntity($cart, ["user_id" => $user->user_id, 'items' => 0]);
                    if ($this->Carts->save($cart)) {
                        $message = true;
                    }
                } else {
                    $message = false;
                }
            }
            Log::info($user['name'] . " signed up", 'user');
            $this->set([
                'user' => $user,
                'message' => $message,
                'id' => $user['user_id'],
                '_serialize' => ['user', 'message', 'id']
            ]);
        }
    }

    public function login() {
        $api = new Captcha();
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            $response=$this->request->getData('g-recaptcha-response');
            $captcha = $api->post('', "secret=$this->secret&response=$response");
            if($captcha->success){
                if (!$user) {
                    $this->log("invalid login", 'user');
                }
                Log::info($user['name']." logged in", 'user');
                $this->set([
                    'message' => true,
                    'token' => JWT::encode([
                        'sub' => $user['user_id'],
                        'exp' =>  time() + 3600, // 1 hour
                    ],
                        Security::salt()),
                    'user' => $user,
                    '_serialize' => ['message', 'user', 'token']
                ]);
            }
        }
    }

    public function profile() {
        $this->loadModel('Addresses');
        $this->loadModel('Orders');
        $user = $this->Auth->user();
        $uid = $user['user_id'];
        $cart = $this->Carts
            ->find()
            ->where(['user_id' => $user['user_id']])
            ->firstOrFail();
            Log::info($user["name"]." profile called", 'user');
        $addresses = $this->Addresses->findByUserId($uid);
        $orders = $this->Orders->findByUserId($uid);
        $this->set([
            'user' => [
                'name'=>$user['name'],
                'email'=>$user['email']
            ],
            'cart' => $cart,
            'addresses' => $addresses,
            'orders' => $orders,
            '_serialize' => ['user', 'cart', 'addresses', 'orders']
        ]);
    }
}
