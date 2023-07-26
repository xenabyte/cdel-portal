<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('bursary')->user();

    //dd($users);

    return view('bursary.home');
})->name('home');

