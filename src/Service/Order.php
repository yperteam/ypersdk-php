<?php
# Copyright (c) 2013-2019, Yper.
# All rights reserved.

namespace {
    const DS = DIRECTORY_SEPARATOR;
    require_once __DIR__ . DIRECTORY_SEPARATOR . "AbstractService.php";
    require_once __DIR__ . DS . ".." . DS . "Helper" . DS . "Validation.php";
}

namespace Yper\SDK\Service {

    class Order extends AbstractService {

        private $orderId;

        public function __construct($api, $orderId) {
            parent::__construct($api);
            $this->orderId = $orderId;
        }

        /**
         * Add a delivery to this order
         * @param $prebookId
         * @return mixed
         */
        public function add_delivery($prebookId) {
            return $this->post('/order/' . $this->orderId . '/add_items', [
                [
                    "type" => "delivery",
                    "id" => $prebookId
                ]
            ]);
        }

        /**
         * Validate this order (it cannot be edited after)
         * @return mixed
         */
        public function validate() {
            return $this->post('/order/' . $this->orderId . '/validate', (object) null);
        }

        /**
         * Pay this order
         * @return mixed
         */
        public function pay() {
            return $this->post('/order/' . $this->orderId . '/pay', (object) null);
        }
    }
}

