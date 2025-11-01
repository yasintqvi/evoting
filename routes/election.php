<?php

use App\Enums\Permission;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ElectionCandidateController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ElectionParticipantController;
use App\Http\Controllers\ElectionVotingController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserExcelController;
use Illuminate\Support\Facades\Route;


Route::prefix('{group:slug}/events/{event}')->group(function () {
    Route::get('elections/{election}/edit', [ElectionController::class, 'edit'])
        ->name('elections.edit')
        ->can(Permission::EDIT_ELECTIONS);
    // Election Routes
    Route::get('/elections', [ElectionController::class, 'index'])
        ->name('elections.index');

    Route::put('/elections/{election}', [ElectionController::class, 'update'])
        ->name('elections.update')
        ->can(Permission::UPDATE_ELECTIONS);

    Route::get('{election}/edit', [ElectionController::class, 'edit'])
        ->name('elections.edit')
        ->can(Permission::EDIT_ELECTIONS);

    Route::delete('/elections/{election}', [ElectionController::class, 'destroy'])
        ->name('elections.delete')
        ->can(Permission::DELETE_ELECTIONS);

    Route::get('elections/{election}/show', [ElectionController::class, 'show'])
        ->name('elections.show')
        ->can(Permission::SHOW_ELECTION);

    Route::get('/elections/create', [ElectionController::class, 'create'])
        ->name('elections.create')
        ->can(Permission::CREATE_ELECTIONS);

    Route::post('/elections/create', [ElectionController::class, 'store'])
        ->name('elections.store')
        ->can(Permission::CREATE_ELECTIONS);

    // Election Candidates
    // Route::get('/elections/{election}/candidates', [ElectionCandidateController::class, 'index'])
    //     ->name('candidates.index');

    Route::get('/groups/{group}/events/{event}/election', [ElectionController::class, 'index']);

    Route::get('/elections/{election}/candidates/create', [ElectionCandidateController::class, 'create'])
        ->name('candidates.create');

    Route::post('/elections/{election}/candidates/create', [ElectionCandidateController::class, 'store'])
        ->name('candidates.store');

    Route::get('/elections/{election}/edit', [ElectionCandidateController::class, 'edit'])
        ->name('candidates.edit')
        ->can(Permission::EDIT_CANDIDATES);

    // Route::put('/elections/{election}/update', [ElectionCandidateController::class, 'update'])->name('candidates.update')
    //     ->can(Permission::UPDATE_CANDIDATES);

    Route::put('/elections/{election:slug}/update', [ElectionCandidateController::class, 'update'])
        ->name('candidates.update')
        ->can(Permission::UPDATE_CANDIDATES);


    // ATTENDANCE
    Route::get('attendances', [AttendanceController::class, 'create'])->name('attendances.create')
        ->can(Permission::CREATE_ATTENDANCE);

    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store')
        ->can(Permission::STORE_ATTENDANCE);

    Route::prefix('/elections/{election}')->group(function () {
        Route::get('/participants', [ElectionParticipantController::class, 'index'])
            ->name('participants.index');

        Route::get('participants/{participant}', [ElectionParticipantController::class, 'show'])
            ->name('participants.show');

        Route::get('/participants/create', [ElectionParticipantController::class, 'create'])
            ->name('participants.create');

        Route::post('/participants/create', [ElectionParticipantController::class, 'store'])
            ->name('participants.store');

        Route::put('/participants/{participant}/edit', [ElectionParticipantController::class, 'update'])
            ->name('participants.update');

        Route::delete('/participants/{participant}/delete', [ElectionParticipantController::class, 'destroy'])
            ->name('participants.delete');

        Route::post('/participants/store-table-participant', [ElectionParticipantController::class, 'storeTableParticipent'])
            ->name('participants.store-table-participant');

        Route::post('/participants/import', [UserExcelController::class, 'import'])
            ->name('participants.import');

        // Voting
        Route::get('/voting', [ElectionVotingController::class, 'create'])
            ->name('voting.create');

        Route::post('/voting', [ElectionVotingController::class, 'store'])
            ->name('voting.store');

        Route::post('/voting/terminate', [ElectionVotingController::class, 'terminate'])
            ->name('voting.terminate');
    });
});

Route::post('/positions', [PositionController::class, 'store'])
    ->name('positions.store');
