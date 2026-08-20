<?php

namespace App\Providers;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFour();

        if (! $this->app->runningInConsole()) {
            $rootUrl = request()->getSchemeAndHttpHost();

            config(['app.url' => $rootUrl]);
            URL::forceRootUrl($rootUrl);
            URL::forceScheme(request()->getScheme());
        }

        // Solo los administradores pasan esta compuerta.
        Gate::define('admin', fn (User $user) => $user->esAdmin());

        // Notificaciones para la campana de la barra superior.
        View::composer('partials.notificaciones-bell', function ($view) {
            $noLeidas = 0;
            $ultimas  = collect();
            // Guardar contra el caso de que la tabla aún no exista (antes de migrar).
            if (Schema::hasTable('notificaciones')) {
                $noLeidas = Notificacion::where('leida', false)->count();
                $ultimas  = Notificacion::orderByDesc('created_at')->limit(8)->get();
            }
            $view->with(['notiNoLeidas' => $noLeidas, 'notiUltimas' => $ultimas]);
        });
    }
}
