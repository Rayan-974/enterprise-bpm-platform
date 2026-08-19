<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Login & Sign Up)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Workspace Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Module
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Workflows, Designer & Drag & Drop Builder
    Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/workflows/create', [WorkflowController::class, 'create'])->name('workflows.create');
    Route::get('/workflows/builder/{id?}', [WorkflowController::class, 'builder'])->name('workflows.builder');
    Route::post('/workflows/designer', [WorkflowController::class, 'storeDesigner'])->name('workflows.storeDesigner');
    Route::get('/workflows/{id}/edit', [WorkflowController::class, 'edit'])->name('workflows.edit');
    Route::put('/workflows/{id}', [WorkflowController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{id}', [WorkflowController::class, 'destroy'])->name('workflows.destroy');
    Route::get('/workflows/{id}/bpmn/export', [WorkflowController::class, 'exportBpmn'])->name('workflows.exportBpmn');
    Route::post('/workflows/bpmn/import', [WorkflowController::class, 'importBpmn'])->name('workflows.importBpmn');
    Route::post('/workflows/{id}/version', [WorkflowController::class, 'createVersion'])->name('workflows.createVersion');
    Route::get('/workflows/{id}', [WorkflowController::class, 'show'])->name('workflows.show');
    Route::post('/workflows/{id}/submit', [WorkflowController::class, 'submit'])->name('workflows.submit');
    Route::get('/workflows/track/{uuid}', [WorkflowController::class, 'track'])->name('workflows.track');

    // Tasks Management Center
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{id}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{id}/delegate', [TaskController::class, 'delegate'])->name('tasks.delegate');

    // Analytics & SLA Intelligence
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/analytics/scan', [AnalyticsController::class, 'scan'])->name('analytics.scan');

    // Audit Trail Inspector
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    // Notifications Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
