<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use App\Policies\ApiPolicy;
use App\Models\NotificationSetting;
use App\Policies\NotificationSettingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Api::class => ApiPolicy::class,
        NotificationSetting::class => NotificationSettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-access', function ($user) {
            return $user->is_admin;
        });
    }
}
