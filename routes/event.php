<?php

use App\Enums\Permission;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::prefix('/{group:slug}')->group(function () {
    Route::get('/events', [EventController::class, 'index'])->can(Permission::VIEW_GROUP_EVENT)->name('events.index');
    Route::get('/events/show/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/create', [EventController::class, 'create'])->can(Permission::CREATE_GROUP_EVENT)->name('events.create');
    Route::post('/events/create', [EventController::class, 'store'])->can(Permission::CREATE_GROUP_EVENT)->name('events.store');
    Route::get('/events/edit/{event}', [EventController::class, 'edit'])->can(Permission::EDIT_GROUP_EVENT)->name('events.edit');
    Route::put('/events/update/{event}', [EventController::class, 'update'])->can(Permission::EDIT_GROUP_EVENT)->name('events.update');
    Route::delete('/events/delete/{event}', [EventController::class, 'destroy'])->can(Permission::DELETE_GROUP_EVENT)->name('events.delete');
});
