<?php
# Copyright (c) 2013-2019, Yper.
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

        /**
         * Get a list of retailpoints
         * @return mixed
         */
        public function get_retailpoints() {
            return $this->get("/pro/" . $this->pro_id . "/retailpoint");
        }

        /**
         * Create a new order for this pro
         * @return string The orderId
         */
        public function create_order() {
            $result = $this->post("/order", ["pro_id" => $this->pro_id]);
            return $result['result']['_id'];
        }

        /**
         * Get a list of possible MissionTemplates for a specific retailpoint
         * @param $retailpoint_id
         * @return mixed
         */
        public function get_retailpoint_mission_templates($retailpoint_id) {
            return $this->get("/pro/" . $this->pro_id . "/retailpoint/" . $retailpoint_id . "/mission_template");
        }

        /**
         * (DEPRECATED) Get available amount in the pro wallet
         * @deprecated
         * @return mixed
         */
        public function get_wallet() {
            return $this->get("/pro/" . $this->pro_id . "/wallet");
        }

        /**
         * prebook a mission from a pro account
         */
        public function post_prebook($options = []) {
            return $this->post("/pro/" . $this->pro_id . "/prebook", $options);
        }

        /**
         * (DEPRECATED) Validate a prebook and pay it
         * @deprecated This method is deprecated in favor of the "Order" system
         * @param $prebook_id
         * @return mixed
         */
        public function post_validate_prebook($prebook_id) {
            return $this->post("/pro/" . $this->pro_id . "/prebook/" . $prebook_id . "/validate", null);
        }

        /**
         * Get informations about a delivery
         * @param $delivery_id
         * @return mixed
         */
        public function get_delivery($delivery_id) {
            return $this->get("/pro/" . $this->pro_id . "/mission/" . $delivery_id);
        }

        /**
         * Get informations about a mission
         * @deprecated Use get_delivery instead
         * @param $mission_id
         * @return mixed
         */
        public function get_mission($mission_id) {
            return $this->get_delivery($mission_id);
        }

        /**
         * Get informations about a mission cancelation (possible & fees associated)
         * @deprecated Use get_cancel_delivery instead
         * @param $mission_id
         * @return mixed
         */
        public function get_cancel_mission($mission_id) {
            return $this->get_cancel_delivery($mission_id);
        }

        /**
         * Get informations about a delivery cancelation (possible & fees associated)
         * @param $delivery_id
         * @return mixed
         */
        public function get_cancel_delivery($delivery_id) {
            return $this->get("/pro/" . $this->pro_id . "/mission/" . $delivery_id . "/cancel");
        }

        /**
         * Validate a mission cancelation
         * @deprecated Use post_cancel_delivery instead
         * @param $mission_id
         * @return mixed
         */
        public function post_cancel_mission($mission_id) {
            return $this->post_cancel_delivery($mission_id);
        }

        /**
         * Validate a delivery cancelation
         * @param $delivery_id
         * @return mixed
         */
        public function post_cancel_delivery($delivery_id) {
            return $this->post("/pro/" . $this->pro_id . "/mission/" . $delivery_id . "/cancel", (object)[]);
        }
    }
}
