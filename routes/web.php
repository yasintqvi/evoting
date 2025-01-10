<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;



Route::middleware('guest')->group(function () {
    
    Route::get('/login', [LoginController::class, 'loginForm'])->name('login.form');

    Route::post('/login', [LoginController::class, 'login'])->name('login');
});
