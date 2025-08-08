<?php

use App\Enums\Permission;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ElectionCandidateController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ElectionParticipantController;
use App\Http\Controllers\ElectionVotingController;
use App\Http\Controllers\UserExcelController;
use Illuminate\Support\Facades\Route;

Route::prefix('{group:slug}/events/{event}/elections')->group(function () {
    Route::get('/', [ElectionController::class, 'index'])->name('elections.index');

    Route::get('/create', [ElectionController::class, 'create'])
        ->name('elections.create')
        ->can(Permission::CREATE_ELECTIONS);

    Route::post('/create', [ElectionController::class, 'store'])
        ->name('elections.store')
        ->can(Permission::CREATE_ELECTIONS);

    Route::get('/edit/{election}', [ElectionController::class, 'edit'])
        ->name('elections.edit')
        ->can(Permission::EDIT_ELECTIONS);

    Route::put('/update/{election}', [ElectionController::class, 'update'])->name('elections.update')
        ->can(Permission::UPDATE_ELECTIONS);

    Route::delete('/delete/{election}', [ElectionController::class, 'destroy'])
        ->name('elections.delete')
        ->can(Permission::DELETE_ELECTIONS);

    Route::get('/show/{election}', [ElectionController::class, 'show'])
        ->name('elections.show')
        ->can(Permission::SHOW_ELECTION);

    Route::get('/{election}/candidates', [ElectionCandidateController::class, 'index'])
        ->name('candidates.index');

    Route::get('/{election}/candidates/create', [ElectionCandidateController::class, 'create'])
        ->name('candidates.create');

    Route::post('/{election}/candidates/create', [ElectionCandidateController::class, 'store'])
        ->name('candidates.store');

    Route::get('{election}/edit', [ElectionCandidateController::class, 'edit'])
        ->name('candidates.edit')
        ->can(Permission::EDIT_CANDIDATES);

    Route::put('{election}/update', [ElectionCandidateController::class, 'update'])->name('candidates.update')
        ->can(Permission::UPDATE_CANDIDATES);

    // Route::resource('{election}/participants', ElectionParticipantController::class)
    //     ->can([
    //         Permission::VIEW_PARTICIPANTS,
    //         Permission::CREATE_PARTICIPANTS,
    //         Permission::EDIT_PARTICIPANTS,
    //         Permission::UPDATE_PARTICIPANTS,
    //         Permission::DELETE_PARTICIPANTS,
    //     ]);

    Route::post('{election}/participants/store-table-participent', [ElectionParticipantController::class, 'storeTableParticipent'])
        ->name('participants.store-table-participen')
        ->can(Permission::STORE_TABLE_PARTICIPANT);

    Route::post('{election}/participants/import', [UserExcelController::class, 'import'])
        ->name('participants.import')
        ->can(Permission::IMPORT_PARTICIPANTS);

    Route::get('{election}/voting', [ElectionVotingController::class, 'create'])->name('voting.create');
    Route::post('{election}/voting', [ElectionVotingController::class, 'store'])->name('voting.store');
    Route::post('{election}/voting/terminate', [ElectionVotingController::class, 'terminate'])->name('voting.terminate');

    Route::get('attendances', [AttendanceController::class, 'create'])->name('attendances.create')
        ->can(Permission::CREATE_ATTENDANCE);
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store')
        ->can(Permission::STORE_ATTENDANCE);
});
