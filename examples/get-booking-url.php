<?php

require_once "../src/Api.php";
require_once "../src/service/Mission.php";

// Informations about your application
$applicationKey = "YOUR_APPLICATION_KEY";
$applicationSecret = "YOUR_APPLICATION_SECRET";

try {
    // Instanciate API
    $api = new \Yper\SDK\Api($applicationKey, $applicationSecret, [],'beta'); // development | beta | production // defaults to : production

    // Instanciate retailPointService
    $missionService = new Yper\SDK\Service\Mission($api);

    print_r($missionService->getBookingURL([
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
            "partner_id" => "00576"
        ],
        "delivery_start" => "2018-01-28 16:00:00.000Z",
        "delivery_end" => "2018-01-28 17:00:08.000Z", // Defaults to "delivery_start" + 1 hour
        "order" => [
            "order_id" => "123456",
            "options" => ['climb'],
            "size" => "L",
        ],
        "extra" => [
            "nb_items" => 3,
            "price" => 49.5
        ],
        "comment" => "Comment displayed to the shopper"
    ]));

//  Returns :
//
//    Array
//    (
//        [status] => 200
//        [result] => Array
//        (
//            [mission_id] => mMBx9P6ngjGq7FdH2
//
//            [confirm_url] => Array
//            (
//                [webapp] => https://app.beta.yper.org/#/book/mMBx9P6ngjGq7FdH2
//            )
//
//            [expires_at] => 2017-11-20T15:55:11.192Z
//        )
//
//    )


} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
