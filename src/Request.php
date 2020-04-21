<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yper\SDK\Helper\QueryHelper;

class Request
{
    private $method;
    private $url;
    private $needAuthentication = true;

    private $body = null;
    private $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

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

    public function disableAuthentication()
    {
        $this->needAuthentication = false;
    }

    public function enableAuthentication()
    {
        $this->needAuthentication = true;
    }

    public function authenticationNeeded()
    {
        return $this->needAuthentication;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function serializeHeader()
    {
        $ret = [];
        foreach ($this->headers as $key => $header){
            $ret[] = $key . ': ' . $header;
        }

        return $ret;
    }

    private function __prepare_request()
    {
        // TODO : Maybe stop to handle this ... not very logical
        if ($this->method == 'GET' && $this->body) {
            $queryHelper = new QueryHelper($this->body);
            $this->url .= $queryHelper->getEncodedUrl();
        }

        $this->curl_options[CURLOPT_URL] = $this->url;
        $this->curl_options[CURLOPT_HTTPHEADER] = $this->serializeHeader();

        if ($this->method == 'POST') {
            $this->curl_options[CURLOPT_POST] = 1;
            $this->curl_options[CURLOPT_POSTFIELDS] = json_encode($this->body);
        }

        if ($this->method == 'PUT') {
            $this->curl_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $this->curl_options[CURLOPT_POSTFIELDS] = json_encode($this->body);
        }

        if ($this->method == 'DELETE') {
            $this->curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }
    }

    /**
     * Classic request but write the output in a file (path)
     *
     * @param $path
     * @return Response
     * @throws YperException
     */
    public function download($path)
    {
        $this->__prepare_request();

        $curl = curl_init();
        curl_setopt_array($curl, $this->curl_options);
        $data = curl_exec($curl);

        if (!$data) {
            throw new YperException("internal_error", "No response from the API service.");
        }

        $file = fopen($path, "w+");
        fputs($file, $data);
        fclose($file);

        $response = new Response(
            curl_getinfo($curl, CURLINFO_HTTP_CODE),
            null
        );

        curl_close($curl);

        return $response;
    }

    /**
     * Upload a $file in a filed named "value"
     *
     * @param UploadedFile $file
     * @return Response
     * @throws YperException
     */
    public function upload($file)
    {
        $this->__prepare_request();

        $curl = curl_init();
        curl_setopt_array($curl, $this->curl_options);
        curl_setopt($curl, CURLOPT_POST, 1);
        $postData = ['value' => curl_file_create(realpath($file->getPathname()), $file->getClientMimeType(), $file->getClientOriginalName())];
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);

        $data = curl_exec($curl);

        if (!$data) {
            throw new YperException("internal_error", "No response from the API service.");
        }

        $response = new Response(
            curl_getinfo($curl, CURLINFO_HTTP_CODE),
            $data
        );
        curl_close($curl);

        return $response;
    }

    public function execute()
    {
        $this->__prepare_request();

        $curl = curl_init();
        curl_setopt_array($curl, $this->curl_options);
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