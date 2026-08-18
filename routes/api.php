<?php

use App\Http\Controllers\Api\V1\TaskApiController;
use App\Http\Controllers\Api\V1\WorkflowApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Workflows API
    Route::get('/workflows', [WorkflowApiController::class, 'index']);
    Route::get('/workflows/{id}', [WorkflowApiController::class, 'show']);
    Route::post('/workflows/{id}/start', [WorkflowApiController::class, 'start']);
    Route::get('/workflows/track/{uuid}', [WorkflowApiController::class, 'track']);

    // Tasks API
    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::post('/tasks/{id}/approve', [TaskApiController::class, 'approve']);
    Route::post('/tasks/{id}/reject', [TaskApiController::class, 'reject']);
});
