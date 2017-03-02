<?php

require_once "../src/Api.php";
require_once "../src/service/DeliveryOffer.php";
require_once "../src/service/RetailPoint.php";

use Yper\SDK\Api;
use Yper\SDK\Service;

// Informations about your application
$applicationKey = "***REMOVED***";
$applicationSecret = "***REMOVED***";
$retailPointPId = "99015085903";

try {
    // Instanciate API
    $api = new Api($applicationKey, $applicationSecret, 'development');

    // Instanciate retailPointService
    $retailPointService = new Service\RetailPoint($api);

    var_dump($retailPointService->getRetailPointAvailabilityFromAddress("3 square de l'ermitage, 59800 Lille", $retailPointPId));
    var_dump($retailPointService->getRetailPointAvailabilityFromCoordinates("50.650549","3.082126", $retailPointPId));

    // Instanciate deliveryOffer service
    $deliveryOfferService = new Service\DeliveryOffer($api);
    // Returns offer for this location
    var_dump($deliveryOfferService->getOffers(50.6251869, 3.1004944));

} catch(Exception $e) {
     echo "An error occured : " . $e->getMessage();
}

?>
