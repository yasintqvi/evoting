<?php

use App\Http\Controllers\AttendanceChartController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/attendance/stats', [AttendanceChartController::class, 'stats']);
// routes/web.php
Route::get('/attendance', function () {
    return view('test-broadcast');
});

// routes/web.php
Route::get('/events/{event}/attendance-stats', [EventController::class, 'attendanceStats'])
    ->name('events.attendance.stats');



