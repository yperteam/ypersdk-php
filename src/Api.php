<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace Yper\SDK;

use Yper\SDK\YperException;
use Exception;

class Api {

    /**
     * Contain key of the current application
     *
     * @var string
     */
    private $applicationKey = null;

    /**
     * Contain secret of the current application
     *
     * @var string
     */
    private $applicationSecret = null;

    private $accessToken = null;
    private $refreshToken = null;
    private $expiresAt = null;
    private $scope = array();
    private $delta = 0;

    private $endpoints = array(
        'development' => 'http://localhost:5000',
        'beta'        => 'https://io.beta.yper.org',
        'production'  => 'https://api.yper.io'
    );

    private $environment = null;
    private $baseURL = null;

    /**
     * Construct a new wrapper instance
     *
     * @param string $applicationKey key of your application.
     *
     * @param string $applicationSecret secret of your application.
     * @param string $endPoint
     * @throws Exception
     */
    public function __construct(
        $applicationKey,
        $applicationSecret,
        $scope = array('global'),
        $environment = 'production'
    ) {

        if (!$this->__has_curl()) {
            throw new Exception("YperSDK need to have curl extension loaded to work");
        }

        if (!isset($applicationKey) || empty($applicationKey)) {
            throw new Exception("Application key parameter is empty");
        }

        if (!isset($applicationSecret) || empty($applicationSecret)) {
            throw new Exception("Application secret parameter is empty");
        }

        $this->applicationKey    = $applicationKey;
        $this->applicationSecret = $applicationSecret;
        $this->scope             = $scope;
        $this->environment       = $environment;
        $this->baseURL           = $this->endpoints[$environment];

        // Fetching server time and calculating delta
        $returnHour    = $this->get('time', null, array('need_authentication' => false));
        $returnHour    = $returnHour['result'];
        $unixTimestamp = $returnHour['unix'];
        $time          = time();
        $this->delta   = $time - $unixTimestamp;

    }

    public function setOAuthToken($oauthToken, $expiresAt, $refreshToken) {
        $this->accessToken = $oauthToken;
        $this->expiresAt = $expiresAt;
        $this->refreshToken = $refreshToken;
    }

    public function refreshToken($refresh_token) {
        $token = $this->__getOAuthToken("refresh_token", array(
            "refresh_token" => $refresh_token
        ));

        $this->setOAuthToken(
            $token['access_token'],
            time() + $token['expires_in'],
            (isset($token['refresh_token']) ? $token['refresh_token'] : null)
        );

        return $token;
    }

    public function authenticate_pro_secret($pro_id, $pro_secret_token) {
        $token = $this->__getOAuthToken("pro_secret_token", array(
            "pro_id" => $pro_id,
            "pro_secret_token" => $pro_secret_token
        ));

        $this->setOAuthToken(
            $token['access_token'],
            time() + $token['expires_in'],
            (isset($token['refresh_token']) ? $token['refresh_token'] : null)
        );

        return $token;
    }

    /**
     * Authentication, get token
     *
     * @return bool
     * @throws Exception
     */
    private function  __getOAuthToken($grant_type = "client_credentials", $params = array()) {
        $content = $params;

        $content['client_id'] = $this->applicationKey;
        $content['client_secret'] = $this->applicationSecret;
        $content['grant_type'] = $grant_type;
        $content['scope'] = $this->scope;

        try {
            $return = $this->post("oauth/token", $content, array('need_authentication' => false));
        } catch(Exception $e) {
            throw new YperException('authentication_failed', $e->getMessage());
        }

        return $return['result'];
    }


    /**
     *  Test if curl is exist on environment
     *  return boolean
     *
     **/
    private function __has_curl(){
        return function_exists('curl_version');
    }

    /**
     * Check if we need an access token for this request and generate/renew one if needed
     *
     * @throws Exception
     */
    private function __authenticate_request() {
        if (!$this->accessToken || $this->expiresAt < (time() - 1)) {
            $token = $this->__getOAuthToken();
            $this->setOAuthToken(
                $token['access_token'],
                time() + $token['expires_in'],
                (isset($token['refresh_token']) ? $token['refresh_token'] : null)
            );
        }
    }

    /**
     * Prepare a request by embedding authentication, body & headers in it
     *
     * @param Request $request Request to prepare
     * @param string $content Body of the request
     * @param array $options Options enabled for this request
     *
     * @throws Exception
     */
    private function __prepare_request(Request $request, $content, array $options = array()) {
        if (isset($options['need_authentication']) && !$options['need_authentication']) {
            $request->disableAuthentication();
        }

        if ($request->authenticationNeeded()) {
            $this->__authenticate_request();
        }

        $request->addHeader('Authorization', "Bearer " . $this->accessToken);
        $request->addHeader('X-Request-Timestamp', time() - $this->delta);
        $request->setBody($content);

        if (isset($options['headers']) && is_array($options['headers'])) {
            foreach ($options['headers'] as $key => $header) {
                $request->addHeader($key, $header);
            }
        }
    }

    /**
     * GET requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param array $options Associative array of options for this request
     * @return array
     * @throws Exception
     */
    public function get($path, $content = null, array $options = array()) {
        $url =  $this->baseURL . $path;
        $req = new Request('GET', $url);
        $this->__prepare_request($req, $content, $options);
        $response = $req->execute();

        if ($response->isSuccess()) {
            return $response->getResponse();
        } else {
            $response->getErrorAsException();
        }
    }

    /**
     * POST requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param array $options Associative of options for this request
     * @return array
     * @throws Exception
     */
    public function post($path, $content = null, array $options = array()) {
        $url =  $this->baseURL . $path;
        $req = new Request('POST', $url);
        $this->__prepare_request($req, $content, $options);
        $response = $req->execute();

        if ($response->isSuccess()) {
            return $response->getResponse();
        } else {
            $response->getErrorAsException();
        }
    }

    /**
     * PUT requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param array $options Associative of options for this request
     * @return array
     * @throws Exception
     */
    public function put($path, $content = null, array $options = array()) {
        $url =  $this->baseURL . $path;
        $req = new Request('PUT', $url);
        $this->__prepare_request($req, $content, $options);
        $response = $req->execute();

        if ($response->isSuccess()) {
            return $response->getResponse();
        } else {
            $response->getErrorAsException();
        }
    }

    /**
     * DELETE requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param array $options Associative of options for this request
     * @return array
     * @throws Exception
     */
    public function delete($path, $content = null, array $options = array()) {
        $url =  $this->baseURL . $path;
        $req = new Request('DELETE', $url);
        $this->__prepare_request($req, $content, $options);
        $response = $req->execute();

        if ($response->isSuccess()) {
            return $response->getResponse();
        } else {
            $response->getErrorAsException();
        }
    }
}