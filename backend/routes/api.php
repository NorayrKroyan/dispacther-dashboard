<?php

use App\Http\Controllers\Api\DispatchAssignmentController;
use App\Http\Controllers\Api\DispatchDashboardController;
use App\Http\Controllers\Api\DispatchDriverNoteController;
use App\Http\Controllers\Api\DispatchDriverStateController;
use App\Http\Controllers\Api\DispatchShiftNoteController;
use App\Http\Controllers\Api\DispatchTrackingSnapshotController;
use Illuminate\Support\Facades\Route;

Route::prefix('dispatch')->group(function () {
    Route::get('/jobs', [DispatchDashboardController::class, 'jobs']);
    Route::get('/jobs/{idJoin}/dashboard', [DispatchDashboardController::class, 'show']);

    Route::post('/assignments', [DispatchAssignmentController::class, 'store']);
    Route::post('/assignments/{assignmentId}/deactivate', [DispatchAssignmentController::class, 'deactivate']);

    Route::post('/state', [DispatchDriverStateController::class, 'store']);
    Route::post('/notes', [DispatchDriverNoteController::class, 'store']);
    Route::post('/tracking-snapshot', [DispatchTrackingSnapshotController::class, 'store']);

    Route::get('/jobs/{idJoin}/shift-notes', [DispatchShiftNoteController::class, 'index']);
    Route::post('/jobs/{idJoin}/shift-notes/active', [DispatchShiftNoteController::class, 'upsertActive']);
    Route::post('/jobs/{idJoin}/shift-notes/{noteId}/close', [DispatchShiftNoteController::class, 'close']);
});
