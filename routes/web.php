<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

Route::get('/test-email', function () {
    Mail::to('mashisavindya@gmail.com')->send(new TestMail());

    return 'Test email sent!';
});

Route::get('/', function () {
    return view('welcome');
});
