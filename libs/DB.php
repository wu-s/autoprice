<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午11:46
 */

class DB {
    private static $instance;

    public static function getInstanse($pdo_config = array()){
        if(!self::$instance){
            self::$instance = new PDO('mysql:host='.$pdo_config['database_host'].';dbname='.$pdo_config['database_name'].';port='.$pdo_config['database_port'].';charset='.$pdo_config['charset'], $pdo_config['database_user'], $pdo_config['database_password']);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$instance;
    }
} 