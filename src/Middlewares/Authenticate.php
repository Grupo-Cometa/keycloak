<?php

namespace GrupoCometa\Keycloak\Middlewares;

use GrupoCometa\Keycloak\Exceptions\KeycloakHttpException;
use GrupoCometa\Keycloak\Exceptions\TokenExpiredException;
use GrupoCometa\Keycloak\Exceptions\TokenNotFoundException;
use GrupoCometa\Keycloak\Exceptions\UserNotFoundException;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if ($this->auth->guard($guard)->check()) {
                return $next($request);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (KeycloakHttpException $e) {
            return response()->json($e->response(), $e->statusCode());
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (TokenNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()],  401);
        }
    }
}