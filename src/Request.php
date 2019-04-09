<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK;

class Request {

    private $method;
    private $url;
    private $needAuthentication = true;

    private $body = null;
    private $headers = array(
        'Accept: application/json',
        'Content-Type: application/json'
    );

    private $curl_options = array(
        CURLOPT_RETURNTRANSFER => 1,
    );

    public function __construct($method, $url)
    {
        $this->method = $method;
        $this->url = $url;
        $this->needAuthentication = true;
        return $this;
    }

    public function disableAuthentication() {
        $this->needAuthentication = false;
    }

    public function enableAuthentication() {
        $this->needAuthentication = true;
    }

    public function authenticationNeeded() {
        return $this->needAuthentication;
    }

    public function addHeader($header, $value)
    {
        $this->headers[] = $header . ': ' . $value;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    private function __prepare_request() {
        // TODO : Maybe stop to handle this ... not very logical
        if ($this->method == 'GET' && $this->body) {
            $this->url .= "?" . http_build_query($this->body);
        }

        $this->curl_options[CURLOPT_URL] = $this->url;
        $this->curl_options[CURLOPT_HTTPHEADER] = $this->headers;

        if ($this->method == 'POST') {
            $this->curl_options[CURLOPT_POST] = 1;
            $this->curl_options[CURLOPT_POSTFIELDS] = json_encode($this->body);
        }

        if ($this->method == 'PUT') {
            $this->curl_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        }

        if ($this->method == 'DELETE') {
            $this->curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }
    }

    public function execute()
    {
        $this->__prepare_request();

        $curl = curl_init();
        curl_setopt_array($curl, $this->curl_options);

        print_r($this->curl_options);
        $resp = curl_exec($curl);

        if (!$resp) {
            throw new YperException("internal_error", "No response from the API service.");
        }

        $response = new Response(
            curl_getinfo($curl, CURLINFO_HTTP_CODE),
            $resp
        );

        curl_close($curl);

        return $response;
    }

}