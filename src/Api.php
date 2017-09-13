<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

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
        'production'  => 'https://api.yper.io/v1.0/'
    ];

    private $endPoint = null;

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
        $scope = ['global'],
        $endPoint = 'production'
    ) {

        if (!$this->_hasCurl()) {
            throw new Exception("YperSDK need to have curl extension loaded to work");
        }

        if (!isset($applicationKey) || empty($applicationKey)) {
            throw new Exception("Application key parameter is empty");
        }

        if (!isset($applicationSecret) || empty($applicationKey)) {
            throw new Exception("Application secret parameter is empty");
        }

        if (!isset($endPoint) || empty($endPoint)) {
            $endPoint = 'development';
        }

        $this->applicationKey    = $applicationKey;
        $this->applicationSecret = $applicationSecret;
        $this->scope             = $scope;
        $this->endPoint          = $this->endPoints[$endPoint];

        if (empty($this->accessToken) || $this->expiresAt < (time() - 1)) {
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

    private function _add_oauth_data($content, $method, $url) {
        $content["oauth_timestamp"]    = time() - $this->delta;
        $content["oauth_signature"]    = $this->_createSignature("GET", $url, $content["oauth_timestamp"]);
        $content["oauth_nonce"]        = $this->_createUniqId();
        $content["oauth_access_token"] = $this->accessToken;

        return $content;
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

        if (!$content) {
            $content = [];
        }

        $url =  $this->endPoint . $path;

        $content = $this->_add_oauth_data($content, "POST", $url);

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
        if (isset($resp['result'])) {
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

        $url = $this->endPoint . $path;

        $content = $this->_add_oauth_data($content, "POST", $url);

        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($content)
        ));

        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);

        if (!$resp) {
            return false;
        }

        $resp =  $this->_decodeResponse($resp);

        if ($resp['status'] != "200") {
            throw new Exception($resp["error_code"]." => ".$resp["error_message"]);
        }

        return $resp;
    }

    /**
     * Authentication, get token
     *
     * @return bool
     * @throws Exception
     */
    private function  _getOAuthToken() {

        if ($this->lastTry > (time() - 5)) {
            return false;
        }

        $this->lastTry = time();

        $content['app_id'] = $this->applicationKey;
        $content['app_secret'] = $this->applicationSecret;
        $content['grant_type'] = $this->grantType;
        $content['scope'] = $this->scope;

        try {
            $return = $this->post("oauth/token", $content);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$return) {
            throw new \Exception("Authentication Failed");
        }

        $this->accessToken = $return['result']['access_token'];
        $expiresIn = $return['result']['expires_in'];
        $this->expiresAt = time() + $expiresIn;
    }

}

?>
