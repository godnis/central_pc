<?php

namespace App\Providers;

use App\Enums\RoleUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // Técnico e admin podem cadastrar/editar máquinas, setores e o catálogo.
        Gate::define('editar', fn (User $user) => in_array($user->role, [RoleUsuario::Admin, RoleUsuario::Tecnico], true));

        // Só admin exclui (inclusive restaurar/apagar definitivamente da lixeira).
        Gate::define('excluir', fn (User $user) => $user->role === RoleUsuario::Admin);
    }
}
