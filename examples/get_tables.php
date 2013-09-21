<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient();

if (!isset($_SERVER['argv'][1])) {
    die("php watch_tables.php <dbname>\n");
}

$dbname = $_SERVER['argv'][1];

printf("# Issuing get table list api\n");
$tablelist = $api->getTableList($dbname)->getResult();

foreach ($tablelist->getTables() as $table) {
    /** @var TreasureData_API_Message_Table $table */
    printf("# %s\t%s\t%s\t%s\n",
        $table->getName(),
        $table->getCount(),
        $table->getType(),
        $table->getEstimatedStorageSize()
    );
}
