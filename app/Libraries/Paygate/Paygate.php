<?php

namespace App\Libraries\Paygate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\RequestOptions as _JSON;


use Log;

class Paygate {
    protected $paymentEndPoint;
    protected $username;
    protected $password;
    

    public function __construct(){
        $this->paymentEndPoint = env('UPPERLINK_CHECKOUT_URL');
        $this->username = env('UPPERLINK_CHECKOUT_USERNAME');
        $this->password = env('UPPERLINK_CHECKOUT_PASSWORD');
        $this->merchantId = env("UPPERLINK_REF");
        $this->requeryEndPoint = env('UPPERLINK_REQUERY_URL');

    }

    public function initializeTransaction($data)
    {
        $dataok = json_encode($data);

        $url = $this->paymentEndPoint;
        $header= array(
            "content-type: application/json",
            "Authorization: Basic ". base64_encode($this->username . ":" . $this->password),
        );

        try {
            $response = $this->makeCurlRequest($url, $dataok, $header);

            $data = json_decode($response, true);
            return $data;
        } catch (Exception $e) {
            Log::info("Message Upperlink paygate payment: ". $e->getMessage());
            return false;
        }

    }
    

    public function verifyTransaction($ref){

        $header= array(
            "content-type: application/json",
            "Authorization: Basic ". base64_encode($this->username . ":" . $this->password),
        );

        $url = $this->requeryEndPoint.'?merchantId='.$this->merchantId.'&ref='.$ref;
    
        $response = $this->makeCurlRequest($url, null, $header, "GET");

        $data = json_decode($response, true);
        return $data;
    }

    public function makeCurlRequest($url, $data, $header, $method = "POST") {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
    
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
    
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Execute cURL and check for errors
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            error_log("cURL Error: " . $error);
            echo "cURL Error: " . $error;
        }

        curl_close($ch);
    
        return $response;
    }
}