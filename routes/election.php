<?php

use App\Enums\Permission;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ElectionCandidateController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ElectionParticipantController;
use App\Http\Controllers\ElectionVotingController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserExcelController;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('{group:slug}/events/{event:slug}')->group(function () {
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

    Route::get('elections/{election:slug}/show', [ElectionController::class, 'show'])
        ->name('elections.show')
        ->can(Permission::SHOW_ELECTION);

    Route::get('elections/{election:slug}/report', [ElectionController::class, 'report'])
        ->name('elections.report')
        ->can(Permission::SHOW_ELECTION);

    Route::get('/elections/{election:slug}/detailed-report', [ElectionController::class, 'detailedReport'])->name('elections.detailed-report');

    Route::get('/elections/create', [ElectionController::class, 'create'])
        ->name('elections.create')
        ->can(Permission::CREATE_ELECTIONS);

    Route::post('/elections/create', [ElectionController::class, 'store'])
        ->name('elections.store')
        ->can(Permission::CREATE_ELECTIONS);

    Route::post('/elections/copy-from/{sourceId}', [ElectionController::class, 'copyFrom'])
        ->name('elections.copy-from')
        ->can(Permission::CREATE_ELECTIONS);

    // Election Candidates
    Route::get('/elections/{election}/candidates/create', [ElectionCandidateController::class, 'create'])
        ->name('candidates.create');

    Route::post('/elections/{election}/candidates/create', [ElectionCandidateController::class, 'store'])
        ->name('candidates.store');

    Route::post('/elections/{election}/candidates/quick-create-user', [ElectionCandidateController::class, 'quickCreateUser'])
        ->name('candidates.quick-create-user');

    Route::get('/elections/{election}/candidates/search-shareholders', [ElectionCandidateController::class, 'searchGroupShareholders'])
        ->name('candidates.search-shareholders');

    Route::get('/elections/{election}/edit', [ElectionCandidateController::class, 'edit'])
        ->name('candidates.edit')
        ->can(Permission::EDIT_CANDIDATES);

    Route::post('/elections/{election:slug}/start', [ElectionController::class, 'start'])
        ->name('elections.start')
        ->can(Permission::CREATE_ELECTION_ROUNDS);

    Route::post('/elections/{election:slug}/end', [ElectionController::class, 'end'])
        ->name('elections.end');

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

    Route::prefix('/elections/{election:slug}')->group(function () {
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

        // Voting - استفاده از route بدون route model binding برای election
        Route::get('/voting', [ElectionVotingController::class, 'create'])
            ->name('voting.create');

        // Route::get('/{event}/live-election-stats', [ElectionVotingController::class, 'liveStats']);
        // Route اصلی
        Route::post('/voting', function (Request $request, Group $group, Event $event) {
            $electionSlug = $request->route()->parameter('election') ?? $request->input('election_slug');
            $election = Election::where('event_id', $event->id)
                ->where('slug', $electionSlug)
                ->firstOrFail();

            $controller = app(ElectionVotingController::class);

            return $controller->store($request, $group, $event, $election);
        })->name('voting.store');

        Route::post('elections/voting/terminate', [ElectionVotingController::class, 'terminate'])
            ->name('voting.terminate');
    });
});

Route::post('/positions', [PositionController::class, 'store'])
    ->name('positions.store');
