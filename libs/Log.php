<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午10:11
 */

class Log {
    public static function debug($params){
        //print_r($params);
        echo '[' . date('Y-m-d H:i:s') . '] ' . json_encode($params) . "\n";
    }
} 