#### Cometa KeyCloack

Uma simples biblioteca para "authentication/authorization" no sso _[keycloack](https://www.keycloak.org/)_ ultilizando o protrotocolo **openid-connect**.

A **authorization** funciona apens para permissoes baseadas em _escopos_ ou permissoes baseadas em _recursos_, para saber mais acesse _[KeyCloack Authorization Services](https://www.keycloak.org/docs/latest/authorization_services/index.html)_

#### Instalação **LARAVEL**

- Instalar usando o composer: `composer require cometa/key-cloack`
- Publicar arquivos de configuração:Execulte o senguite codigo no terminal `php artisan vendor:publish --tag=config` isso fará com com o laravel crie o arquivo de configuração em _config/ caso isso não sai como esperado será necessario fazer isso manualmente. Basta copiar \_vendor/cometa-keycloack/config/keyCloack.php_ para _config/_.

- Registrar Middlewares: Em _app/Http/Kenel.php_ adicioner os dois items no array **$routeMiddleware**

```php
   $routerMiddleware = [
       'auth' => Cometa\KeyCloack\Middlewares\Authenticate::class,
       'permission' => Cometa\KeyCloack\Middlewares\Authorization::class
       ...
   ];

```

* Configurar _config/auth.php_: Alterar a key __guards_
~~~php
        'guards' => [
            'api' => [
                    'driver' => 'keycloak',
                    'provider' => 'users',
                ],
        ]
~~~
#### Instalação **LUMEN**

- Instalar usando o composer: `composer require cometa/key-cloack`
- Publicar configurações:
  - Copiar _vendor/cometa-keycloack/config/keyCloack.php_ para _config/_.
  - Copiar _vendor/cometa-keycloack/config/auth.php_ para _config/_., caso o arquivo auth já exista fazer apenas um merger das informações de acordo com sua necessidade, as extrutura as chaves a baixo devem ficar da seguite forma.

~~~php
// config/auth.php
    [
        'defaults' => [
            'guard' => 'api',
            'passwords' => 'users',
        ],

        'guards' => [
            'api' => [
                    'driver' => 'keycloak',
                    'provider' => 'users',
                ],
        ],

        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => User::class
            ]
        ]
    ];

~~~

* Registrar Providers: Adicione a linha em *_bootstrap/app.php_*

```php
$app->register(Cometa\KeyCloack\Providers\KeyCloackServiceProvider::class);
```

- Registrar middlewares **authorization** e **authentication**: adicionar as linhas em _bootstrap/app.php_

```php
$app->routeMiddleware([
   'auth' => Cometa\KeyCloack\Middlewares\Authenticate::class,
   'permission' => Cometa\KeyCloack\Middlewares\Authorization::class
]);

```

#### Usando

Se voce seguiu todas os passos corretamente basta chamar o middleware um sua rota. O middleware **permission** recebe um parametro _route#scoped_, para entender mais sobre contrele de acesso com keyclock acesse _[keycloack](https://www.keycloak.org/)_

```php
$router->get('/keycloack', [
    'uses' => "KeyCloackController@index",
    'middleware' => ['auth', 'permission:users#list-all']
]);

```

#### Exemplos

- Captura o usuario autheticado

```php

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     **/
    Illuminate\Support\Facades\Auth::user();

```

- Verificar se o usuario logado possui um papel

```php

    /**
     * @param  array<App\Model\Role>| Role
     * @return bool
     **/
     Illuminate\Support\Facades\Auth::hasRoles(Role::admin);
     ## OR
     Illuminate\Support\Facades\Auth::hasRoles([Role::admin, Role::gestor]);
```

- Retornar todas as permissoes do usuario logado

```php
    /**
     * @return array
     **/
    Illuminate\Support\Facades\Auth::allPermission();
```

- Retornar todos os papeis

```php
    /**
     * @return array
     **/
    Illuminate\Support\Facades\Auth::getRoles();

```

- Retornar um atributos contido no token

```php
    /**
     * @param string
     * @return mixed
     **/
    Illuminate\Support\Facades\Auth::getAttribute("name");
```
