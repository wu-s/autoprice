<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:51
 */

class Leadgenesis extends Agent {

    public function mockResponse(){

        $this->response = <<<__RESPONSE__
<?xml version="1.0"?>
<result><code>0</code><msg>success</msg><pingid>fab57211e92258d6cc66b4d67ca456a2</pingid><price>0.00</price><expires>1523025780</expires></result>
__RESPONSE__;

    }

    protected function init($data){
        $this->url = "https://api.leadgenesis.info/v2/leads/ping";
        $this->params = array(
            'camp_id'               => '3756',
            'camp_key'              => 'f8CUBG9gPwU',
            'homeowner'             => 'YES',
            'roof_shade'            => 'A Little Shade',
            'electric_bill_monthly' => '$151-200',
            'zip_code'           => $data['Zip'],
            'electric_provider'  => $data['Utility_Provider'],
        );
    }

    protected function parse(){
        $xml = simplexml_load_string($this->response);
        $json = json_encode($xml);
        $rtn = json_decode($json, true);
        $this->price = isset($rtn['price']) ? $rtn['price'] : 0;
        $this->success = isset($rtn['code']) && $rtn['code'] == '0' ? 1 : 0;
//        Log::debug("test");
        $this->result = $rtn;
        Log::debug($rtn);
    }
} 