<?php

namespace Cometa\KeyCloack\Providers;

use Cometa\KeyCloack\KeyCloackGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class KeyCloackServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $local = __DIR__ . '/../../config/keyCloack.php';
        $app = base_path("config/keyCloack.php");
        $this->publishes([$local => $app], 'config');
        $this->mergeConfigFrom($local, 'keyCloack');
    }

    public function register()
    {
        Auth::extend('keyCloack', function ($app, $name, array $config) {
            return new KeyCloackGuard(Auth::createUserProvider($config['provider']), $app->request);
        });
    }
}
