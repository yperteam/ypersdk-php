<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

namespace {
    const DS = DIRECTORY_SEPARATOR;
    require_once __DIR__ . DIRECTORY_SEPARATOR . "AbstractService.php";
    require_once __DIR__ . DS . ".." . DS . "Helper" . DS . "Validation.php";
}

namespace Yper\SDK\Service {

    class Mission extends AbstractService {

        public function __construct($api) {
            parent::__construct($api);
        }

        /**
         * book a mission from a pro account
         */
        public function getBookingURL($params = []) {

//            $params = \Yper\SDK\Helper\Validation::validateParams($options, [
//
//            ]);

            $return = $this->post("mission/prebook", $params);

            if (isset($return['status']) && $return['status'] == 200 && isset($return['result'])) {
                return $return;
            } else {
                throw new \Exception('Invalid response from prebooking service');
            }
        }

    }

}

