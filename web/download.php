<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/5/2
 * Time: 上午7:05
 */

// 数据下载，下载最近一次所有州的post数据，或者指定州的数据。
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php');

$report = new Report();
$date = date('Y-m-d H:i:s');
$report->init($date);

$startDate = date('Y-m-01 00:00:00', strtotime("-3 month"));
$endDate = date('Y-m-d H:i:s');
$inquiryTimes = $report->getInquiryTime($startDate, $endDate);
$lastInquiryTime = array_pop($inquiryTimes);

$sample = new Sample();
$sample->init(ROOT_DIR . 'data/sample_data.csv');
$states = $sample->getStates();
#print_r($states);

$inquiryData = $report->getPriceInquiryHistory($lastInquiryTime, $date);
#print_r($inquiryData);

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=inquiry_history_$lastInquiryTime.csv");
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');

if(!count($inquiryData)){
    exit(0);
}
$header = $inquiryData[0];
$fp = fopen ( 'php://output', 'a' );
fputcsv($fp, array_keys($header));
foreach($inquiryData as $v){
    fputcsv($fp, array_values($v));
}