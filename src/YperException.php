<?php

namespace Yper\SDK;
use Exception;

class YperException extends Exception {

    private $yper_code;
    private $yper_message;

    public function __construct($code, $message) {

        $this->yper_code = $code;
        $this->yper_message = $message;

        return parent::__construct($code . " => " . $message);
    }

    public function getAPIErrorCode() {
        return $this->yper_code;
    }

    public function getAPIErrorMessage() {
        return $this->yper_message;
    }

}
