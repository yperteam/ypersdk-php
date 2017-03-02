<?php
# Copyright (c) 2013-2017, Yper.
# All rights reserved.

/**
 * This file contains code about \Yper\SDK\Offer class
 */

namespace {
    require_once "AbstractService.php";
}

namespace Yper\SDK\Service {

    class RetailPoint extends AbstractService {

        public function __construct(
            $api
        ) {
            parent::__construct($api);
        }

        private function _getRetailPointAvailability($content) {
            $return = $this->get("retailpoint/availability/", $content);
            return $return['available'];
        }

        /**
         * get retail point availability with an address
         * @param $address string
         * @param $retailPointPid
         * @param null $dateFrom
         */
        public function getRetailPointAvailabilityFromAddress($address, $retailPointPid, $dateFrom = null) {

            if(!$address || !$retailPointPid) {
                throw new Exception("Latitude, longitude or retailPointPid not defined");
            }
            $content['address'] = $address;
            $content['retailpoint_pid'] = $retailPointPid;
            $content['date_from'] = $dateFrom;

            return $this->_getRetailPointAvailability($content);
        }


        /**
         * get retail point availability with coordinates GPS
         * @param $latitude
         * @param $longitude
         * @param $retailPointPid
         * @param null $dateFrom
         * @return mixed
         */
        public function getRetailPointAvailabilityFromCoordinates($latitude, $longitude, $retailPointPid, $dateFrom = null) {

            if(!$latitude || !$longitude || !$retailPointPid) {
                throw new Exception("Latitude, longitude or retailPointPid not defined");
            }
            $content['lat'] = $latitude;
            $content['lng'] = $longitude;
            $content['date_from'] = $dateFrom;
            $content['retailpoint_pid'] = $retailPointPid;


            return $this->_getRetailPointAvailability($content);
        }

    }

}

