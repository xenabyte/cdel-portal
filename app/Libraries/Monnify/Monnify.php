<?php

namespace App\Libraries\Monnify;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Monnify
{
    protected $apiKey;
    protected $secretKey;
    protected $authUrl;
    protected $invoiceUrl;
    protected $verifyTransactionUrl;

    public function __construct()
    {
        $this->apiKey               = env('MONNIFY_API_KEY');
        $this->secretKey            = env('MONNIFY_SECRET_KEY');
        $this->authUrl              = env('MONNIFY_AUTH_URL');
        $this->invoiceUrl           = env('MONNIFY_CREATE_INVOICE_URL');
        $this->verifyTransactionUrl = env('MONNIFY_VERIFY_TRANSACTION_URL');
    }

    /**
     * Get Access Token (Cached for performance)
     */
    protected function getAccessToken()
    {
        return Cache::remember('monnify_access_token', 50, function () {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode("{$this->apiKey}:{$this->secretKey}"),
                        'Content-Type'  => 'application/json',
                    ])
                    ->post($this->authUrl, []);

                $json = $response->json();

                if (isset($json['requestSuccessful']) && $json['requestSuccessful'] === true) {
                    return $json['responseBody']['accessToken'];
                }

                Log::error('Failed to retrieve Monnify access token', $json);
                return null;
            } catch (\Exception $e) {
                Log::error('Monnify Token Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Initiate Invoice with Bearer Token
     */
    public function initiateInvoice(array $paymentData){
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => true, 'message' => 'Unable to authorize request.'];
        }    
        try {
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->post($this->invoiceUrl, $paymentData);

            $response = json_decode($response->body());
            return $response;
        } catch (\Exception $e) {
            Log::error('Monnify Invoice Error: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Invoice creation failed.'];
        }
    }

    /**
     * Verify Invoice with Bearer Token
     */
    public function verifyInvoice(string $reference)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => true, 'message' => 'Unable to authorize request.'];
        }

        $url = rtrim($this->verifyTransactionUrl, '/') . '/' . urlencode($reference);

        try {
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->get($url);

            $response = json_decode($response->body());
            return $response;
        } catch (\Exception $e) {
            Log::error('Monnify Verification Error: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Transaction verification failed.'];
        }
    }
}

//    $response = json_decode($createInvoice);

//         return $response;