<?php

namespace App\Providers;

use App\Services\LogService;
use App\Services\RedisService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Redis\RedisManager;
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


        $this->app->singleton(RedisService::class, static fn ($app): RedisService => new RedisService(
            redisManager: $app->make(RedisManager::class)
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
