<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/attendance/stats/{event}', [DashboardController::class, 'index']);
