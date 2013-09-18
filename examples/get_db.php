<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient(array(
    "api_key" => "<PUT YOUR API KEY HERE(see ~/.td/td.conf)>",
));

$result = $api->getDatabaseList();
var_dump($result->getResult());