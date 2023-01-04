#### Cometa Keycloak

Uma simples biblioteca para "authentication/authorization" no sso _[keycloak](https://www.keycloak.org/)_ utilizando o protocolo **openid-connect**.

A **authorization** funciona apenas para permissões baseadas em _escopos_ ou permissões baseadas em _recursos_, para saber mais acesse _[Keycloak Authorization Services](https://www.keycloak.org/docs/latest/authorization_services/index.html)_

#### Instalação **LARAVEL**

- Instalar usando o composer: `composer require grupo-cometa/keycloak`
- Publicar arquivos de configuração:Execute o seguinte código no terminal `php artisan vendor:publish --tag=config` isso fará com que o laravel crie o arquivo de configuração em _config/ caso isso não saia como esperado será necessario fazer isso manualmente. Basta copiar \_vendor/cometa-keycloack/config/keyCloack.php_ para _config/_.

- Registrar Middlewares: Em _app/Http/Kenel.php_ adicionar os dois items no array **$routeMiddleware**

```php
   $routerMiddleware = [
       'auth' => GrupoCometa\Keycloak\Middlewares\Authenticate::class,
       'permission' => GrupoCometa\Keycloak\Middlewares\Authorization::class
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

- Instalar usando o composer: `composer require grupo-cometa/keycloak`
- Publicar configurações:
  - Copiar _vendor/grupo-cometa/keycloak/config/keycloak.php_ para _config/_.
  - Copiar _vendor/grupo-cometa/keycloak/config/auth.php_ para _config/_., caso o arquivo auth já exista fazer apenas um merge das informações de acordo com sua necessidade, as extrutura e as chaves a baixo devem ficar da seguite forma.

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

* Registre as Variáveis de Ambiente: Adicione as variáveis **realm_public_key** e **signature_algorithm** no *.env*

* Altere o Model de Usuário: Copie _vendor/grupo-cometa/keycloak/config/User.php_ para _app/Models/User.php_.

* Registre o Controller de Autenticação: Copie _vendor/grupo-cometa/keycloak/config/User.php_ para _app/Models/User.php_.

* Registre a rota para cadastro de usuários:

```php
$router->post('/', [
    'uses' => 'AuthController@store'
]);
```

* Registrar Providers: Adicione a linha em *_bootstrap/app.php_*

```php
$app->register(GrupoCometa\Keycloak\Providers\KeycloakServiceProvider::class);
```

- Registrar middlewares **authorization** e **authentication**: adicionar as linhas em _bootstrap/app.php_

```php
$app->routeMiddleware([
   'auth' => GrupoCometa\Keycloak\Middlewares\Authenticate::class,
   'permission' => GrupoCometa\Keycloak\Middlewares\Authorization::class
]);

```

#### Usando

Se voce seguiu todas os passos corretamente basta chamar o middleware um sua rota. O middleware **permission** recebe um parametro _route#scoped_, para entender mais sobre o controle de acesso com keycloak acesse _[Keycloak](https://www.keycloak.org/)_

```php
$router->get('/keycloak', [
    'uses' => "KeycloakController@index",
    'middleware' => ['auth', 'permission:users#list-all']
]);

```

#### Exemplos

- Captura o usuario autenticado

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
