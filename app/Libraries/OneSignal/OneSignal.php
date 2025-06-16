<?php

namespace App\Libraries\OneSignal;

use Log;

class OneSignal {
    protected $oneSignalEndPoint;
    protected $appId;
    protected $apiKey;
    

    public function __construct(){
        $this->oneSignalEndPoint = env('ONESIGNAL_URL');
        $this->appId = env('ONESIGNAL_APP_ID');
        $this->apiKey = env('ONESIGNAL_API_KEY');
    }

    public function sendNotification($heading, $message, $playerIds = [])
    {
        $data = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $playerIds,
            'headings' => ["en" => $heading],
            'contents' => ["en" => $message],
        ];

        $dataok = json_encode($data);

        $url = $this->oneSignalEndPoint;
        $header= array(
            "content-type: application/json",
            "Authorization: Basic ". env('ONESIGNAL_API_KEY'),
        );

    
        try {
            $response = $this->makeCurlRequest($url, $dataok, $header);

            $data = json_decode($response, true);
            return $data;
        } catch (Exception $e) {
            Log::info("Message OneSignal: ". $e->getMessage());
            return false;
        }
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