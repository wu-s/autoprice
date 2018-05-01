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
$result['state'] = $states;
$result['price'] = $r;

$callback = $_REQUEST['callback'];
echo $callback.'('. json_encode($result) .')';

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


