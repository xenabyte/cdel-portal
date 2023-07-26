<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('guardian')->user();

    //dd($users);

    return view('guardian.home');
})->name('home');

