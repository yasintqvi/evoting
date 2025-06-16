<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ElectionCandidateController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ElectionParticipantController;
use App\Http\Controllers\ElectionRoundController;
use App\Http\Controllers\ElectionVotingController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExcelController;
use App\Enums\Permission;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
    Route::get('/login/otp', [AuthController::class, 'otpForm'])->name('otp.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('app.index');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::post('/enable-google2fa', [ProfileController::class, 'enableGoogle2fa'])->name('profile.enable-google2fa');
        Route::post('/verify-google2fa', [ProfileController::class, 'verifyGoogle2fa'])->name('profile.verify-google2fa');
        Route::match(['get', 'post'], '/logout', [ProfileController::class, 'logout'])->name('logout');
    });

    Route::prefix('groups')->group(function () {
        Route::get('/create', [GroupController::class, 'create'])->name('groups.create')
            ->middleware('can:' . Permission::CREATE_GROUP->value);
        Route::post('/create', [GroupController::class, 'store'])->name('groups.store')
            ->middleware('can:' . Permission::CREATE_GROUP->value);
        Route::get('/edit/{group:slug}', [GroupController::class, 'edit'])->name('groups.edit')
            ->middleware('can:' . Permission::EDIT_GROUP->value);
        Route::put('/edit/{group:slug}', [GroupController::class, 'update'])->name('groups.update')
            ->middleware('can:' . Permission::UPDATE_GROUP->value);
        Route::delete('/delete/{group:slug}', [GroupController::class, 'destroy'])->name('groups.delete')
            ->middleware('can:' . Permission::DELETE_GROUP->value);
        Route::post('/leave/{group:slug}', [GroupController::class, 'leave'])->name('groups.leave');
    });

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

        Route::prefix('elections')->group(function () {
            Route::get('/', [ElectionController::class, 'index'])->name('elections.index');

            Route::get('/create', [ElectionController::class, 'create'])->name('elections.create')
                ->middleware('can:' . Permission::CREATE_ELECTIONS->value);
            Route::post('/create', [ElectionController::class, 'store'])->name('elections.store')
                ->middleware('can:' . Permission::CREATE_ELECTIONS->value);
            Route::get('/edit/{election}', [ElectionController::class, 'edit'])->name('elections.edit')
                ->middleware('can:' . Permission::EDIT_ELECTIONS->value);
            Route::put('/update/{election}', [ElectionController::class, 'update'])->name('elections.update')
                ->middleware('can:' . Permission::UPDATE_ELECTIONS->value);
            Route::delete('/delete/{election}', [ElectionController::class, 'destroy'])->name('elections.delete')
                ->middleware('can:' . Permission::DELETE_ELECTIONS->value);
            Route::get('/show/{election}', [ElectionController::class, 'show'])->name('elections.show')
                ->middleware('can:' . Permission::SHOW_ELECTION->value);

            Route::resource('{election}/candidates', ElectionCandidateController::class)->except('edit', 'update')
                ->middleware([
                    'can:' . Permission::VIEW_CANDIDATES->value,
                    'can:' . Permission::CREATE_CANDIDATES->value,
                    'can:' . Permission::DELETE_CANDIDATES->value,
                ]);

            Route::get('{election}/edit', [ElectionCandidateController::class, 'edit'])->name('candidates.edit')
                ->middleware('can:' . Permission::EDIT_CANDIDATES->value);
            Route::put('{election}/update', [ElectionCandidateController::class, 'update'])->name('candidates.update')
                ->middleware('can:' . Permission::UPDATE_CANDIDATES->value);

            Route::resource('{election}/participants', ElectionParticipantController::class)
                ->middleware([
                    'can:' . Permission::VIEW_PARTICIPANTS->value,
                    'can:' . Permission::CREATE_PARTICIPANTS->value,
                    'can:' . Permission::EDIT_PARTICIPANTS->value,
                    'can:' . Permission::UPDATE_PARTICIPANTS->value,
                    'can:' . Permission::DELETE_PARTICIPANTS->value,
                ]);

            Route::post('{election}/participants/store-table-participent', [ElectionParticipantController::class, 'storeTableParticipent'])
                ->name('participants.store-table-participen')
                ->middleware('can:' . Permission::STORE_TABLE_PARTICIPANT->value);

            Route::post('{election}/participants/import', [UserExcelController::class, 'import'])
                ->name('participants.import')
                ->middleware('can:' . Permission::IMPORT_PARTICIPANTS->value);

            Route::resource('{election}/election-rounds', ElectionRoundController::class)
                ->middleware([
                    'can:' . Permission::VIEW_ELECTION_ROUNDS->value,
                    'can:' . Permission::CREATE_ELECTION_ROUNDS->value,
                    'can:' . Permission::EDIT_ELECTION_ROUNDS->value,
                    'can:' . Permission::UPDATE_ELECTION_ROUNDS->value,
                    'can:' . Permission::DELETE_ELECTION_ROUNDS->value,
                ]);

            Route::get('{election}/voting', [ElectionVotingController::class, 'create'])->name('voting.create');
            Route::post('{election}/voting', [ElectionVotingController::class, 'store'])->name('voting.store');
            Route::post('{election}/voting/terminate', [ElectionVotingController::class, 'terminate'])->name('voting.terminate');

            Route::resource('/election-users', GroupUserController::class)
                ->middleware([
                    'can:' . Permission::VIEW_GROUP_USERS->value,
                    'can:' . Permission::CREATE_GROUP_USERS->value,
                    'can:' . Permission::EDIT_GROUP_USERS->value,
                    'can:' . Permission::UPDATE_GROUP_USERS->value,
                    'can:' . Permission::DELETE_GROUP_USERS->value,
                ]);

            Route::get('attendances', [AttendanceController::class, 'create'])->name('attendances.create')
                ->middleware('can:' . Permission::CREATE_ATTENDANCE->value);
            Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store')
                ->middleware('can:' . Permission::STORE_ATTENDANCE->value);
        });

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
});
