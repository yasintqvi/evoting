<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;



Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'loginForm'])->name('login.form');

    Route::post('/login', [LoginController::class, 'login'])->name('login');
});
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('app.index'))->name('app.index');

    Route::prefix('elections')->group(function () {
        Route::get('/', fn() => view('app.election.index'));

        Route::get('/details', fn() => view('app.election.details'));

        Route::get('/create', fn() => view('app.election.create'));

        Route::get('/candidates', fn() => view('app.election.candidate.index'));
    });
});
