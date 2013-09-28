<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient();

if (!isset($_SERVER['argv'][1])) {
    die("php get_job_result.php <job_id>\n");
}

$job_id = $_SERVER['argv'][1];
$result = $api->getJobResult($job_id, "msgpack.gz")->getResult();

var_dump($result);
