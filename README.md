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
require_once("<Your Path>/src/Request.php");
require_once("<Your Path>/src/Response.php");
require_once("<Your Path>/src/YperException.php");
require_once("<Your Path>/src/Api.php");
require_once("<Your Path>/src/Service/<SERVICE_TO_INCLUDE>.php");

```

## Versioning

Please never include our SDK using the `dev-master` tag

Always make sure to include a specific version of the SDK

We're using semantic versionning, please take this in consideration 

More infos on : https://semver.org/