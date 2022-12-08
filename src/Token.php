<?php

namespace Cometa\KeyCloack;

use Cometa\KeyCloack\Exceptions\TokenExpiredException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use stdClass;

class Token
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function decode($publicKey, $algori = 'RS256'): stdClass
    {
        $publicKey = $this->buildPublicKey($publicKey);

        try {
            JWT::$leeway = 20;
            return JWT::decode($this->token, new Key($publicKey, $algori));
        } catch (ExpiredException $e) {
            throw new TokenExpiredException();
        }

    }

    public function buildPublicKey($publicKey)
    {
        return <<<EOD
       -----BEGIN PUBLIC KEY-----
       {$publicKey}
       -----END PUBLIC KEY-----
       EOD;
    }
}
