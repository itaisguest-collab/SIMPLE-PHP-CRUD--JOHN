<?php

namespace App\Providers;
use App\Services\Employee\EmployeeServiceInterface;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   public function register(): void
{
    $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
}
}
