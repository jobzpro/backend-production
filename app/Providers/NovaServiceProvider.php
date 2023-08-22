<?php

namespace App\Providers;

use App\Nova\Benefit;
use App\Nova\UserRole;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\User;
use App\Nova\Account;
use App\Nova\BusinessType;
use App\Nova\Role;
use App\Nova\Company;
use App\Nova\Industry;
use App\Nova\JobApplication;
use App\Nova\JobList;
use App\Nova\JobLocation;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuList;

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

        Nova::mainMenu(function (Request $request){
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),
                MenuSection::make('Account',[
                    MenuItem::resource(Account::class),
                    MenuItem::resource(User::class),
                    MenuItem::resource(UserRole::class),
                    MenuItem::resource(Role::class),
                ])->icon('users')->collapsable(),

                MenuSection::make('Company',[
                    MenuItem::resource(Company::class),
                    MenuItem::resource(Industry::class),
                    MenuItem::resource(BusinessType::class),
                ])->collapsable(),

                MenuSection::make('Job',[
                    MenuItem::resource(JobList::class),
                    MenuItem::resource(JobLocation::class),
                    MenuItem::resource(JobApplication::class),
                ])->icon('briefcase')->collapsable(),

            ];
        });
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
                'admin@jobzpro.com',
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
    }

    //***function to test laravel nova gates on local env**//
    
    // protected function authorization(){
    //     $this->gate();

    //     Nova::auth(function(Request $request){
    //         return Gate::check('viewNova', [$request->user()]);
    //     });
    // }
}
