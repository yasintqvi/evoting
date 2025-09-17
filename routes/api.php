<?php

use App\Http\Controllers\AttendanceChartController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TestBroadcastController;
use Illuminate\Support\Facades\Route;

Route::get('/attendance/stats/{event}', [DashboardController::class, 'index']);

