<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use App\Repositories\Eloquent\WorkflowRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Contracts\FormRepositoryInterface;
use App\Repositories\Eloquent\FormRepository;
use App\Repositories\Contracts\AuditRepositoryInterface;
use App\Repositories\Eloquent\AuditRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WorkflowRepositoryInterface::class, WorkflowRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(FormRepositoryInterface::class, FormRepository::class);
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
