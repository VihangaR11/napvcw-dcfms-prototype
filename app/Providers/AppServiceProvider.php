<?php

namespace App\Providers;

use App\Models\CaseAssignment;
use App\Models\DcfmsCase;
use App\Policies\CaseAssignmentPolicy;
use App\Policies\DcfmsCasePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Policies\UserPolicy;

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
         Gate::policy(
        DcfmsCase::class,
        DcfmsCasePolicy::class
    );

    Gate::policy(
        CaseAssignment::class,
        CaseAssignmentPolicy::class
    );

    Gate::policy(
    User::class,
    UserPolicy::class
    );
    }
}
