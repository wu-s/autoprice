<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:52
 */

use JMathai\PhpMultiCurl\MultiCurl;

class Agent {

    const CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected $url;
    protected $params;
    protected $response;
    protected $result;
    protected $price;
    protected $mock;

    public function run($data, $mock = false){
        $this->mock = $mock;
        $this->init($data);
//        Log::debug($this->url);
//        Log::debug($this->params);
//        print_r($this->result);
        $this->requestPost();
        $this->parse();
    }

    public function getPrice(){
        return $this->price;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getParams(){
        return $this->params;
    }

    public function getResponse(){
        return $this->response;
    }

    public function log(){

    }

    public function mockResponse(){

    }

    protected function requestPost(){

        if($this->mock){
            $this->mockResponse();
            return;
        }

        $mc = MultiCurl::getInstance();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);

        $call = $mc->addCurl($ch);
        $code = $call->code;
        $this->response = $call->getResult();
//        $this->response = curl_exec($ch);
        print_r($this->url);
        print_r($this->params);
        print_r($this->response);

//        curl_close($ch);

//        Log::debug('aaaa='.$data.'=bbbb');
    }

    protected function createLeadToken(){
        //B6680FCB-6EA2-BF32-F0EA-042C5C46333A
        $rtn = array();
        $rtn[] = $this->getRandomStr(8);
        $rtn[] = $this->getRandomStr(4);
        $rtn[] = $this->getRandomStr(4);
        $rtn[] = $this->getRandomStr(4);
        $rtn[] = $this->getRandomStr(12);
        return join('-', $rtn);
    }

    protected function getRandomStr($num){
        $t = str_split(self::CHARS);
        shuffle($t);
        return join('', array_slice($t, 0, $num));
    }
} 