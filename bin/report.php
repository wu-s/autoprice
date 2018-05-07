<?php


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.    'autoload.php');

$report = new Report();
$report->init('2018-04-07 12:24:24');
$r1 = $report->getPriceByState();

//send price to aitracker
$rtn = array();
foreach($r1 as $row){
    $rtn[$row['state']] = $row['avg_price'];
}
print_r($rtn);
$aiTracker = new AiTracker();
$aiTracker->init($rtn);
$aiTracker->run();