<?php


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.    'autoload.php');

$report = new Report();
$report->init('2018-04-06 17:48:32');
$r = $report->getAvgPriceHistoryByState();
print_r($r);