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

    private $data;
    private $result = array();
    private $inquiry_time;

    const MOCK_MODE = false;
//    const MOCK_MODE = true;

    public function init($csv){
        $sample = new Sample();
        $sample->init($csv);
        $this->data = $sample->getSampleData(20);
        $this->inquiry_time = date('Y-m-d H:i:s', time());
    }

    private function formatInquryRecord($row, $type = 1, Agent $agent = null){
        return array(
            $this->inquiry_time,   //inquiry_time
            $row['State'], //state
            $row['Zip'],    //zip
            $row['Utility_Provider'], //
            $agent->getPrice(),
            $type,
            $agent->getUrl(),
            $agent->getParams(),
            $agent->getResponse(),
        );

    }

    public function run(){

        $gotigay = new Gotitguy();
        $hilprod = new Hilprod();
        $leadgenesis = new Leadgenesis();
        $realygreatrate = new Reallygreatrate();
        $inquiry_record = array();
        $inquiry_log = array();

        $db = DB::getInstanse();

        $sql = 'insert into inquiry_record (`inquiry_time`, `state`, `zip`, `utility_provider`, `price`, `type`, `insert_date`, `update_date`, `url`, `params`, `response`) values(?, ?, ?, ?, ?, ?, ?, now(), now(), ?, ?, ?)';
        $sth = $db->prepare($sql);

        foreach($this->data as $v){
            foreach($v as $row){

//                $gotigay->run($row, self::MOCK_MODE);
//                $sth->execute($this->formatInquryRecord($row, self::TYPE_GOTITGUY, $gotigay));
//                $price = $gotigay->getPrice();
//                Log::debug("price = " . $price);

                $hilprod->run($row, self::MOCK_MODE);
                $price = $hilprod->getPrice();
                $sth->execute($this->formatInquryRecord($row, self::TYPE_HILPROD, $hilprod));
                Log::debug("price = " . $price);


                $leadgenesis->run($row, self::MOCK_MODE);
                $sth->execute($this->formatInquryRecord($row, self::TYPE_LEADGENESIS, $leadgenesis));
                $price = $leadgenesis->getPrice();
                Log::debug("price = " . $price);


//                $realygreatrate->run($row, self::MOCK_MODE);
//                $sth->execute($this->formatInquryRecord($row, self::TYPE_REALLYGREATRATE, $realygreatrate));
//                $price = $realygreatrate->getPrice();
//                Log::debug("price = " . $price);

                break;
            }
            break;
        }
    }
} 