<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::put('/update', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/change-password', [ProfileController::class, 'changePassword'])
        ->name('profile.change-password');

    Route::post('/enable-google2fa', [ProfileController::class, 'enableGoogle2fa'])
        ->name('profile.enable-google2fa');

    Route::post('/verify-google2fa', [ProfileController::class, 'verifyGoogle2fa'])
        ->name('profile.verify-google2fa');

    Route::match(['get', 'post'], '/logout', [ProfileController::class, 'logout'])
        ->name('logout');
});
