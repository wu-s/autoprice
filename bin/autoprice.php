<?php

set_time_limit(0);

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.    'autoload.php');

run();

function run(){
    $inquiry_time = date('Y-m-d H:i:s', time());

    $inquiry = new Inquiry();
    $csv = ROOT_DIR."data/sample_data.csv";
    $inquiry->init($csv, $inquiry_time);
    $inquiry->run();

//    $inquiry_time = '2018-04-07 12:24:24';
    $report = new Report();
    $report->init($inquiry_time);
    $r1 = $report->getPriceByState();
    $r2 = $report->getPriceChangeOver50ByState();
    $f1 = ROOT_DIR.'tmp/report_state.csv';
    $f2 = ROOT_DIR.'tmp/report_price_change_over_50_percents.csv';
    writeCsv($r1, $f1);
    writeCsv($r2, $f2);

    $transport = (new Swift_SmtpTransport('smtp.qq.com', 465))
        ->setUsername('214190413@qq.com')
        ->setPassword('Snail@#0405^&')
        ->setEncryption('ssl');
    $mailer = new Swift_Mailer($transport);
    $message = (new Swift_Message('autoprice report'))
        ->setFrom(['214190413@qq.com' => 'John Doe'])
        ->setTo(['2205935650@qq.com', 'keater@gmail.com', ])
        ->setBody('Here is the message itself')
    ;
    if($r1){
        $attachment = Swift_Attachment::fromPath($f1)->setFilename('report_state.csv');
        $message->attach($attachment);
    }
    if($r2){
        $attachment = Swift_Attachment::fromPath($f2)->setFilename('report_price_change_over_50_percents.csv');
        $message->attach($attachment);
    }

    $mailer->send($message);

    //send price to aitracker
    $rtn = array();
    foreach($r1 as $row){
        $rtn[$row['state']] = $row['avg_price'];
    }
//    print_r($rtn);
    $aiTracker = new AiTracker();
    $aiTracker->init($rtn);
    $aiTracker->run();
}

function writeCsv($data, $fn){
    $fh = fopen($fn, 'w');
    if(!$data){
        fclose($fh);
        return;
    }
//    print_r($data);
    $cols = array_keys($data[0]);
    fputcsv($fh, $cols);
    foreach($data as $row){
        fputcsv($fh, array_values($row));
    }
    fclose($fh);
}




