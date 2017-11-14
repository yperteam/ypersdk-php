<?php

require_once "../src/Api.php";
require_once "../src/service/Mission.php";

// Informations about your application
$applicationKey = "YOUR_APP_ID";
$applicationSecret = "YOUR_APP_SECRET";

try {
    // Instanciate API
    $api = new \Yper\SDK\Api($applicationKey, $applicationSecret, [],'beta'); // development | beta | production // defaults to : production

    // Instanciate retailPointService
    $missionService = new Yper\SDK\Service\Mission($api);

    print_r($missionService->getBookingURL([
        "order" => [
            "orderId" => "100249275",
            "when" => "2017-10-11T13:30:00",
            "nbItems" => 1,
            "price" => 1.19
        ],
        "customer" => [
            "firstname" => "John",
            "lastname" => "Doe",
            "phone" => "0600000000",
            "address" => "121 rue Chanzy, 59260 Lille-Hellemmes"
        ],
        "retailPoint" => [
            "internalId" => "00523"
        ]
    ]));

} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
