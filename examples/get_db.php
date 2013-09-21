<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient();

printf("# Issuing getDatabaseList API\n");
$databases = $api->getDatabaseList()->getResult();
foreach ($databases->getDatabases() as $value) {
    /** @var TreasureData_API_Message_Database $value */
    printf("# %s\t%s\t%s\t%s\n",
        $value->getName(),
        $value->getCount(),
        $value->getCreatedAt()->format("Y-m-d H:i:s"),
        $value->getUpdatedAt()->format("Y-m-d H:i:s")
    );
}