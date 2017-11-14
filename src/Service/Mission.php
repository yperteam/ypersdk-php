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
        public function getBookingURL($options = []) {

            $params = \Yper\SDK\Helper\Validation::validateParams($options, [

            ]);

            // TODO : Return dynamic URL here
            return [
                "missionId" => "abcdefgh",
                "bookingURL" => "https://app.yper.fr",
                "expiresAt" => "2017-11-14T09:09:58.045Z"
            ];
        }

    }

}

