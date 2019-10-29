<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK;
use Exception;

class YperException extends Exception {

    private $yper_code;
    private $yper_message;

    /**
     * @param string $code codified error
     * @param string $message full (human) message
     * @param int $status HTTP status code (for requests)
     */
    public function __construct($code, $message, $status=null) {

        $this->yper_code = $code;
        $this->yper_message = $message;
        $this->status = $status;

        return parent::__construct($code . " => " . $message);
    }

    public function getAPIErrorCode() {
        return $this->yper_code;
    }

    public function getAPIErrorMessage() {
        return $this->yper_message;
    }

    public function getAPIErrorStatus() {
        return $this->status;
    }

}
