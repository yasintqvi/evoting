<?php

use App\Http\Controllers\Survey\SurveyController;

Route::prefix('{group:slug}/events/{event}')->group(function () {
    Route::get('/surveys', [SurveyController::class, 'index'])
        ->name('surveys.index');

    Route::get('/surveys/create', [SurveyController::class, 'create'])
        ->name('surveys.create');

    Route::post('/surveys/create', [SurveyController::class, 'store'])
        ->name('surveys.store');

    // Route::get('/surveys/show/{event}', [SurveyController::class, 'show'])
    //     ->name('surveys.show');

    Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])
        ->name('surveys.edit');

    Route::put('/surveys/{survey}', [SurveyController::class, 'update'])
        ->name('surveys.update');

    Route::delete('/surveys/delete/{survey}', [SurveyController::class, 'destroy']);

    Route::post('/surveys/{survey}/questions', [SurveyController::class, 'storeQuestion'])
        ->name('questions.store');

    Route::get('/surveys/{survey}/questions/{question}/edit', [SurveyController::class, 'editQuestion'])
        ->name('questions.edit');

    Route::put('/surveys/{survey}/questions/{question}', [SurveyController::class, 'updateQuestion'])
        ->name('questions.update');
});