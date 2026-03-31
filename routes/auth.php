<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
    Route::get('/login/otp', [AuthController::class, 'otpForm'])->name('otp.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

//Route::get('test',function(){
//    auth()->loginUsingId(3);
//});
});
