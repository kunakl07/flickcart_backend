<?php
namespace App\Controller;

use Cake\Log\Log;
use App\Controller\AppController;

class ReportsController extends AppController{
    public function initialize(){
        parent::initialize();
        $this->Auth->allow(['daily', 'products']);
    }

    public function daily($date){
        $this->loadModel("ProductsViewed");
        $this->loadModel("CartPersist");
        $this->loadModel('Orders');
        $this->loadModel('Searches');
        $products = $this->ProductsViewed->find()->where(["DATE(created) =" => $date]);
        $orders = $this->Orders->find()->where(["DATE(created) =" => $date]);
        $delivered = $this->Orders->find()->where(["delivery_date =" => $date]);
        $carts = $this->CartPersist->find()->where(["DATE(created) =" => $date]);
        $searches = $this->Searches->find()->where(["DATE(created) =" => $date]);
        $this->set([
            'productsviewed'=> $products, 
            'orders' => $orders,
            'carts' => $carts,
            'delivered' => $delivered,
            'searches' => $searches,
            '_serialize' => ['orders','carts','delivered','productsviewed', 'searches']
        ]);
    }

    public function products(){
        if($this->request->query('from')) {
            $this->loadModel("ProductsViewed");
            $this->loadModel('Searches');
            $current = strtotime($this->request->query('from'));
            $last = strtotime($this->request->query('to'));
            $products=[];
            $searches=[];
            $days=[];
            $format = 'Y-m-d';
            while($current <= $last){
                $today = date($format, $current);
                array_push($days, $today);
                $current = strtotime('+1 day', $current);
                $pquery = $this->ProductsViewed->find()
                    ->select(['product_id','count'=>'COUNT(*)'])
                    ->where(["DATE(created)" => $today]);
                $ptotal[] = $pquery->count();
                $product = $pquery->group(['product_id'])->all();
                $products[$today]= [$product];
                $squery = $this->Searches->find()
                    ->select(['query','count'=>'COUNT(*)'])
                    ->where(["DATE(created)" => $today]);
                $stotal[] = $squery->count();
                $search = $squery->group(['query'])->all();
                $searches[$today]= [$search];
            }
            $this->set([
                'days'=>json_encode(array_values($days)),
                'products'=>json_decode(json_encode($products), true), 
                'ptotal'=>json_encode(array_values($ptotal)),
                'searches'=>json_decode(json_encode($searches), true), 
                'stotal'=>json_encode(array_values($stotal)),
            ]);
        }
    }
}