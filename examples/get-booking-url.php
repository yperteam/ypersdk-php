<?php

require_once "../src/Request.php";
require_once "../src/Response.php";
require_once "../src/YperException.php";
require_once "../src/Api.php";
require_once "../src/service/Mission.php";

// Informations about your application
$applicationKey = "***REMOVED***";
$applicationSecret = "***REMOVED***";

try {
    // Instanciate API
    $api = new \Yper\SDK\Api($applicationKey, $applicationSecret, [],'development'); // development | beta | production // defaults to : production

    // Instanciate retailPointService
    $missionService = new Yper\SDK\Service\Mission($api);

    print_r($missionService->getBookingURL([
        "delivery_address" => [
            "formatted_address" => "121 rue chanzy, 59260 Lille, France", // L'adresse du client en texte
            "additional_number" => null, // Complément sur le numéro de l'adresse : BIS|TER
            "additinal" => "Comment about the address", // Commentaire sur l'adresse du client
        ],
        "receiver" => [
            "firstname" => "John", // Prénom du client
            "lastname" => "Doe", // Nom du client
            "phone" => "+33612345678", // Téléphone du client
            "email" => "support@yper.fr" // Adresse email du client
        ],
        "retailpoint" => [
            "partner_id" => "00576" // Identifiant partenaire du magasin (votre identifint)
        ],
        "delivery_start" => "2018-01-28 16:00:00.000Z", // Heure de début de livraison
        "delivery_end" => "2018-01-28 17:00:08.000Z", // Defaults to "delivery_start" + 1 hour
        "order" => [
            "order_id" => "123456", // Numéro de commande
            "options" => ['climb'], // Options sur la commande
            "size" => "car", // Taille de la commande
        ],
        "extra" => [
            "nb_items" => 3, // Nombre d'articles
            "price" => 49.5 // Prix de la commande (obligatoire pour le FRANCO)
        ],
        "comment" => "Comment displayed to the shopper" // Commentaire général à propos de la livraison
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
