<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

/**
 * This file contains code about \Yper\SDK\Api class
 */

namespace Yper\SDK;
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

    /**
     * Grand type of connexion
     *
     * @var string
     */
    private $grantType = "client_credentials";

    /**
     *
     * Date to expire token
     *
     **/
    private $expiresAt = "";

    /**
     *
     * Token to access
     *
     **/
    private $accessToken = "";

    /**
     *
     *
     *
     **/
    private $scope = array();

    private $delta = 0;

    private $lastTry = 0;


    private $endPoints = [
        'development' => 'http://localhost:8080/',
        'beta'        => 'https://ws.beta.yper.org/v1.0/',
        'production'  => 'https://ws.yper.fr/v1.0/'
    ];

    private $endPoint = null;

    /**
     * Construct a new wrapper instance
     *
     * @param string $applicationKey    key of your application.
     *
     * @param string $applicationSecret secret of your application.
     *
     *
     * @throws Exceptions\InvalidParameterException if one parameter is missing or with bad value
     */
    public function __construct(
        $applicationKey,
        $applicationSecret,
        $endPoint
    ) {

        if( !$this->_hasCurl()) {
            throw new Exceptions\ApiException("YperSDK need to have curl loaded to work");
        }

        if (!isset($applicationKey) || empty($applicationKey)) {
            throw new Exceptions\InvalidParameterException("Application key parameter is empty");
        }

        if (!isset($applicationSecret) || empty($applicationKey)) {
            throw new Exceptions\InvalidParameterException("Application secret parameter is empty");
        }

        if(!isset($endPoint) || empty($endPoint)) {
            $endPoint = 'development';
        }


        $this->applicationKey    = $applicationKey;
        $this->applicationSecret = $applicationSecret;
        $this->endPoint          = $this->endPoints[$endPoint];

        if(empty($this->accessToken) || $this->expiresAt < (time() - 1)) {
            try {
                $this->_getOAuthToken();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        // Fetching server time and calculating delta
        $returnHour    = $this->get('time');
        $unixTimestamp = $returnHour['unix'];
        $time          = time();
        $this->delta   = $time - $unixTimestamp;
    }

    /**
     *  Test if curl is exist on environment
     *  return boolean
     *
     **/
    private function _hasCurl(){
        return function_exists('curl_version');
    }


    /**
     * Create uniqID from applicationKey, timestamp, and random string
     * @return string
     */
    private function _createUniqId() {
        return $this->applicationKey.time().rand();
    }

    /**
     * @param $method GET,POST..
     * @param $url URL to send
     * @param $timestamp timestamp with delta
     * @return string signature
     *
     */
    private function _createSignature($method, $url, $timestamp) {
        $string = $this->applicationSecret."+".$this->accessToken."+".$method."+".$url."+".$timestamp;

        $signature = "$1$".sha1($string);
        return $signature;
    }

    /**
     * Decode a Response object body to an Array
     *
     * @param  $response
     *
     * @return array
     */
    private function _decodeResponse($response) {
        return json_decode($response, true);
    }

    /**
     * GET requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param null $headers
     * @return array
     * @throws Exception
     */
    public function get($path, $content = null, $headers = null) {

        if(!$content) {
            $content = [];
        }

        $url =  $this->endPoint.$path;

        $content["oauth_timestamp"] = time() - $this->delta;
        $content["oauth_signature"] = $this->_createSignature("GET", $url, $content["oauth_timestamp"]);
        $content["oauth_nonce"]     = $this->_createUniqId();
        $content["oauth_token"]     = $this->accessToken;

        if ($content) {
            $url .= "?" . http_build_query($content);
        }

        // Get cURL ressource
        $curl = curl_init();

        // Setting curl options
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $resp = $this->_decodeResponse($resp);

        // Close request to clear up some resources
        curl_close($curl);

        // $this->debug($resp);
        if(isset($resp['result'])) {
            return $resp['result'];
        }

        throw new Exception( $resp["errorCode"]." ".$resp["errorMessage"]);
    }


    /**
     * POST requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @return array
     * @throws Exception
     */
    public function post($path, $content = null) {
        $content["oauth_nonce"]     = $this->_createUniqId();
        $content["oauth_timestamp"] = time() - $this->delta;
        $content["oauth_token"]     = $this->accessToken;
        $content["oauth_signature"] = $this->_createSignature("POST", $this->endPoint.$path, $content["oauth_timestamp"] );

        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->endPoint.$path,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $content
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($content));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);

        if(!$resp) {
            return false;
        }

        $resp =  $this->_decodeResponse($resp);

        if($resp['status'] != "200") {
            throw new Exception($resp["errorCode"]." => ".$resp["errorMessage"]);
        }

        return $resp;

    }

    /**
     * Authentication, get token
     *
     * @return bool
     * @throws Exception
     */
    private function _getOAuthToken() {

        if($this->lastTry > (time() - 5)) {
            return false;
        }

        $this->lastTry = time();

        $content['app_id'] = $this->applicationKey;
        $content['app_secret'] = $this->applicationSecret;
        $content['grant_type'] = $this->grantType;

        try {
            $return = $this->post("oauth/token", $content);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$return) {
            throw new \Exception("Authentication Failed");
        }

        if ($return) {
            $this->accessToken = $return['result']['accessToken'];
            $expiresIn = $return['result']['expiresIn'];
            $this->expiresAt = time()+$expiresIn;
            $this->scope = $return['result']['expiresIn'];
        }

    }

}

?>
