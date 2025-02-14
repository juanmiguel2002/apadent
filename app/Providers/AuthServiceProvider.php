<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // User::class => UserPolicy::class,
    ];
    public function boot()
    {
        // $this->registerPolicies();

        // Si deseas definir dinámicamente los permisos de los roles, puedes hacerlo así:
        // if (Auth::check()) {
        //     $roles = Role::with('permissions')->get();
        //     $permissionsArray = [];

        //     foreach ($roles as $role) {
        //         foreach ($role->permissions as $permission) {
        //             $permissionsArray[$permission->name][] = $role->id;
        //         }
        //     }

        //     foreach ($permissionsArray as $name => $roles) {
        //         Gate::define($name, function ($user) use ($roles) {
        //             return count(array_intersect($user->roles->pluck('id')->toArray(), $roles)) > 0;
        //         });
        //     }
        // }
    }
}

