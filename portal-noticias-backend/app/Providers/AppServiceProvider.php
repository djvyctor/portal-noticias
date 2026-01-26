<?php

namespace App\Providers;

use App\Models\News;
use App\Models\User;
use App\Policies\NewsPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // registra servicos do aplicativo
    public function register(): void
    {
        //
    }

    // inicializa servicos do aplicativo com policies e etc
    public function boot(): void
    {
        // registra as policies para autorizacao
        Gate::policy(News::class, NewsPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
