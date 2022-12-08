<?php

namespace Cometa\KeyCloack\Exceptions;

use Exception;

class KeycloackHttpException extends Exception {
    private $response;
    private $statusCode;
    public function __construct($response, $statusHttpCode)
    {
        $this->response = $response;
        $this->statusCode = $statusHttpCode;
        parent::__construct($response,$statusHttpCode);
    }

    public function response(){
        return json_decode($this->response);
    }

    public function statusCode(){
        return $this->statusCode;
    }
}
