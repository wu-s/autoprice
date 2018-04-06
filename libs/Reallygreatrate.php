<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:51
 */

class Reallygreatrate extends Agent {
    protected function init($data){
        $this->url = "https://www.reallygreatrate.com/api/ping/index.php";
        $this->params = array(
            'api_key'               => 'e41eb329b2f84f1c72ce02bb359616a2',
            'publisher_id'          => '984',
            'rcid'                  => '5330',
            'field_3'               => 'Yes',
            'field_4'               => 200,
            'field_13'              => $this->createLeadToken(),
            'zip'                   => $data['Zip'],
            'Electricity_Company_1' => $data['Utility_Provider'],
            'field_2'               => $data['State']
        );
    }

    protected function parse(){
        $rtn = json_decode($this->response, true);
        $this->price = isset($rtn['price']) ? $rtn['price'] : 0;
        $this->result = $rtn;
        Log::debug($rtn);
    }
} 