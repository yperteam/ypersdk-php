<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

namespace {
    const DS = DIRECTORY_SEPARATOR;
    require_once __DIR__ . DIRECTORY_SEPARATOR . "AbstractService.php";
    require_once __DIR__ . DS . ".." . DS . "Helper" . DS . "Validation.php";
}

namespace Yper\SDK\Service {

    class Pro extends AbstractService {

        public function __construct($api) {
            parent::__construct($api);
        }

        private function _check_arg($options, $param, $required = true, $default = null) {

        }

        /**
         * book a mission from a pro account
         */
        public function book($options = []) {
            $params = \Yper\SDK\Helper\Validation::validateParams($options, [
                "deliveryAddress" => [
                    "required" => true
                ],
                "deliveryAddressAdditional" => [
                    "required" => false,
                    "default" => null
                ],
                "deliveryAddressAdditionalNumber" => [
                    "required" => false,
                    "default" => null
                ],
                "firstname" => [
                    "required" => true
                ],
                "lastname" => [
                    "required" => true
                ],
                "phone" => [
                    "required" => true
                ],
                "email" => [
                    "required" => false,
                    "default" => null
                ],
                "retailPointId" => [
                    "required" => true
                ],
                "proId" => [
                    "required" => true
                ],
                "when" => [
                    "required" => true
                ],
                "orderId" => [
                    "required" => true
                ],
                "options" => [
                    "required" => true
                ],
                "size" => [
                    "required" => true
                ],
                "comment" => [
                    "required" => false,
                    "default" => null
                ]
            ]);

            // Transcript params to the right format for the API Call
            $content = [
                "address" => [
                    "formatted_address" => $params["deliveryAddress"],
                    "additional_number" => $params["deliveryAddressAdditionalNumber"],
                    "additional" => $params["deliveryAddressAdditional"]
                ],
                "receiver" => [
                    "firstname" => $params["firstname"],
                    "lastname" => $params["lastname"],
                    "phone" => $params["phone"],
                    "email" => $params["email"]
                ],
                "retailpoint_id" => $params["retailPointId"],
                "pro_id" => $params["proId"],
                "when" => $params["when"],
                "order_id" => $params["orderId"],
                "size" => $params["size"],
                "options" => $params["options"],
                "comment" => $params["comment"],
            ];

            $return = $this->post("pro/order", $content);

            return $return;
        }

    }

}

