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

            throw new \Exception('not_implemented');

//            $params = [
//                "delivery_address" => [
//                    "formatted_address" => "121 rue chanzy, 59260 Lille, France",
//                    "additional_number" => null, // BIS|TER
//                    "additinal" => "Comment about the address",
//                ],
//                "receiver" => [
//                    "firstname" => "John",
//                    "lastname" => "Doe",
//                    "phone" => "+33612345678",
//                    "email" => "support@yper.fr"
//                ],
//                "retailpoint" => [
//                    "partner_id" => "00576"
//                ],
//                "delivery_start" => "2018-01-28 16:00:00.000Z",
//                "delivery_end" => "2018-01-28 17:00:08.000Z",
//                "order" => [
//                    "order_id" => "123456",
//                    "options" => ['climb'],
//                    "size" => "L",
//                ],
//                "comment" => "Comment displayed to the shopper"
//            ];


            // Transcript params to the right format for the API Call
//            $content = [
//                "address" => [
//                    "formatted_address" => $params["deliveryAddress"],
//                    "additional_number" => $params["deliveryAddressAdditionalNumber"],
//                    "additional" => $params["deliveryAddressAdditional"]
//                ],
//                "receiver" => [
//                    "firstname" => $params["firstname"],
//                    "lastname" => $params["lastname"],
//                    "phone" => $params["phone"],
//                    "email" => $params["email"]
//                ],
//                "retailpoint_id" => $params["retailPointId"],
//                "pro_id" => $params["proId"],
//                "when" => $params["when"],
//                "order_id" => $params["orderId"],
//                "size" => $params["size"],
//                "options" => $params["options"],
//                "comment" => $params["comment"],
//            ];
//
//            $return = $this->post("pro/order", $content);

//            return $return;
        }

    }

}

