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
    // We need to add the pro:48259867A7109 scope to use this method
    $api = new Api($applicationKey, $applicationSecret, ['pro:48259867A7109'],'development');

    // Instanciate retailPointService
    $proService = new Service\Pro($api);

    print_r($proService->book([
        "delivery_address" => [
            "formatted_address" => "121 rue chanzy, 59260 Lille, France",
            "additional_number" => null, // BIS|TER
            "additinal" => "Comment about the address",
        ],
        "receiver" => [
            "firstname" => "John",
            "lastname" => "Doe",
            "phone" => "+33612345678",
            "email" => "support@yper.fr"
        ],
        "retailpoint" => [
            "id" => "59pe159JSK11848fe9ced73b"
        ],
        "pro" => [
            "id" => "48259867A7109",
        ],
        "delivery_start" => "2018-01-28 16:00:00.000Z",
        "delivery_end" => "2018-01-28 17:00:08.000Z", // Defaults to "delivery_start" + 1 hour
        "order" => [
            "order_id" => "123456",
            "options" => ['climb'],
            "size" => "L",
        ],
        "comment" => "Comment displayed to the shopper"
    ]));

} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
