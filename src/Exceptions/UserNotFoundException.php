<?php

namespace GrupoCometa\Keycloak\Exceptions;

use Exception;

class UserNotFoundException extends Exception {

    public function __construct()
    {
        parent::__construct('token expired', 401);
    }
}
