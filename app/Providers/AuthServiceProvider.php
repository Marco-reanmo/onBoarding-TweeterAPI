<?php

namespace App\Providers;

use App\Models\Tweet;
use App\Models\User;
use App\Policies\TweetPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Tweet::class => TweetPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Password::defaults(function() {
            return Password::min(8)
                ->rules(['max:64'])
                ->letters()
                ->numbers()
                ->symbols();
        });
    }
}
