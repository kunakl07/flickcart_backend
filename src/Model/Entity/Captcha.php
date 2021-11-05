<?php
namespace App\Model\Entity;

class Captcha{
    public $url = 'https://www.google.com/recaptcha/api/siteverify';

    function get($extension) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => "$this->url/$extension",
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($ch, $options);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return json_decode($server_output);
    }

    public function post($extension, $values) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL =>$this->url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $values,
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($ch, $options);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return json_decode($server_output);
    }

    public function getWithHeaders($extension, $headers) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL =>"$this->url/$extension",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        );
        curl_setopt_array($ch, $options);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return json_decode($server_output);
    }

    public function postWithHeaders($extension, $values, $headers) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL =>"$this->url/$extension",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $values,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        );
        curl_setopt_array($ch, $options);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return json_decode($server_output);
    }
}

?>
