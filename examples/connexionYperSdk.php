
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Titre de la page</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>

<?php
require_once"../src/Api.php";

use Yper\SDK\Api;

// Informations about your application
$applicationKey = "APPLICATION_KEY";
$applicationSecret = "APPLICATION_SECRET";
$retailPointPId = "retailPointPid";

try {
    // Instancier Yper
    $objetYper = new Api($applicationKey, $applicationSecret, 'beta');


    //Return bool(true)
    var_dump($objetYper->getRetailPointAvailabilityFromAddress("3 square de l'ermitage, 59800 Lille", $retailPointPId));
    var_dump($objetYper->getRetailPointAvailabilityFromCoordinates("50.650549","3.082126", $retailPointPId));


    //Return bool(false)
    var_dump($objetYper->getRetailPointAvailabilityFromAddress("24bis Rue Basse Mouillère, 45100 Orléans, France", $retailPointPId));
    var_dump($objetYper->getRetailPointAvailabilityFromCoordinates("47.84265762816538","2.0654296875",$retailPointPId));


} catch(Exception $e) {
     echo "Une Erreur est survenue !! : " . $e->getMessage();
}

?>
</body>
</html>
