<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:51
 */

class Gotitguy extends Agent {


    public function mockResponse(){
        return <<<__RESPONSE__
<?xml version="1.0" encoding="utf-8"?>
<response><status>Success</status><lead_id>1475841</lead_id></response>
__RESPONSE__;

    }

    protected function init($data){
        $this->url = "https://leads.gotitguy.com/new_api/api.php";
        $this->params = array(
            'Key'               => 'V53927FKsIFcU0F9Z7Uvs7rDsTOSZ5lvsTp9Z7owWwlKswJS2HWSdEO-iljj',
            'API_Action'        => 'pingPostLead',
            'Mode'              => 'ping',
            'Return_Best_Price' => 1,
            'TYPE'              => 47,
            'SRC'               => 'ToLocal_Solar',
            'Pub_ID'            => '1070',
            'Landing_Page'      => 'http://national-solar-rebate.com',
            'st_t'              => 'st_t',
            'Home_Shaded'       => 'Mostly Sunny',
            'Homeowner'         => 'Yes',
            'Electric_Bill'     => '$150 - $200',
            'UNIVERSAL_LEAD_ID' => $this->createLeadToken(),
            'Zip'           => $data['Zip'],
            'Electricity_Company_1'  => $data['Utility_Provider'],
            'State'             => $data['State']
        );
    }

    protected function parse(){
        $xml = simplexml_load_string($this->response);
        $json = json_encode($xml);
        $rtn = json_decode($json, true);
        $this->price = isset($rtn['price']) ? $rtn['price'] : 0;
//        Log::debug("test");
        $this->result = $rtn;
        Log::debug($rtn);
    }
} 