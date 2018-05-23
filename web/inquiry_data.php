<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/5/1
 * Time: 下午12:01
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