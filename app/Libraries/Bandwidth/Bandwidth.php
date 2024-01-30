<?php

namespace App\Libraries\Bandwidth;

use Log;


class Bandwidth {
    protected $balanceUrl;
    protected $purchaseUrl;
    protected $newUserUrl;
    protected $apiToken;
    protected $checkUserUrl;

    public function __construct()
    {
        $this->balanceUrl = env('BANDWIDTH_BALANCE_URL');
        $this->purchaseUrl = env('BANDWIDTH_PURCHASE_URL');
        $this->newUserUrl = env('BANDWIDTH_NEW_USER_URL');
        $this->apiToken = env('APP_API_KEY');
        $this->checkUserUrl = env('BANDWIDTH_CHECKUSER_URL');

    }

    public function validateUser($username){
        $postfields = array(
            'username' => $username,
            'token' => $this->apiToken
        );

        $dataok = json_encode($postfields);

        $url = $this->checkUserUrl;
        $header= array(
        "content-type: application/json"
        );

        $response = $this->makeCurlRequest($url, $dataok, $header);

        $data = json_decode($response, true);
        return $data;
    }

    public function checkDataBalance($username, $password){
        $postfields = array(
            'username' => $username,
            'password' => $password,
            'token' => $this->apiToken
        );

        $dataok = json_encode($postfields);

        $url = $this->balanceUrl;
        $header= array(
        "content-type: application/json"
        );

        $response = $this->makeCurlRequest($url, $dataok, $header);

        $data = json_decode($response, true);
        return $data;
    }

    public function addToDataBalance($username, $bandwidth){
        $postfields = array(
            'username' => $username,
            'bandwidth' => $bandwidth,
            'token' => $this->apiToken
        );

        $dataok = json_encode($postfields);

        $url = $this->purchaseUrl;
        $header= array(
        "content-type: application/json"
        );

        $response = $this->makeCurlRequest($url, $dataok, $header);

        $data = json_decode($response, true);
        return $data;
    }

    public function createUser($userData){
        $postfields = array(
            'username' => $username,
            'bandwidth' => $bandwidth,
            'token' => $this->apiToken
        );

        $dataok = json_encode($postfields);

        $url = $this->newUserUrl;
        $header= array(
        "content-type: application/json"
        );

        $response = $this->makeCurlRequest($url, $dataok, $header);

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

