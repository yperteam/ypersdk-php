<?php

require_once "../src/Api.php";
require_once "../src/service/Pro.php";

use Yper\SDK\Api;
use Yper\SDK\Service;

// Informations about your application
$applicationKey = "YOUR_APP_ID";
$applicationSecret = "YOUR_APP_SECRET";

try {

    // Instanciate API
    // We need to add the pro:***REMOVED*** scope to use this method
    $api = new Api($applicationKey, $applicationSecret, ['pro:***REMOVED***'],'development');

    // Instanciate retailPointService
    $proService = new Service\Pro($api);

    print_r($proService->book([
        "deliveryAddress" => "121 rue chanzy, 59000, Lille, France",
        "deliveryAddressAdditionalNumber" => null,
        "deliveryAddressAdditional" => null,
        "firstname" => "Yper",
        "lastname" => "Yper",
        "phone" => "+33972517520",
        "email" => "support@yper.fr",
        "proId" => "***REMOVED***",
        "retailPointId" => "59pe159JSK11848fe9ced73b",
        "when" => "2017-09-18T16:00:00.000Z",
        "orderId" => "YPER-1",
        "options" => [
            "frozen", "fragile", "climb", "heavy", "fresh"
        ],
        "size" => "bike",
        "comment" => "Comment for the shopper"
    ]));


} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
