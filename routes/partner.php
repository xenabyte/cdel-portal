<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('partner')->user();

    //dd($users);

    return view('partner.home');
})->name('home');

