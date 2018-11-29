<?php

require_once "../src/Api.php";
require_once "../src/YperException.php";
require_once "../src/service/Pro.php";

use Yper\SDK\Api;
use Yper\SDK\Service;

// Informations about your application
$applicationKey = "YOUR_APP_ID";
$applicationSecret = "YOUR_APP_SECRET";

$pro_id = "PRO_ID";
$pro_secret = "PRO_SECRET";

try {

    // Instanciate API
    $api = new Api($applicationKey, $applicationSecret, [], 'beta'); // development | beta | production // defaults to : production

    $api->authenticate_pro_secret($pro_id, $pro_secret); // Authenticate with the pro_id and pro_secret ; Available in yper.pro backoffice

    $proService = new Service\Pro($api, $pro_id);

    $address = "Maubeuge, France";

    $rps = $proService->get_retailpoints();
    print_r($rps); // Get pro retailpoints
    print_r($proService->get_wallet()); // Get pro wallet

    try {
        $res = $proService->post_prebook([
            "delivery_address" => [
                "formatted_address" => $address, // L'adresse du client en texte
                "additional_number" => null, // Complément sur le numéro de l'adresse : BIS|TER
                "additional" => "Comment about the address", // Commentaire sur l'adresse du client
            ],
            "receiver" => [
                "firstname" => "John", // Prénom du client
                "lastname" => "Doe", // Nom du client
                "phone" => "+33612345678", // Téléphone du client
                "email" => "support@yper.fr" // Adresse email du client
            ],
            "retailpoint" => [
                "id" => $rps['result'][0]['_id'] // Identifiant partenaire du magasin (votre identifiant)
            ],
            "mission_template_id" => null, // Type de livraison yper (si non renseigné, prend celui par défaut)
            "delivery_start" => "2019-01-28 16:00:00.000Z", // Heure de début de livraison
            "delivery_end" => "2019-01-28 17:00:08.000Z", // Defaults to "delivery_start" + 1 hour
            "order" => [
                "order_id" => "123456", // Numéro de commande // FACULTATIF : Si non saisi, nous en générons un par défaut
                "options" => ['climb'], // Options sur la commande // FACULTATIF
                "transport" => "car", // Taille de la commande // FACULTATIF (Défaut : foot, moto, bike, car, break)
            ],
            "extra" => [
                "price" => 49.5 // Prix de la commande
            ],
            "comment" => "Comment displayed to the shopper" // Commentaire général à propos de la livraison
        ]);
    } catch (\Yper\SDK\YperException $e) {

        if ($e->getAPIErrorCode() == 'invalid_prebook' && $e->getAPIErrorMessage() == "Delivery address is too far") {
            print_r("DELIVERY ADDRESS IS TOO FAR, CANNOT PREBOOK" . PHP_EOL);
        } else {
            print_r("ERROR ON PREBOOK : OTHER ERROR : " . $e->getMessage() . PHP_EOL);
        }

        die();
    }


    print_r($res);
    $prebook_id = $res['result']['prebook_id'];

    // Get mission template list
    $res = $proService->get_retailpoint_mission_templates($rps['result'][0]["_id"]);
    print_r($res);

    $res = $proService->post_validate_prebook($prebook_id);
    $mission_id = $res['result']['mission_id'];
    print_r($res);

    // Get mission informations
    $mission = $proService->get_mission($mission_id);
    print_r($mission);

    // Return if mission is cancellable and the fee
    // $cancellable = $proService->get_cancel_mission($mission_id);
    // print_r($cancellable);

    // Cancel the mission
    // $res = $proService->post_cancel_mission($mission_id);
    // print_r($res);

} catch(Exception $e) {
    echo "An error occured : " . $e->getMessage();
}

?>
