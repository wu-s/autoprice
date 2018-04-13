<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午8:51
 */

class Reallygreatrate extends Agent {

    public function mockResponse(){
        return <<<__RESPONSE__
{"status":"success","ping_id":1252185409,"price":0,"expires":1523070084}
__RESPONSE__;

    }

    protected function init($data){
        $this->url = "https://www.reallygreatrate.com/api/ping/index.php";
        $this->params = array(
            'api_key'               => 'e41eb329b2f84f1c72ce02bb359616a2',
            'publisher_id'          => '984',
            'rcid'                  => '5330',
            'field_3'               => 'Yes',
            'field_4'               => 200,
            'field_13'              => $this->createLeadToken(),
            'state'                 => $data['State'],
            'zip'                   => $data['Zip'],
            'field_2'               => $data['Utility_Provider'],
        );
    }

    protected function parse(){
        $rtn = json_decode($this->response, true);
        $this->price = isset($rtn['price']) ? $rtn['price'] : 0;
        $this->success = isset($rtn['status']) && $rtn['status'] == 'success' ? 1 : 0;
        $this->result = $rtn;
        Log::debug($rtn);
    }
} 