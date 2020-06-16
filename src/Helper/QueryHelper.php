<?php

namespace Yper\SDK\Helper;

class QueryHelper
{
    private $encodedUrl = null;

    /**
     * Check if an array is associative (key/value), or is just a list.
     * from https://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     * @param array $arr
     * @return bool
     */
    public static function isAssoc($arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * get the encoded url
     * @param array $args
     */
    function __construct($args)
    {
        $namedParameters = array();
        $listParameters = array();

        foreach ($args as $name => $value) {
            if ($value instanceof \DateTime) {
                $value = $value->format(\DateTime::ISO8601);
                $namedParameters[$name] = $value;
            } else if (is_bool($value)) {
                $value = $value ? "true" : "false";
                $namedParameters[$name] = $value;
            } else if (is_array($value) && !$this->isAssoc($value)) {
                # If currenttly analyzed option is not associative, its because
                # we have a parameter as a list of distinct values
                # requiring a spetial encoding :
                # https://stackoverflow.com/questions/6243051/how-to-pass-an-array-within-a-query-string
                foreach ($value as $key => $item) {
                    array_push($listParameters, $name.'='.urlencode($item));
                }
            } else {
                $namedParameters[$name] = $value;
            }
        }

        if (count($namedParameters) > 0 || count($listParameters) > 0){
            $this->encodedUrl = "?" . http_build_query($namedParameters);
        }

        if (!empty($listParameters)) {
            if (strlen($this->encodedUrl) > 1) {
                # Aka, there is parameters before
                $this->encodedUrl .= "&";
            }
            $this->encodedUrl .= join('&', $listParameters);
        }
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
