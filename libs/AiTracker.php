<?php

/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/5/7
 * Time: 上午9:21
 */
class AiTracker
{
    const url = 'http://tolocal.aitracker.com/setstatevalue';
//    const url = 'http://192.168.99.100/web/setstatevalue.php';
    private $data = array();
    private $errNo;
    private $errMsg;

    public function init($price)
    {
        $this->errMsg = '';
        $this->errNo = 0;
        $this->data = $price;
    }

    public function run($dryRun = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        $response = curl_exec($ch);
//        echo '2222';
        error_log($response);
        $errNo = curl_errno($ch);
        if ($errNo) {
            $this->errNo = $errNo;
            $this->errMsg = curl_error($ch);
        }
        curl_close($ch);
    }

    public function success()
    {
        return !$this->errNo ? true : false;
    }

    public function errorMsg()
    {
        return $this->errMsg;
    }
} 