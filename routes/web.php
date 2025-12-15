<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\DashboardController;

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
Route::get('/recent-activity', [DashboardController::class, 'recentActivity'])->name('dashboard.activity');

// Halaman Analisis Proposisi (dengan Controller)
Route::get('/proposition-calculator', [PropositionController::class, 'propositionCalculator'])->name('proposition-calculator');

// Route lama untuk kompatibilitas
Route::get('/proposition-checker', function () {
    return view('calculator/proposisi');
})->name('proposition.checker');

// Calculator Routes
Route::get('/calculator', [PropositionController::class, 'index'])->name('calculator');
Route::post('/calculate', [PropositionController::class, 'calculate'])->name('calculate');
Route::post('/parse-expression', [PropositionController::class, 'parseExpression'])->name('parse.expression');
Route::post('/validate-expression', [PropositionController::class, 'validateExpression'])->name('validate.expression');
Route::get('/history', [PropositionController::class, 'history'])->name('history');
Route::get('/logic-laws', [PropositionController::class, 'getLogicLaws'])->name('logic.laws');
Route::get('/examples', [PropositionController::class, 'getPropositionExamples'])->name('examples');
Route::post('/clear-cache', [PropositionController::class, 'clearCache'])->name('clear.cache')->middleware('auth');

// Test routes untuk debugging
Route::get('/test-parser', [PropositionController::class, 'testParser'])->name('test.parser');
Route::get('/test-truth-table', [PropositionController::class, 'testTruthTable'])->name('test.truth.table');
Route::get('/truth-table-example', [PropositionController::class, 'getTruthTableExample'])->name('truth.table.example');