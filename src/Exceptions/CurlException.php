<?php

namespace Cometa\KeyCloack\Exceptions;

use Exception;

class CurlException extends Exception {

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

}
