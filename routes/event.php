<?php

use App\Enums\Permission;
use App\Http\Controllers\CloneEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventStartController;
use Illuminate\Support\Facades\Route;

Route::prefix('/{group:slug}')->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->can(Permission::CREATE_GROUP_EVENT)->name('events.create');
    Route::get('/events', [EventController::class, 'index'])->can(Permission::VIEW_GROUP_EVENT)->name('events.index');
    Route::get('/events/{event:slug}', [EventController::class, 'show'])->can(Permission::VIEW_GROUP_EVENT)->name('events.show');
    Route::post('/events', [EventController::class, 'store'])->can(Permission::CREATE_GROUP_EVENT)->name('events.store');
    Route::get('/events/{event:slug}/edit', [EventController::class, 'edit'])->can(Permission::EDIT_GROUP_EVENT)->name('events.edit');
    Route::put('/events/{event:slug}', [EventController::class, 'update'])->can(Permission::EDIT_GROUP_EVENT)->name('events.update');
    Route::delete('/events/{event:slug}/delete', [EventController::class, 'destroy'])->can(Permission::DELETE_GROUP_EVENT)->name('events.delete');
    Route::get('events/{event:slug}/clone',CloneEventController::class)->name('events.clone');
});

Route::get('/groups/{group:slug}/events/{event}', [EventController::class, 'show'])
    ->name('group.event.show');


Route::get('/groups/{group:slug}/events/{event}/edit', [EventController::class, 'edit'])
    ->name('group.event.edit');

Route::get('/events/{event}/attendance-stats', [EventController::class, 'attendanceStats'])
    ->name('events.attendance.stats');
