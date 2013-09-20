<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient(array(
    "api_key" => new TreasureData_API_ConfigResolver_HomeConfigResolver(),
));

if (!isset($_SERVER['argv'][1])) {
    die("php watch_status.php <job_id>\n");
}

$job_id = $_SERVER['argv'][1];
while (true) {
    printf("# Issuing job status api. we wait 10 seconds after issuing api.\n");

    $st = $api->getJobStatus($job_id)->getResult();
    /* @var TreasureData_API_Message_JobStatus $status */
    if ($st->isSuccess()) {
        echo "FINISHED" . PHP_EOL;
        foreach($st->toArray() as $key => $value) {
            printf("%s => %s\n", $key, $value);
        }
        break;
    } else if ($st->isError()) {
        throw new RuntimeException(sprintf("job_id %s returns error", $message->getJobId()));
    } else {
        printf(".");
        sleep(10);
    }
}


