<?php

namespace App\Libraries\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\RequestOptions as _JSON;

class Sms {
    private $url;
    private $owneremail;
    private $subaccount, $subaccount_password;

    public function __construct()
    {
        $this->url = getenv('SMSLIVE_BASE_URL');
        $this->owneremail = getenv('SMSLIVE_OWNER_EMAIL');
        $this->subaccount = getenv('SMSLIVE_SUBACCOUNT');
        $this->subaccount_password = getenv('SMSLIVE_SUBACCOUNT_PASSWORD');

        $defaults = [
            'base_uri' => getenv('SMSLIVE_BASE_URL'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'http_errors' => false,
            'handler' => HandlerStack::create(new CurlHandler()) //use native curl
        ];

        $this->client = new Client($defaults);
    }

    public function sendSms($message, $recipient)
    {
        $sender = "TAU";
        $url = $this->url . "cmd=sendquickmsg" . "&owneremail=" . UrlEncode($this->owneremail) . "&subacct=" . UrlEncode($this->subaccount) . "&subacctpwd=" . UrlEncode($this->subaccount_password) . "&message=" . UrlEncode($message) . "&sender=" . UrlEncode($sender) . "&sendto=" . UrlEncode($recipient) . "&msgtype=" . UrlEncode($recipient);

        if ($f = @fopen($url, "r")) {
            $response = fgets($f, 255);
            if (substr($response, 0, 2) == "OK") {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}