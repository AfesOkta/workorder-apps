<?php

namespace App\Providers;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //

        // Implicitly grant "Super-Admin" role all permission checks using can()
       Gate::before(function ($user, $ability) {
            if ($user->hasRole('administrator')) {
                return true;
            }

            Gate::define('delete-kegiatan', function (User $user) {
                return $user->hasRole('administrator')
                            ? Response::allow()
                            : Response::deny('You must be an administrator.');
            });
            Gate::define('delete-rekeninig', function (User $user) {
                return $user->hasRole('administrator')
                            ? Response::allow()
                            : Response::deny('You must be an administrator.');
            });
            // Gate::define('delete-tbp',function (User $user){
            //     return $user->hasRole('administrator')
            //                 ? Response::allow()
            //                 : Response::deny('You must be an administrator.');
            // });
            // Gate::define('edit-tbp',function (User $user){
            //     return $user->hasRole('administrator')
            //                 ? Response::allow()
            //                 : Response::deny('You must be an administrator.');
            // });
        });
    }
}
