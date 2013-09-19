<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "TreasureData", "Autoloader.php"));
date_default_timezone_set("UTC");
TreasureData_Autoloader::register();

$api = TreasureData_APIFactory::createClient(array(
    "api_key" => "<PUT YOUR API KEY HERE(see ~/.td/td.conf)>",
));

$message = $api->issueHiveQuery("testdb", "select v['code'] as code, count(1) as cnt from www_access group by v['code']", 0)
                ->getResult();

/* @var TreasureData_API_Message_IssueHiveJob $message */

$message->getJobId();
printf("# Issuing job_id %s successful\n", $message->getJobId());

echo "# polling job status. this will 30 over seconds...\n";
while (true){
    echo "# Issuing job status api\n";

    $st = $api->getJobStatus($message->getJobId())->getResult();
    /* @var TreasureData_API_Message_JobStatus $status */
    if ($st->isSuccess()) {
        $result = $api->getJobResult($message->getJobId())->getResult();
        break;
    } else if ($st->isError()) {
        throw new RuntimeException(sprintf("job_id %s returns error", $message->getJobId()));
    } else {
        echo ".";
        var_dump($st);
        sleep(10);
    }
}

var_dump($result);
