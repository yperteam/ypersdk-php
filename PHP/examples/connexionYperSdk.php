
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
require_once "../src/Exceptions/InvalidParameterException.php";
use YperSdk\Api;
use YperSdk\Exceptions;


// Informations about your application
$applicationKey = $applicationSecret = "***REMOVED***";

try {

    //Instancier Yper
    $objetYper = new Api($applicationKey, $applicationSecret, 'beta');

    $objetYper->getRetailPointAvailability();

} catch(Exception $e) {

     echo "Une Erreur est survenue !! : ".$e->getMessage();

}

?>
</body>
</html>