<?php

use App\Http\Controllers\AttendanceChartController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttorneyController;
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

Route::post('/get-attorney',[AttorneyController::class,'getAttorney'])->name('attorneys.index');
Route::post('/create-attorney',[AttorneyController::class,'storeAttorney'])->name('attorneys.store');
Route::post('/present/{participant}',[AttendanceController::class,'setPresent'])->name('attendance.present');
Route::post('/delete-attorney/{participant}',[AttorneyController::class,'deleteAttorney'])->name('attorneys.delete');
Route::get('/user/select2',[AttendanceController::class,'getUser'])->name('user.select2');
