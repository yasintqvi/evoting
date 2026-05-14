<?php

use App\Http\Controllers\Survey\SurveyController;
use App\Http\Controllers\Survey\SurveyParticipantController;
use Illuminate\Support\Facades\Route;

Route::prefix('{group:slug}/events/{event}')->group(function () {
    Route::get('/surveys', [SurveyController::class, 'index'])
        ->name('surveys.index');

    Route::get('/surveys/create', [SurveyController::class, 'create'])
        ->name('surveys.create');

    Route::post('/surveys/create', [SurveyController::class, 'store'])
        ->name('surveys.store');

    Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])
        ->name('surveys.edit');

    Route::put('/surveys/{survey}', [SurveyController::class, 'update'])
        ->name('surveys.update');

    Route::delete('/surveys/delete/{survey}', [SurveyController::class, 'destroy'])
        ->name('surveys.destroy');

    Route::post('/surveys/{survey:slug}/start', [SurveyController::class, 'start'])
        ->name('surveys.start');

    Route::post('/surveys/{survey:slug}/end', [SurveyController::class, 'end'])
        ->name('surveys.end');

    Route::post('/surveys/{survey}/questions', [SurveyController::class, 'storeQuestion'])
        ->name('questions.store');

    Route::get('/surveys/{survey}/questions/{question}/edit', [SurveyController::class, 'editQuestion'])
        ->name('questions.edit');

    Route::put('/surveys/{survey}/questions/{question}', [SurveyController::class, 'updateQuestion'])
        ->name('questions.update');

    Route::delete('/surveys/{survey}/questions/{question}', [SurveyController::class, 'destroyQuestion'])->name('questions.destroy');

    Route::get('/surveys/{survey}/answer', [SurveyController::class, 'showAnswerForm'])
        ->name('surveys.answer');

    Route::post('/surveys/{survey}/answer', [SurveyController::class, 'storeAnswer'])
        ->name('surveys.answer.store');

    Route::get('/surveys/{survey}/statistics', [SurveyController::class, 'statistics'])
        ->name('surveys.statistics');
});

Route::get('/my-surveys', [SurveyParticipantController::class, 'index'])
    ->name('my-surveys.index');
