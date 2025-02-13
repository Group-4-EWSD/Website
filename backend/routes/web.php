<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-email', function () {
    Mail::raw('This is a test email from Laravel using Mailtrap!', function ($message) {
        $message->to('your-email@example.com')    // Replace with your email to test
                ->subject('Test Email from Laravel');
    });

    return 'Test email sent successfully!';
});