<?php

namespace App\Libraries\Monnify;

use Illuminate\Support\Facades\Http;

use Log;

class Monnify
{
    protected $baseUrl;
    protected $api_key;
    protected $secret_key;

    public function __construct()
    {
        $this->baseUrl = env("MONNIFY_INVOICE_URL");
        $this->api_key = env("MONNIFY_API_KEY");
        $this->secret_key = env("MONNIFY_SECRET_KEY");
    }

    public function initiateInvoice($paymentData)
    {
        $createInvoice = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$this->api_key}:{$this->secret_key}"),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, $paymentData);   
        
        $response = json_decode($createInvoice);

        return $response;
    }

    // Implement other methods as needed
}
