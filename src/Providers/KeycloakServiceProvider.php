<?php

namespace GrupoCometa\Keycloak\Providers;

use GrupoCometa\Keycloak\KeycloakGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class KeycloakServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $local = __DIR__ . '/../../config/keycloak.php';
        $app = base_path("config/keycloak.php");
        $this->publishes([$local => $app], 'config');
        $this->mergeConfigFrom($app, 'keycloak');
    }

    public function register()
    {
        Auth::extend('keycloak', function ($app, $name, array $config) {
            return new KeycloakGuard(Auth::createUserProvider($config['provider']), $app->request);
        });
    }
}
