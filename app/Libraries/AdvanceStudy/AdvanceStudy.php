<?php

namespace App\Libraries\AdvanceStudy;

use Log;


class AdvanceStudy {
    protected $advancedStudiesProgrammesUrl;
    protected $advandedStudiesProgrammeUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->advancedStudiesProgrammesUrl = env('ADVANCED_STUDIES_PROGRAMMES_URL');
        $this->advandedStudiesProgrammeUrl = env('ADVANCED_STUDIES_PROGRAMME_URL');
        $this->newUserUrl = env('BANDWIDTH_NEW_USER_URL');
        $this->apiToken = env('APP_API_KEY');
        $this->checkUserUrl = env('BANDWIDTH_CHECKUSER_URL');

    }

    public function getProgrammes(){

        $url = $this->advancedStudiesProgrammesUrl;
        $header= array(
            "content-type: application/json"
        );

        try {
            $response = $this->makeCurlRequest($url, null, $header, 'GET');

            $data = json_decode($response, true);
            return $data['data'];
        } catch (Exception $e) {
            Log::info("Message validate Bandwidth User: ". $e->getMessage());
            return false;
        }
    }

    public function getProgramme($id){

        $url = $this->advandedStudiesProgrammeUrl.'/'.$id;
        $header= array(
            "content-type: application/json"
        );

        try {
            $response = $this->makeCurlRequest($url, null, $header, "GET");

            $data = json_decode($response, true);
            return $data;
        } catch (Exception $e) {
            Log::info("Message Bandwidth balance: ". $e->getMessage());
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

