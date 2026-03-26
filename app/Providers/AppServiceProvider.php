<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\{User, Fee, Transaction};
use App\Policies\{UserPolicy, FeePolicy, TransactionPolicy};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Register authorization policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Fee::class, FeePolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
    }
}
