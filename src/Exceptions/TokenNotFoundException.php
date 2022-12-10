<?php

namespace GrupoCometa\Keycloak\Exceptions;

use Exception;

class TokenNotFoundException extends Exception {

    public function __construct()
    {
        parent::__construct('token not found Authorization: Bearer {token}');
    }
}
