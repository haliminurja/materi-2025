<?php

namespace App\Providers;

use App\Services\LogService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LogService::class, fn ($app): LogService => new LogService(
            request: $app->make(Request::class),
        ));

        $this->app->singleton(TransactionService::class, static fn ($app): TransactionService => new TransactionService(
            logService: $app->make(LogService::class),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
