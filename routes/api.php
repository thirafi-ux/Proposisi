<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PropositionAPIController;

Route::prefix('v1')->group(function () {
    Route::post('/calculate', [PropositionAPIController::class, 'calculate']);
    Route::get('/history', [PropositionAPIController::class, 'history']);
    Route::get('/logic-laws', [PropositionAPIController::class, 'getLogicLaws']);
    Route::post('/validate', [PropositionAPIController::class, 'validateExpression']);
});