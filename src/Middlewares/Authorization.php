<?php

namespace GrupoCometa\Keycloak\Middlewares;

use GrupoCometa\Keycloak\Exceptions\KeycloakHttpException;
use GrupoCometa\Keycloak\Exceptions\TokenExpiredException;
use GrupoCometa\Keycloak\Exceptions\TokenNotFoundException;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;

class Authorization
{
    public function handle($request, Closure $next, $guard)
    {
        try {
            if (Auth::hasRoles("admin")) return $next($request);

            Auth::can($guard);

            return $next($request);

        } catch (KeycloakHttpException $e) {
            return response()->json($e->response(), $e->statusCode());
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (TokenNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => "Server error"], 500);
        }
    }
}
