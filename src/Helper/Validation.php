<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

namespace Yper\SDK\Helper {

    class Validation {

        public static function validateParams($array, $validation) {

            $finalParams = array();

            foreach ($validation as $param => $conditions) {

                if (isset($conditions["required"]) && $conditions["required"]) {
                    if (!isset($array[$param]) || $array[$param] === null) {
                        throw new \Exception("Missing parameter : " .  $param);
                    }
                }

                $finalParams[$param] = $array[$param];

                if (isset($conditions["required"]) && !$conditions["required"]
                    && array_key_exists('default', $conditions) && !isset($array[$param])) {
                    $finalParams[$param] = $conditions["default"];
                }
            }

            return $finalParams;
        }
    }
}

