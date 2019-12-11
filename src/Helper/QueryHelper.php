<?php

namespace Yper\SDK\Helper;

class QueryHelper
{
    private $encodedUrl = null;

    /**
     * get the encoded url
     * @param array $args
     */
    function __construct(...$args)
    {
        $formattedArray = array();

        foreach ($args as $key => $arg) {
            foreach ($arg as $name => $value){
                if ($value !== null) {
                    if ($value instanceof \DateTime) {
                        $value = $value->format(\DateTime::ISO8601);
                    }
                    else if (is_bool($value)) {
                        $value = $value ? "true" : "false";
                    }
                    $formattedArray[$name] = $value;
                }
            }
        }
        $this->encodedUrl = "?" . http_build_query($formattedArray);
    }

    /**
     * get the encoded url
     * @return string
     */
    public function getEncodedUrl()
    {
        return $this->encodedUrl;
    }
}