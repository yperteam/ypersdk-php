<?php

require_once "../src/Api.php";
require_once "../src/service/Pro.php";

use Yper\SDK\Api;
use Yper\SDK\Service;

// Informations about your application
$applicationKey = "YOUR_APP_ID";
$applicationSecret = "YOUR_APP_SECRET";

$pro_id = "PRO_ID";
$pro_secret = "PRO_SECRET";

$applicationKey = "8397813756";
$applicationSecret = "P9H326W7ckKF7nswx78B27rgZXWYti";

$pro_id = "48259867A7109";
$pro_secret = "123456789";

try {

    // Instanciate API
    $api = new Api($applicationKey, $applicationSecret, [], 'beta'); // development | beta | production // defaults to : production

    $api->authenticate_pro_secret($pro_id, $pro_secret); // Authenticate with the pro_id and pro_secret ; Available in yper.pro backoffice

    $proService = new Service\Pro($api, $pro_id);

    print_r($proService->get_retailpoints()); // Get pro retailpoints
    print_r($proService->get_wallet()); // Get pro wallet

//    print_r($proService->prebook([
//        "delivery_address" => [
//            "formatted_address" => "121 rue chanzy, 59260 Lille, France", // L'adresse du client en texte
//            "additional_number" => null, // Complément sur le numéro de l'adresse : BIS|TER
//            "additinal" => "Comment about the address", // Commentaire sur l'adresse du client
//        ],
//        "receiver" => [
//            "firstname" => "John", // Prénom du client
//            "lastname" => "Doe", // Nom du client
//            "phone" => "+33612345678", // Téléphone du client
//            "email" => "support@yper.fr" // Adresse email du client
//        ],
//        "retailpoint" => [
//            "partner_id" => "00576" // Identifiant partenaire du magasin (votre identifint)
//        ],
//        "delivery_start" => "2018-01-28 16:00:00.000Z", // Heure de début de livraison
//        "delivery_end" => "2018-01-28 17:00:08.000Z", // Defaults to "delivery_start" + 1 hour
//        "order" => [
//            "order_id" => "123456", // Numéro de commande
//            "options" => ['climb'], // Options sur la commande
//            "transport" => "car", // Taille de la commande
//        ],
//        "extra" => [
//            "nb_items" => 3, // Nombre d'articles
//            "price" => 49.5 // Prix de la commande (obligatoire pour le FRANCO)
//        ],
//        "comment" => "Comment displayed to the shopper" // Commentaire général à propos de la livraison
//    ]));

} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
