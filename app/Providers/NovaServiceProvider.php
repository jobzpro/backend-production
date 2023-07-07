<?php

namespace App\Providers;

use App\Nova\UserRole;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\User;
use App\Nova\Account;
use App\Nova\Role;
use App\Nova\Company;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Dashboards\Main;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Nova::mainMenu(function (Request $request){
        //     return [
        //         MenuSection::dashboard(Main::class)->icon('chart-bar'),
        //         MenuSection::make('Account',[
        //             MenuItem::resource(User::class),
        //             MenuItem::resource(UserRole::class),
        //         ]),
        //     ];
        // });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
                'yukarihirai15@gmail.com',
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public function resources(){
        Nova::resourcesIn(app_path('Nova'));

        // Nova::resources([
        //     Account::class,
        //     Company::class,
        //     User::class,
        //     Role::class,
        //     UserRole::class,
        // ]);
    }
}
