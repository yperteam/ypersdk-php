<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

namespace {
    require_once "AbstractService.php";
}

namespace Yper\SDK\Service {

    // TODO : Old stuff : To Refactor
    class RetailPoint extends AbstractService {

        public function __construct(
            $api
        ) {
            parent::__construct($api);
        }

        private function _getRetailPointAvailability($content) {
            $return = $this->get("retailpoints/availability/", $content);
            return $return['available'];
        }

        /**
         * get retail point availability with an address
         * @param $address string
         * @param $retailPointPid
         */
        public function getRetailPointAvailabilityFromAddress($address, $retailPointPid) {

            if(!$address || !$retailPointPid) {
                throw new Exception("Latitude, longitude or retailPointPid not defined");
            }
            $content['address'] = $address;
            $content['retailpoint_pid'] = $retailPointPid;

            return $this->_getRetailPointAvailability($content);
        }


        /**
         * get retail point availability with coordinates GPS
         * @param $latitude
         * @param $longitude
         * @param $retailPointPid
         * @return mixed
         */
        public function getRetailPointAvailabilityFromCoordinates($latitude, $longitude, $retailPointPid) {

            if(!$latitude || !$longitude || !$retailPointPid) {
                throw new Exception("Latitude, longitude or retailPointPid not defined");
            }
            $content['lat'] = $latitude;
            $content['lng'] = $longitude;
            $content['retailpoint_pid'] = $retailPointPid;


            return $this->_getRetailPointAvailability($content);
        }

    }

}

