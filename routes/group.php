<?php

use App\Enums\Permission;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupPermissionController;
use App\Http\Controllers\GroupUserAccessController;
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

    Route::get('/roles/{group}',[GroupPermissionController::class,'index'])->name('group.permissions');
    Route::get('/roles/{group}/create',[GroupPermissionController::class,'create'])->name('group.permissions.create');
    Route::post('/role/{group}/store',[GroupPermissionController::class,'store'])->name('group.permissions.store');

    Route::get('/roles/{group}/edit/{role}',[GroupPermissionController::class,'edit'])->name('group.permissions.edit');
    Route::put('/roles/{group}/update/{role}',[GroupPermissionController::class,'update'])->name('group.permissions.update');
    Route::delete('/roles/{group}/delete/{role}',[GroupPermissionController::class,'destroy'])->name('group.permissions.destroy');

    //group user permissions change
    Route::get('/users/change/{group}/permissions/{user}',[GroupUserAccessController::class,'edit'])->name('user.group.permissions');

    Route::put('/users/change/{group}/permissions/{user}',[GroupUserAccessController::class,'update'])->name('user.group.permissions.update');

});
