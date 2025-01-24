<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('center')->user();

    //dd($users);

    return view('center.home');
})->name('home');

