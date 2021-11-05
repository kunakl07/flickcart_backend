<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class CurlComponent extends Component{

    public function get($query) {
        $url = "http://connexity-us.dmz.varnish.proxy.sem.infra/services/catalog/v1/api/product?apiKey=7169191781a650332d7d7e3b7cef1df5&publisherId=615979&placementId=1&format=json&offersOnly=true&sort=relevancy_desc&$query";
        $ch = curl_init();
        $curlheader = array();
        $options = array(
            CURLOPT_URL =>$url,
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($ch, $options);
        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return [json_decode($server_output), $httpcode];
    }
    
}