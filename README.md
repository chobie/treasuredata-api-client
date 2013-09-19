# Treasure Data API Client

[![Build Status](https://secure.travis-ci.org/chobie/treasuredata-api-client.png)](http://travis-ci.org/chobie/treasuredata-api-client)


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

KEEP IN MIND
-------------------------

Unfortunately, PHP is really poor about processing BIG Data as some reasons.

* PHP function is very slow. (enough to process web services. but big data requires really many function call)
* array implementation (HashTable) does not scale. php will re-alloc memories and iterating Big HashTable is really slow.
* PHP curl implementation returns result as string directly. this will take big memory if job result is large.

  * So, This lib use StreamSocketDriver as default driver. you can also use CurlDriver. but I don't recommend it as above problem.

So. I strongly recommend You process small result (at most under 1 million records) with this lib or downloading job result only.

Anyway, have fun with Treasure Data API and PHP!

LICENSE
--------------------------

Apache License
