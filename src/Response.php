<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK;

class Response {

    private $httpCode;
    private $rawResponse;
    private $parsedResponse;

    public function __construct($httpCode, $rawResponse) {
        $this->httpCode = $httpCode;
        $this->rawResponse = $rawResponse;
        $this->parsedResponse = json_decode($rawResponse, true);
    }

    public function getRawResponse() {
        return $this->rawResponse;
    }

    public function getResponse() {
        return $this->parsedResponse;
    }

    public function isSuccess() {
        if ($this->httpCode >= 200 && $this->httpCode < 400) {
            return true;
        }
        return false;
    }

    public function isFailure() {
        if ($this->httpCode >= 400) {
            return true;
        }
        return false;
    }

    public function getErrorAsException() {
        if ($this->isFailure()
            && isset($this->parsedResponse['error_code'])
            && isset($this->parsedResponse['error_message'])) {
            throw new YperException($this->parsedResponse['error_code'], $this->parsedResponse['error_message']);
        }
        throw new YperException('unknown_error', "Unhandled error");
    }

}