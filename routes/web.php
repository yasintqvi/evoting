<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');

    Route::get('/login/otp', [AuthController::class, 'otpForm'])->name('otp.form');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');


Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');


Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('app.dashboard'))->name('app.index');

    Route::prefix('groups')->group(function () {
        Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/create', [GroupController::class, 'store'])->name('groups.store');
    });


    Route::prefix('{group:slug}')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('groups.index');
    });

    Route::prefix('elections')->group(function () {
        Route::get('/', fn() => view('app.election.index'));

        Route::get('/details', fn() => view('app.election.details'));

        Route::get('/create', fn() => view('app.election.create'));

        Route::get('/candidates', fn() => view('app.election.candidate.index'));
    });
});