<?php

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.    'autoload.php');

run();

function run(){
    $inquiry = new Inquiry();
    $inquiry_time = date('Y-m-d H:i:s', time());
    $csv = ROOT_DIR."data/sample_data.csv";
    $inquiry->init($csv, $inquiry_time);
    $inquiry->run();
}




