<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ElectionCandidateController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ElectionParticipantController;
use App\Http\Controllers\ElectionUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExcelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');

    Route::get('/login/otp', [AuthController::class, 'otpForm'])->name('otp.form');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');


Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('app.dashboard'))->name('app.index');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::post('/enable-google2fa', [ProfileController::class, 'enableGoogle2fa'])->name('profile.enable-google2fa');
        Route::post('/verify-google2fa', [ProfileController::class, 'verifyGoogle2fa'])->name('profile.verify-google2fa');
        Route::match(['get', 'post'], '/logout', [ProfileController::class, 'logout'])->name('logout');
    });

    Route::prefix('groups')->group(function () {
        Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/create', [GroupController::class, 'store'])->name('groups.store');
    });

    Route::resource('users', UserController::class);

    Route::post('uplode-users', [UserExcelController::class, 'uplodeExcel'])->name('uplode-users');

    Route::prefix('{group:slug}')->group(function () {

        Route::get('/', [GroupController::class, 'index'])->name('groups.index');

        Route::prefix('elections')->group(function () {

            Route::get('/', [ElectionController::class, 'index'])->name('elections.index');

            Route::get('/create', [ElectionController::class, 'create'])->name('elections.create');

            Route::post('/create', [ElectionController::class, 'store'])->name('elections.store');

            Route::get('/show/{election}', [ElectionController::class, 'show'])->name('elections.show');

            Route::resource('{election}/candidates', ElectionCandidateController::class);

            Route::resource('{election}/participants', ElectionParticipantController::class);

            Route::prefix("{election}/voting")->group(function () {
                // Route::get('/', [])
            });

            Route::resource('/election-users', ElectionUserController::class);
        });
    });
});
