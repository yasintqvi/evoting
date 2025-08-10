<?php

use App\Enums\Permission;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('groups')->group(function () {
    Route::get('/create', [GroupController::class, 'create'])
        ->name('groups.create')
        ->can(Permission::CREATE_GROUP);

    Route::post('/create', [GroupController::class, 'store'])
        ->name('groups.store')
        ->can(Permission::CREATE_GROUP);

    Route::get('/edit/{group:slug}', [GroupController::class, 'edit'])
        ->name('groups.edit')
        ->can(Permission::EDIT_GROUP);

    Route::put('/edit/{group:slug}', [GroupController::class, 'update'])
        ->name('groups.update')
        ->can(Permission::UPDATE_GROUP);

    Route::delete('/delete/{group:slug}', [GroupController::class, 'destroy'])
        ->name('groups.delete')
        ->can(Permission::DELETE_GROUP);

    Route::post('/leave/{group:slug}', [GroupController::class, 'leave'])
        ->name('groups.leave');
});
