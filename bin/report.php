<?php


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.    'autoload.php');

$report = new Report();
$report->init('2018-04-06 17:48:32');
$r1 = $report->getPriceByState();
$r2 = $report->getPriceChangeOver50ByState();
print_r($r1);
print_r($r2);