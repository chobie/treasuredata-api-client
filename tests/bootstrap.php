<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

define("TD_API_FIXTURE_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR);