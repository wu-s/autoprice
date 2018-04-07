<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午9:32
 */

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', realpath(__DIR__).'/');
}

date_default_timezone_set('UTC');

$app = array();
$app['config'] = require(ROOT_DIR.'/config/config.php');

$pdo_config = $app['config']['PDO'];

function my_autoload($class){
    $fn = ROOT_DIR."libs/".$class.".php";
    if(file_exists($fn)){
        require_once($fn);
    }
}

spl_autoload_register("my_autoload");

require ROOT_DIR.'vendor/autoload.php';

DB::getInstanse($pdo_config);