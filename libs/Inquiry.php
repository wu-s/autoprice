<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:52
 */

class Inquiry {

    const TYPE_GOTITGUY = 1;
    const TYPE_HILPROD = 2;
    const TYPE_LEADGENESIS = 3;
    const TYPE_REALLYGREATRATE = 4;

    const CONCURRENCY_NUM = 5;

    private $data;
    private $result = array();
    private $inquiry_time;

    private $concurrency = 0;

    const MOCK_MODE = false;
//    const MOCK_MODE = true;

    public function init($csv, $inquiry_time){
        $sample = new Sample();
        $sample->init($csv);
        $this->data = $sample->getSampleData(20);
        $this->inquiry_time = $inquiry_time;
    }

    private function formatInquryRecord($type = 1, Agent $agent = null){
        $row = $agent->getData();
        return array(
            $this->inquiry_time,   //inquiry_time
            $row['State'], //state
            $agent->getCode(),
            $row['Zip'],    //zip
            $row['Utility_Provider'], //
            $agent->getPrice(),
            $type,
            $agent->getUrl(),
            $this->formatParam($agent->getParams()),
            $agent->getResponse(),
        );

    }

    private function formatParam($params){
        $rtn = array();
        foreach($params as $k => $v){
            $rtn[] = $k . '=' . urlencode($v);
        }
        return join('&', $rtn);
    }

    public function run(){

        $gotigayObjs = array();
        $hilprodObjs = array();
        $leadgenesisObjs = array();
        $realygreatrateObjs = array();

        for($i = 0; $i < self::CONCURRENCY_NUM; $i ++){
            $gotigayObjs[] = new Gotitguy();
            $hilprodObjs[] = new Hilprod();
            $leadgenesisObjs[] = new Leadgenesis();
            $realygreatrateObjs[] = new Reallygreatrate();
        }

        $db = DB::getInstanse();

        $sql = 'insert into inquiry_record (`inquiry_time`, `state`, `code`, `zip`, `utility_provider`, `price`, `type`, `insert_date`, `update_date`, `url`, `params`, `response`) values(?, ?, ?, ?, ?, ?, ?, now(), now(), ?, ?, ?)';
        $sth = $db->prepare($sql);

        $this->concurrency = 0;

        foreach($this->data as $state => $v){
            foreach($v as $row){
                if($this->concurrency >= self::CONCURRENCY_NUM){
                    for($i = 0; $i < self::CONCURRENCY_NUM; $i ++){
                        Log::debug("\n\n=================================\n\n");
                        $gotigay = $gotigayObjs[$i];
                        $gotigay->result();
                        $sth->execute($this->formatInquryRecord(self::TYPE_GOTITGUY, $gotigay));
                        $gotigay->log();

                        $hilprod = $hilprodObjs[$i];
                        $hilprod->result();
                        $sth->execute($this->formatInquryRecord(self::TYPE_HILPROD, $hilprod));
                        $hilprod->log();

                        $leadgenesis = $leadgenesisObjs[$i];
                        $leadgenesis->result();
                        $sth->execute($this->formatInquryRecord(self::TYPE_LEADGENESIS, $leadgenesis));
                        $leadgenesis->log();

                        $realygreatrate = $realygreatrateObjs[$i];
                        $realygreatrate->result();
                        $sth->execute($this->formatInquryRecord(self::TYPE_REALLYGREATRATE, $realygreatrate));
                        $realygreatrate->log();
                    }

                    $this->concurrency = 0;
                }

                $gotigayObjs[$this->concurrency]->run($row, self::MOCK_MODE);
                $hilprodObjs[$this->concurrency]->run($row, self::MOCK_MODE);
                $leadgenesisObjs[$this->concurrency]->run($row, self::MOCK_MODE);
                $realygreatrateObjs[$this->concurrency]->run($row, self::MOCK_MODE);

                $this->concurrency ++;
//                break;
            }
//            break;
        }
    }
} 