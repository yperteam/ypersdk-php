<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

namespace Yper\SDK;
use Yper\SDK\YperException;

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

    private $endPoints = [
        'development' => 'http://localhost:5000/',
        'beta'        => 'https://io.beta.yper.org/',
        'production'  => 'https://api.yper.io/'
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

        if (!isset($applicationSecret) || empty($applicationSecret)) {
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
                throw $e;
            }
        }

        // Fetching server time and calculating delta
        $returnHour    = $this->get('time');

        $returnHour    = $returnHour['result'];

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
        return sha1($this->applicationKey . time() . rand());
    }

    /**
     * @param $method GET,POST..
     * @param $url URL to send
     * @param $timestamp timestamp with delta
     * @return string signature
     *
     */
    private function _createSignature($method, $url, $timestamp) {
        $string = $this->applicationSecret . "+" . $this->accessToken . "+" . $method . "+" . $url . "+" . $timestamp;
        $signature = "$1$" . sha1($string);

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
        $content["oauth_signature"]    = $this->_createSignature($method, $url, $content["oauth_timestamp"]);
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

        $curl = curl_init();

        // Setting curl options
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        $resp = curl_exec($curl);

        curl_close($curl);

        if (!$resp) {
            throw new Exception( "Invalid response from the API service");
        }

        $resp = $this->_decodeResponse($resp);

        if (isset($resp['status']) && $resp['status'] == 200 && isset($resp['result'])) {
            return $resp;
        } else if (isset($resp['status']) && $resp['status'] != 200 && isset($resp['error_code']) && isset($resp['error_message'])) {
            throw new YperException($resp["error_code"], $resp["error_message"]);
        }

        throw new Exception( "Invalid response from the API service");
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

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($content)
        ));

        $resp = curl_exec($curl);

        curl_close($curl);

        if (!$resp) {
            throw new Exception( "Invalid response from the API service");
        }

        $resp = $this->_decodeResponse($resp);

        if (isset($resp['status']) && $resp['status'] == 200 && isset($resp['result'])) {
            return $resp;
        } else if (isset($resp['status']) && $resp['status'] != 200 && isset($resp['error_code']) && isset($resp['error_message'])) {
            throw new YperException($resp["error_code"], $resp["error_message"]);
        }

        throw new Exception( "Invalid response from the API service");
    }

    public function authenticate_pro_secret($pro_id, $pro_secret_token) {
        $this->_getOAuthToken("pro_secret_token", [
            "pro_id" => $pro_id,
            "pro_secret_token" => $pro_secret_token
        ]);
    }

    /**
     * Authentication, get token
     *
     * @return bool
     * @throws Exception
     */
    private function  _getOAuthToken($grant_type = "client_credentials", $params = []) {
        $content = $params;

        $content['app_id'] = $this->applicationKey;
        $content['app_secret'] = $this->applicationSecret;

        $content['grant_type'] = $grant_type;
        $content['scope'] = $this->scope;

        try {
            $return = $this->post("oauth/token", $content);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$return) {
            throw new \Exception("Authentication Failed");
        }

        $return = $return['result'];

        $this->accessToken = $return['access_token'];
        $expiresIn = $return['expires_in'];
        $this->expiresAt = time() + $expiresIn;
    }

}

?>
