<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/6
 * Time: 下午9:37
 */

class Sample {

    const ZIP_CODE_LENGTH = 5;

    private $csv;
    private $data;

    public function init($csv = "data/sample_data.csv"){
        $this->csv = $csv;
        $this->loadSampleData();
    }

    private function loadSampleData(){
        $result = array();
        $fp = fopen($this->csv, "r");
        $cols = fgetcsv($fp, 1000, "\t");
        while(($data = fgetcsv($fp, 1000, "\t")) !== false){
            $tmp = array();
            foreach($cols as $i => $v){
                $tmp[$v] = $data[$i];
            }
            $result[] = $tmp;
        }
        $this->data = $result;
    }

    public function getStates(){
        $result = array();
        foreach($this->data as $v){
            $result[$v['State']] = 1;
        }
        return array_keys($result);
    }

    public function getSampleData($limit = 20){
        $data = array();
        $rtn = array();
        foreach($this->data as $row){
//            if($row['State'] != $state){
//                continue;
//            }
            $row['Zip'] = str_pad($row['Zip'], self::ZIP_CODE_LENGTH, '0', STR_PAD_LEFT);
            $data[$row['State']][] = $row;
        }
        foreach($data as $state => $v){
            $num = count($v) > $limit ? $limit : count($v);
            shuffle($v);
            $rtn[$state] = array_slice($v, 0, $num);
        }
        return $rtn;
    }
} 