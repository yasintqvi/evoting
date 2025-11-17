<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModulePermissionController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/attendance/stats/{event}', [DashboardController::class, 'index']);
Route::get('/get/{module}/group/{group}',ModulePermissionController::class);
