<?php

namespace GrupoCometa\Keycloak\Exceptions;

use Exception;

class KeycloakHttpException extends Exception {

    private $response;
    private $statusCode;

    public function __construct($response, $statusHttpCode)
    {
        $this->response = $response;
        $this->statusCode = $statusHttpCode;
        parent::__construct($response->error_description,$statusHttpCode);
    }

    public function response()
    {
        return $this->response;
    }

    public function statusCode()
    {
        return $this->statusCode;
    }
}
