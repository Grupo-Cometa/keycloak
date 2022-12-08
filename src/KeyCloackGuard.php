<?php

namespace Cometa\KeyCloack;


use App\Models\User;
use Cometa\KeyCloack\Exceptions\TokenNotFoundException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class KeyCloackGuard implements Guard
{
    private $config;
    private $user = null;
    private UserProvider $provider;
    private Token $token;
    private $decodedToken;
    private Request $request;
    private array $attributeUser = [];

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->config = config('keyCloack');
        $this->provider = $provider;
        $this->request = $request;
        $this->setDecodedToken();
        $this->initUser();
    }

    private function setDecodedToken()
    {
        $this->decodedToken = $this->decodedToken($this->getBearerToken());
    }

    private function initUser()
    {
        foreach ($this->config['bind_user_key_cloack'] as $key => $value) {
            $this->attributeUser[$key] = $this->decodedToken->$value;
        }

        $classUser =  $this->provider->getModel();
        $user = new $classUser($this->attributeUser);
        $this->setUser($user);
    }

    private function getBearerToken()
    {
        $inputKey = $this->config['input_key'] ?? "";
        $token = $this->request->bearerToken() ?? $this->request->input($inputKey);
        if ($token) return $token;
        throw new TokenNotFoundException();
    }

    private function decodedToken($token)
    {
        $this->token = new Token($token);
        return $this->token->decode($this->config['reaml_public_key'], $this->config['signature_algorithm']);
    }

    private function getCredentials()
    {
        return [
            $this->config['user_provider_credential'] => $this->decodedToken->{$this->config['token_principal_attribute']}
        ];
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function check()
    {
        return $this->validate($this->getCredentials());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user()->id;
    }

    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        if ($user) {
            $this->setUser($user);
            return true;
        }

        return false;
    }

    public function can($permission)
    {
        $data = [
            'permission' => $permission,
            'audience' => $this->config['client_id'],
            'grant_type' => 'urn:ietf:params:oauth:grant-type:uma-ticket'
        ];

        $header = [
            "Authorization: Bearer {$this->getBearerToken()}",
            'Content-Type: application/x-www-form-urlencoded'
        ];

        return !!Http::post($this->decodedToken->iss . "/protocol/openid-connect/token", $data, $header);
    }

    public function hasRoles($roles): bool
    {
        $rolesUserAuth = $this->decodedToken->resource_access->{$this->config['client_id']}->roles;
        if (!is_array($roles)) return in_array($roles, $rolesUserAuth);

        foreach ($roles as $role) {
            return  in_array($role, $rolesUserAuth);
        }
    }

    public function getRoles()
    {
        return $this->decodedToken->resource_access->{$this->config['client_id']}->roles;
    }

    public function getAttribute(string $attribute)
    {
        return @$this->decodedToken->$attribute ?: false;
    }

    public function allPermission()
    {
        $data = [
            'audience' => 'cometa-leitura',
            'grant_type' => 'urn:ietf:params:oauth:grant-type:uma-ticket'
        ];

        $header = [
            "Authorization: Bearer {$this->getBearerToken()}",
            'Content-Type: application/x-www-form-urlencoded'
        ];

        $data =  Http::post($this->decodedToken->iss . "/protocol/openid-connect/token", $data, $header);

        $decode = $this->decodedToken($data->access_token);

        return @$decode->authorization->permissions ?: [];
    }
}
