<?php

use App\Http\Controllers\AttendanceChartController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/attendance/stats', [AttendanceChartController::class, 'stats']);
// routes/web.php
Route::get('/attendance', function () {
    return view('test-broadcast');
});

Route::get('/groups/{group}/events/{event}', [AttendanceController::class, 'show'])
    ->name('group.event.show');

Route::get('/events/{event}/attendance-stats', [EventController::class, 'attendanceStats'])
    ->name('events.attendance.stats');





