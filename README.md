# ypersdk

## Install

### With composer (fastest)

Install the latest version with :

```
$ composer require yper/yperapi-sdk-php
```

In your code, include composer autoload :

```php
<?php

require "vendor/autoload.php";
```


### With sources

Download the release you want from :

https://github.com/yperteam/ypersdk-php/releases

Copy the files to your project directory and include the following classes :

```
require_once("<Your Path>/src/Api.php");
require_once("<Your Path>/src/RetailPoint.php");
require_once("<Your Path>/src/DeliveryOffer.php");
```

## Usage (Deprecated)

**This usage is currently deprecated with the latest version of the API. You can check a complete example here : https://github.com/yperteam/ypersdk-php/blob/master/examples/basic.php**


Ce connecteur vous permet de vous connecter de façon simple et sécurisée à l'API Yper.
Tous les retours sont les bienvenus.

Ce dépôt sera régulièrement mis-à-jour et enrichis avec les différents ajouts.

Pour cloner le dépôt : 
```
$ git clone git@github.com:yperteam/ypersdk-php.git
```

Le SDK fourni les méthodes suivantes : 
- `getRetailPointAvailabilityFromAddress(string $address, string $retailPointId)`
- `getRetailPointAvailabilityFromCoordinates($latitude, $longitude, string $retailPointId)`

Pour accéder à l'API, vous devez vous munir des identifiants que nous vous avons fourni : 
- `$applicationKey`
- `$applicationSecret` 

Exemple d'utilisation de l'API
```
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
```

[Exemple complet ici](https://github.com/yperteam/ypersdk-php/blob/master/examples/basic.php)
