<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/5/1
 * Time: 上午11:26
 */

// 给定日期，选择各州的价格变化状况，并且可以下载。

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php');

$param = parseParam();
//print_r($param);
$startDate = $param[0];
$endDate = $param[1];

$report = new Report();
$report->init($endDate);
$r = $report->getAvgPriceHistoryByState($startDate, $endDate);
//print_r($r);

$sample = new Sample();
$sample->init(ROOT_DIR . 'data/sample_data.csv');
$states = $sample->getStates();

$result = array();
foreach ($states as $state) {
    $tmp = array();
    $tmp['state'] = $state;
    foreach ($r as $inquiryTime => $v) {
        if (isset($r[$inquiryTime][$state])) {
            $tmp[$inquiryTime] = $r[$inquiryTime][$state]['avg_price'];
        } else {
            $tmp[$inquiryTime] = 0;
        }
    }
    $result[] = $tmp;
}

$filename = 'price_' . date('Y-m-d', strtotime($startDate)) . '_' . date('Y-m-d', strtotime($endDate)) . '.csv';
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');

if (!count($result)) {
    exit(0);
}
$header = $result[0];
$fp = fopen('php://output', 'a');
fputcsv($fp, array_keys($header));
foreach ($result as $v) {
    fputcsv($fp, array_values($v));
}


function parseParam()
{
    $result = array();
    if (isset($_REQUEST['start_date'])) {
        $startDate = $_REQUEST['start_date'];
    } else {
        $startDate = date('Y-m-01 00:00:00', strtotime("-3 month"));
    }
    if (isset($_REQUEST['end_date'])) {
        $endDate = $_REQUEST['end_date'];
    } else {
        $endDate = date('Y-m-d H:i:s');
    }
    $result[] = $startDate;
    $result[] = $endDate;
    return $result;
}


