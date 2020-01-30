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
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new YperException(json_last_error(),
                                    json_last_error_msg(),
                                    500);
        }
    }

    public function getRawResponse() {
        return $this->rawResponse;
    }

    public function getResponse() {
        return $this->parsedResponse;
    }

    public function isSuccess() {
        return ($this->httpCode >= 200 && $this->httpCode < 400);
    }

    public function isFailure() {
        return ($this->httpCode >= 400);
    }

    public function isAuthError() {
        return ($this->httpCode == 403);
    }

    public function getErrorAsException() {
        if ($this->isFailure()
            && isset($this->parsedResponse['error_code'])
            && isset($this->parsedResponse['error_message'])) {
            if ($this->isAuthError()) {
                throw new AuthException($this->parsedResponse['error_code'],
                                        $this->parsedResponse['error_message']);
            }
            throw new YperException($this->parsedResponse['error_code'],
                                    $this->parsedResponse['error_message'],
                                    $this->httpCode);
        }
        throw new YperException('unknown_error', $this->rawResponse, $this->httpCode);
    }

}