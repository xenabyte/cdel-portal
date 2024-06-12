<?php

namespace App\Libraries\Paygate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\RequestOptions as _JSON;


class Paygate {
    protected $apiEndpoint;
    protected $apiToken;

    public function __construct(){
        // $this->apiEndpoint = getenv('SMSLIVE_BASE_URL');
        // $this->apiToken = getenv('SMSLIVE_BASE_URL');
    }

    public function initializeTransaction($data)
    {
        $client = new Client();

        try {
            $response = $client->post($this->apiEndpoint, [
                'form_params' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Log or handle the exception as needed
            return ['error' => $e->getMessage()];
        }
    }

    public function verifyTransaction($data){

        $upperLinkRequeryUrl = env('UPPERLINK_REQUERY_URL');
        $transactionId = $data->transactionId;
        $merchantId = env("UPPERLINK_MERCHANT_ID");

        $header= array(
            "content-type: application/json"
        );

        $url = $upperLinkRequeryUrl.'?transaction_id='.$transactionId.'&merchant_id='.$merchantId;
    
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