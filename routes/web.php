<?php

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
use App\Imports\ParticipantsImport;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');

    Route::get('/login/otp', [AuthController::class, 'otpForm'])->name('otp.form');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');


Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('app.dashboard'))->name('app.index');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::post('/enable-google2fa', [ProfileController::class, 'enableGoogle2fa'])->name('profile.enable-google2fa');
        Route::post('/verify-google2fa', [ProfileController::class, 'verifyGoogle2fa'])->name('profile.verify-google2fa');
        Route::match(['get', 'post'], '/logout', [ProfileController::class, 'logout'])->name('logout');
    });

    Route::prefix('groups')->group(function () {
        Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/create', [GroupController::class, 'store'])->name('groups.store');
        Route::get('/edit/{group:slug}', [GroupController::class, 'edit'])->name('groups.edit');
        Route::put('/edit/{group:slug}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/delete/{group:slug}', [GroupController::class, 'destroy'])->name('groups.delete');
        Route::post('/leave/{group:slug}', [GroupController::class, 'leave'])->name('groups.leave');
    });

    Route::resource('users', UserController::class);

    Route::post('uplode-users', [UserExcelController::class, 'uplodeExcel'])->name('uplode-users');

    Route::prefix('{group:slug}')->group(function () {

        Route::get('/', [GroupController::class, 'index'])->name('groups.index');

        Route::prefix('elections')->group(function () {

            Route::get('/', [ElectionController::class, 'index'])->name('elections.index');

            Route::get('/create', [ElectionController::class, 'create'])->name('elections.create');

            Route::post('/create', [ElectionController::class, 'store'])->name('elections.store');

            Route::get('/edit/{election}', [ElectionController::class, 'edit'])->name('elections.edit');

            Route::put('/update/{election}', [ElectionController::class, 'update'])->name('elections.update');

            Route::get('/show/{election}', [ElectionController::class, 'show'])->name('elections.show');

            Route::resource('{election}/candidates', ElectionCandidateController::class)->except('edit' , 'update');

            Route::get('{election}/edit', [ElectionCandidateController::class, 'edit'])->name('candidates.edit');

            Route::put('{election}/update', [ElectionCandidateController::class, 'update'])->name('candidates.update');


            Route::resource('{election}/participants', ElectionParticipantController::class);

            Route::post('{election}/participants/store-table-participent', [ElectionParticipantController::class, 'storeTableParticipent'])->name('participants.store-table-participen');
            
            Route::post('{election}/participants/import', [UserExcelController::class, 'import'])->name('participants.import');

            Route::resource('{election}/election-rounds', ElectionRoundController::class);

            Route::get('{election}/voting', [ElectionVotingController::class, 'create'])->name('voting.create');

            Route::post('{election}/voting', [ElectionVotingController::class, 'store'])->name('voting.store');

            Route::post('{election}/voting/terminate', [ElectionVotingController::class, 'terminate'])->name('voting.terminate');

            Route::resource('/election-users', GroupUserController::class);
        });

        Route::resource('users', GroupUserController::class)->names([
            'index' => 'group.users.index',
            'create' => 'group.users.create',
            'store' => 'group.users.store',
            'edit' => 'group.users.edit',
            'update' => 'group.users.update',
        ]);  
        
    });
});
