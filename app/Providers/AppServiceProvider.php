<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No interface bindings needed.
        // Laravel can resolve concrete classes automatically.
    }
}
