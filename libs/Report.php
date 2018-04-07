<?php
/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/7
 * Time: 上午1:28
 */

class Report {

    private $inquiry_time;
    private $db;

    public function init($inquiry_time){
        $this->inquiry_time = $inquiry_time;
        $this->db = DB::getInstanse();
    }

    public function getPriceByState(){
        $sql = 'select `state`, `zip`, `utility_provider`, max(price) as price from inquiry_record where inquiry_time = ? group by `state`, `zip`, `utility_provider`';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($this->inquiry_time));
        $data = $sth->fetchAll();
//        print_r($data);
        $rtn = array();
        foreach($data as $row){
            $state = $row['state'];
            $price = $row['price'];
            if(isset($rtn[$state])){
                $rtn[$state]['count']++;
                $rtn[$state]['max_price'] = $rtn[$state]['max_price'] > $price ? $rtn[$state]['max_price'] : $price;
                $rtn[$state]['total_price'] = $rtn[$state]['total_price'] + $price;
            }else{
                $rtn[$state] = array('count' => 1, 'max_price' => $price, 'total_price' => $price);
            }
        }

        $result = array();
        foreach($rtn as $state => $v){
            $result[$state] = array(
                'state' => $state,
                'max_price' => $v['max_price'],
                'avg_price' => round($v['total_price'] / $v['count'], 2),
            );
        }

        return $result;
    }

    public function getPriceChangeOver50ByState(){
        $lastInquiryTime = $this->getLastInquiryTime();
        if(!$lastInquiryTime){
            return;
        }
        $currentReport = $this->getPriceByState();
        $lastReport = $this->getPriceByState();
        $states = array_unique(array_merge(array_keys($currentReport), array_keys($lastReport)));
//        print_r($states);
//        print_r(array_keys($currentReport));
        $rtn = array();
        foreach($states as $state){
            $tmp = array(
                'state'             => $state,
                'diff'              => '-',
                'last_max_price'    => '-',
                'last_avg_price'    => '-',
                'current_max_price' => '-',
                'current_avg_price' => '-',
            );
            if(isset($lastReport[$state])){
                $tmp['last_max_price'] = $lastReport[$state]['max_price'];
                $tmp['last_avg_price'] = $lastReport[$state]['avg_price'];
            }
            if(isset($currentReport[$state])){
                $tmp['current_max_price'] = $currentReport[$state]['max_price'];
                $tmp['current_avg_price'] = $currentReport[$state]['avg_price'];
            }
            if(!isset($lastReport[$state]) || !isset($currentReport[$state])){
                $rtn[] = $tmp;
                continue;
            }
            if(intval($lastReport[$state]['avg_price'])==0 && intval($currentReport[$state]['avg_price'])==0){
                continue;
            }
            $diff = $currentReport[$state]['avg_price'] - $lastReport[$state]['avg_price'];
            if(intval($lastReport[$state]['avg_price'])==0 || $diff / $lastReport[$state]['avg_price'] > 0.5){
                $tmp['diff'] = round($diff / $lastReport[$state]['avg_price'], 2);
            }
            $rtn[] = $tmp;
        }
        return $rtn;
    }

    private function getLastInquiryTime(){
        $sql = 'select distinct inquiry_time from inquiry_record where inquiry_time < ? order by inquiry_time desc limit 1';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($this->inquiry_time));
        $data = $sth->fetchAll();
        return $data ? $data[0]['inquiry_time'] : null;
    }
} 