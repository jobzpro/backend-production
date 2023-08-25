<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class TelescopeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function gate():void {
        Gate::define('viewTelescope', function(User $user){
            return in_array($user->email,[
                'admin@jobzpro.com',
            ]);
        });
    }
}
