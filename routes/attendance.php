<?php

use App\Enums\Permission;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttorneyController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;




Route::post('/get-attorney', [AttorneyController::class, 'getAttorney'])->name('attorneys.index');
Route::post('/create-attorney', [AttorneyController::class, 'storeAttorney'])->name('attorneys.store');
Route::post('/present/{participant}', [AttendanceController::class, 'setPresent'])->name('attendance.present');
Route::post('/delete-attorney/{participant}', [AttorneyController::class, 'deleteAttorney'])->name('attorneys.delete');
Route::get('/{group:slug}/user/select2', [AttendanceController::class, 'getUser'])->name('user.select2');

// Attendance show route for chart display
Route::get('/groups/{group}/attendances/{event}', [EventController::class, 'showAttendance'])
    ->name('attendances.show')
    ->can(Permission::VIEW_GROUP_EVENT);
