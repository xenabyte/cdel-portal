<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Google;


class GoogleController extends Controller{
    //
    public function redirectToGoogle(Google $google){
        return redirect($google->getAuthUrl());
    }

    public function handleGoogleCallback(Google $google){
        $code = request('code');
        $google->handleCallback($code);

        // Redirect to your desired page after authentication
        return redirect()->route('student.home');
    }
}
