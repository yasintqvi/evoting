<?php

use App\Enums\Permission;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExcelController;
use Illuminate\Support\Facades\Route;


Route::resource('users', UserController::class)
    ->middleware([
        'can:' . Permission::VIEW_USERS->value,
        'can:' . Permission::CREATE_USERS->value,
        'can:' . Permission::EDIT_USERS->value,
        'can:' . Permission::UPDATE_USERS->value,
        'can:' . Permission::DELETE_USERS->value,
    ]);

Route::get('users/{user}/changes-access', [UserAccessController::class, 'edit'])->name('users.change-access.edit')
    ->middleware('can:' . Permission::CHANGE_ACCESS->value);

Route::put('users/{user}/changes-access', [UserAccessController::class, 'update'])->name('users.change-access.update')
    ->middleware('can:' . Permission::CHANGE_ACCESS->value);

Route::get('user-activities', UserActivityController::class)->name('users.activities.index');

Route::post('uplode-users', [UserExcelController::class, 'uplodeExcel'])->name('uplode-users')
    ->middleware('can:' . Permission::IMPORT_USERS->value);

Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')
    ->middleware('can:' . Permission::VIEW_PERMISSIONS->value);

Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index')
        ->middleware('can:' . Permission::VIEW_ROLES->value);
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create')
        ->middleware('can:' . Permission::CREATE_ROLES->value);
    Route::post('/create', [RoleController::class, 'store'])->name('roles.store')
        ->middleware('can:' . Permission::CREATE_ROLES->value);
    Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('roles.edit')
        ->middleware('can:' . Permission::EDIT_ROLES->value);
    Route::put('/update/{role}', [RoleController::class, 'update'])->name('roles.update')
        ->middleware('can:' . Permission::UPDATE_ROLES->value);
    Route::delete('/delete/{role}', [RoleController::class, 'destroy'])->name('roles.delete')
        ->middleware('can:' . Permission::DELETE_ROLES->value);
});


Route::prefix('{group:slug}')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('groups.index');

    Route::resource('users', GroupUserController::class)->names([
        'index' => 'group.users.index',
        'create' => 'group.users.create',
        'store' => 'group.users.store',
        'edit' => 'group.users.edit',
        'update' => 'group.users.update',
        'destroy' => 'group.users.destroy',
    ])->middleware([
        'can:' . Permission::VIEW_GROUP_USERS->value,
        'can:' . Permission::CREATE_GROUP_USERS->value,
        'can:' . Permission::EDIT_GROUP_USERS->value,
        'can:' . Permission::UPDATE_GROUP_USERS->value,
    ]);
});
