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

        public function get_wallet() {
            $result = $this->get("pro/" . $this->pro_id . "/wallet");
            return $result;
        }

        /**
         * prebook a mission from a pro account
         */
        public function post_prebook($options = []) {
            $result = $this->post("pro/" . $this->pro_id . "/prebook", $options);
            return $result;
        }

        public function post_validate_prebook($prebook_id) {
            $result = $this->post("pro/" . $this->pro_id . "/prebook/" . $prebook_id . "/validate", null);
            return $result;
        }

        public function get_mission($mission_id) {
            $result = $this->get("pro/" . $this->pro_id . "/mission/" . $mission_id);
            return $result;
        }

        public function get_cancel_mission($mission_id) {
            $result = $this->get("pro/" . $this->pro_id . "/mission/" . $mission_id . "/cancel");
            return $result;
        }

        public function post_cancel_mission($mission_id) {
            $result = $this->post("pro/" . $this->pro_id . "/mission/" . $mission_id . "/cancel");
            return $result;
        }

    }

}

