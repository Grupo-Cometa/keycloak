<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use GrupoCometa\Keycloak\Exceptions\KeycloakHttpException;
use GrupoCometa\Keycloak\Exceptions\TokenExpiredException;
use GrupoCometa\Keycloak\Exceptions\TokenNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $userAuth = $this->auth->guard()->user();
            $response['permissions'] = $this->getPermission();
            $response['roles'] = FacadesAuth::getRoles();

            $user = User::updateOrCreate($userAuth->getAttributes());

            DB::commit();
            return response()->json($user, 201);
        } catch (KeycloakHttpException $e) {
            DB::rollBack();
            return response()->json($e->response(), $e->statusCode());
        } catch (TokenExpiredException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (TokenNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPermission()
    {
        $permission = [];

        foreach (FacadesAuth::allPermission() as $value) {
            $permission[$value->rsname] = @$value->scopes ?: [];
        }

        return $permission;
    }
}