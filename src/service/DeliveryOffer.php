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

    class DeliveryOffer extends AbstractService {

        public function __construct(
            $api
        ) {
            parent::__construct($api);
        }

        public function getOffers($lat, $lng, $dateStart = null, $dateEnd = null) {

            $content = [];
            $content['lat'] = $lat;
            $content['lng'] = $lng;

            if (isset($dateStart) && $dateStart) {
                $content['dateStart'] = $dateStart;
            }

            if (isset($dateEnd) && $dateEnd) {
                $content['dateEnd'] = $dateEnd;
            }

            $return = $this->get("offers", $content);

            return $return;
        }
    }

}


