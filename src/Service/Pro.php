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

        private $pro_id = null;

        public function __construct($api, $pro_id) {
            parent::__construct($api);
            $this->pro_id = $pro_id;
        }

        public function get_retailpoints() {
            $result = $this->get("pro/" . $this->pro_id . "/retailpoint");
            return $result;
        }

        /**
         * prebook a mission from a pro account
         */
        public function prebook($options = []) {

        }

    }

}

