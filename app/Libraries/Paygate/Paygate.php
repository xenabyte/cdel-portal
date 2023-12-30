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

    public function __construct()
    {
        $this->apiEndpoint = getenv('SMSLIVE_BASE_URL');
        $this->apiToken = getenv('SMSLIVE_BASE_URL');

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
}