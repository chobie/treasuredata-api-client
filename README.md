# Treasure Data API Client

MOTIVATION
--------------------------

There are several implementation but PHP does not have ROBUST Treasure Data REST API client.
This Treasure Data API Client aims robust and provide useful features.

USAGE
--------------------------

composer.json

````
{
  "require": {
      "chobie/treasuredata-api-client": "dev-master"
   }
}
````


Example

````
<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$api = TreasureData_APIFactory::createClient(array(
    "api_key" => "<PUT YOUR API KEY HERE(see ~/.td/td.conf)>",
));

$result = $api->getDatabaseList();
var_dump($result->getResult());
````

LICENSE
--------------------------

Apache License