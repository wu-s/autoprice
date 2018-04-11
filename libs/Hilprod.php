<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:51
 */

class Hilprod extends Agent {
    const SUB_ID_LENGTH = 5;
    const CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function mockResponse(){
        $this->response = <<<__RESPONSE__
<?xml version="1.0" encoding="UTF-8"?>
<response>
 <status>Unmatched</status>
 <lead_id>773002401</lead_id>
<price>0.00</price></response>
__RESPONSE__;

    }

    protected function init($data){
        $this->url = "https://lm.hilprod.com/lead/ping";
        $this->params = array(
            'SRC'       => 'Aff8006',
            'Key'       => 'h3akb6cVE6TNE6v3E6r',
            'Project'   => 'Solar Electrical',
            'Return_Best_Price' => 1,
            'tcpa'      => 'Yes',
            'Landing_Page'  => 'http://national-solar-rebate.com',
            'Motivation'    => '$151-200',
            'Sub_ID'        => $this->createSubId(),
            'leadToken'     => $this->createLeadToken(),
            'Zip'           => $data['Zip'],
            'Utility_Provider'  => $data['Utility_Provider'],
        );
    }

    protected function parse(){
        $xml = simplexml_load_string($this->response);
        $json = json_encode($xml);
        $rtn = json_decode($json, true);
        $this->price = isset($rtn['price']) ? $rtn['price'] : 0;
        $this->success = isset($rtn['status']) && $rtn['status'] == 'Matched' ? 1 : 0;
//        Log::debug("test");
        $this->result = $rtn;
        Log::debug($rtn);
    }

    private function createSubId(){
        $rtn = array();
        for($i = 0; $i < self::SUB_ID_LENGTH; $i ++){
            $rtn[] = mt_rand(0, 9);
        }
        return join('', $rtn);
    }
} 