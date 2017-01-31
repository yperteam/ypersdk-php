<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.
/**
 * This file contains code about \ypersdk\Api class
 */

namespace YperSdk;
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
    'development' => 'https://sandbox-ws.yper.org/',
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

       if( !$this->_isCurl()) {
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



       if(empty($this->accessToken) || $this->expiresAt < (time() -1)) {
           try {
               $this->oAuth();
           } catch (Exception $e) {
               throw new Exception($e->getMessage());
           }
       }


       $returnHour = $this->get('time');
       $unixTimestamp = $returnHour['unix'];

       $time = time();

       $this->delta = $time - $unixTimestamp;
   }

   private function oAuth() {

       if($this->lastTry >  (time() - 5)) {
            return false;
       }

       $this->lastTry = time();


       $content['app_id'] = $this->applicationKey;
       $content['app_secret'] = $this->applicationSecret;
       $content['grant_type'] = "client_credentials";

       try {
           $return = $this->post("oauth/token", $content);
       } catch(Exception $e) {
           throw new Exception($e->getMessage());
       }

       if (!$return) {
           throw new \Exception("Authentification Failed");
       }

       if ($return) {
           $this->accessToken = $return['result']['accessToken'];
           $expiresIn = $return['result']['expiresIn'];
           $this->expiresAt = time()+$expiresIn;
           $this->scope = $return['result']['expiresIn'];
       }

   }

   public function getRetailPointAvailability() {
       $content['oauth_token'] = $this->accessToken;

       $return = $this->get("retailpoint/availability/", $content );

       if($return !== false) {
           echo "DISPONIBILITE OK ! <br /> <br />";
       }
   }

   /**
   *  Test if curl is exist on environment
   *
   **/
   private function _isCurl(){
       return function_exists('curl_version');
   }


   private function _createUniqId() {
     return $this->applicationKey.time().rand();
   }

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
    private function decodeResponse($response)
    {
        return json_decode($response, true);
    }

    /**
     * GET requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     *
     * @return array
     * @throws Exception\ClientException if http request is an error
     */


    private function get($path, $content = null, $headers = null)
    {


        $url =  $this->endPoint.$path;

        $content["oauth_timestamp"] = time() - $this->delta;

        $content["oauth_signature"] = $this->_createSignature("GET", $url, $content["oauth_timestamp"]);


       /* return $this->decodeResponse(
            $this->rawCall("GET", $path, $content, true, $headers)
        );*/
       $content["oauth_nonce"] = $this->_createUniqId();

       $content["oauth_timestamp"] = time() - $this->delta;
       $content["oauth_token"] = $this->accessToken;


        if($content) {
            $url .="?".http_build_query($content);
        }

        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $resp = $this->decodeResponse($resp);

        // Close request to clear up some resources
        curl_close($curl);

        return $resp['result'];


    }


    /**
     * POST requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     *
     * @return array
     **/
    private function post($path, $content = null, $headers = null)
    {
        $content["oauth_nonce"] = $this->_createUniqId();
        $content["oauth_timestamp"] = time() - $this->delta;
        $content["oauth_token"] = $this->accessToken;
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

        $resp =  $this->decodeResponse($resp);

        if($resp['status'] != "200") {
            throw new Exception("Something wrong in authentification");
        }

        return $resp;

    }


    public function debug($var) {
        echo"<pre>";
        var_dump($var);
        echo"</pre> <br />";
    }
}

?>