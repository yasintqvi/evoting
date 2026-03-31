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

Route::get('/users', [UserController::class, 'index'])
    ->can(Permission::VIEW_USERS)
    ->name('users.index');

Route::get('/users/create', [UserController::class, 'create'])
    ->can(Permission::CREATE_USERS)
    ->name('users.create');

Route::post('/users/create', [UserController::class, 'store'])
    ->name('users.store');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])
    ->can(Permission::UPDATE_USERS)
    ->name('users.edit');

Route::put('users/{user}/update', [UserController::class, 'update'])
    ->can(Permission::UPDATE_USERS)
    ->name('users.update');

Route::post('/users/{user}', [UserController::class, 'show'])
    ->can(Permission::VIEW_USERS)
    ->name('users.show');

Route::delete('users/{user}/delete', [UserController::class, 'destroy'])
    ->can(Permission::DELETE_USERS)
    ->name('users.delete');

Route::get('users/{user}/changes-access', [UserAccessController::class, 'edit'])->name('users.change-access.edit')
    ->can(Permission::CHANGE_ACCESS);

Route::put('users/{user}/changes-access', [UserAccessController::class, 'update'])->name('users.change-access.update')
    ->can(Permission::CHANGE_ACCESS);

Route::get('user-activities', UserActivityController::class)->name('users.activities.index');

Route::post('uplode-users', [UserExcelController::class, 'uplodeExcel'])->name('uplode-users')
    ->can(Permission::IMPORT_USERS);

Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')
    ->can(Permission::VIEW_PERMISSIONS);

Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index')
        ->can(Permission::VIEW_ROLES);
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create')
        ->can(Permission::CREATE_ROLES);
    Route::post('/create', [RoleController::class, 'store'])->name('roles.store')
        ->can(Permission::CREATE_ROLES);
    Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('roles.edit')
        ->can(Permission::EDIT_ROLES);
    Route::put('/update/{role}', [RoleController::class, 'update'])->name('roles.update')
        ->can(Permission::UPDATE_ROLES);
    Route::delete('/delete/{role}', [RoleController::class, 'destroy'])->name('roles.delete')
        ->can(Permission::DELETE_ROLES);
});

Route::prefix('{group:slug}')->group(function () {
    Route::get('/', [GroupController::class, 'index'])
        ->name('groups.index');

    Route::get('/users', [GroupUserController::class, 'index'])
        ->can(Permission::VIEW_GROUP_USERS)
        ->name('group.users.index');

    Route::get('/users/{user}/edit', [GroupUserController::class, 'edit'])
        ->can(Permission::UPDATE_GROUP_USERS)
        ->name('group.users.edit');

    Route::get('/users/create', [GroupUserController::class, 'create'])
        ->can(Permission::CREATE_GROUP_USERS)
        ->name('group.users.create');

    Route::post('/users/create', [GroupUserController::class, 'store'])
        ->name('group.users.store');

    Route::post('/users/add-batch', [GroupUserController::class, 'addUsers'])
        ->name('group.users.add-batch');

    Route::put('users/{user}/update', [GroupUserController::class, 'update'])
        ->can(Permission::UPDATE_GROUP_USERS)
        ->name('group.users.update');

    Route::get('/users/{user}', [GroupUserController::class, 'show'])
        ->can(Permission::VIEW_GROUP_USERS)
        ->name('group.users.show');

    Route::delete('users/{user}/delete', [GroupUserController::class, 'destroy'])
        ->can(Permission::DELETE_GROUP_USERS)
        ->name('group.users.destroy');
});
