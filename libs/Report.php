<?php

/**
 * Created by PhpStorm.
 * User: wu-s
 * Date: 18/4/7
 * Time: 上午1:28
 */
class Report
{

    private $inquiry_time;
    private $db;

    public function init($inquiry_time)
    {
        $this->inquiry_time = $inquiry_time;
        $this->db = DB::getInstanse();
    }

    public function getPriceByState()
    {
        $data = $this->getPriceByStateAndInquiryTime($this->inquiry_time);
        $rtn = array_values($data);
        usort($rtn, function ($a, $b) {
            if ($a['avg_price'] == $b['avg_price']) return 0;
            return ($a['avg_price'] > $b['avg_price']) ? -1 : 1;
        });
        return $rtn;
    }

    public function getInquiryTime($startDate, $endDate)
    {
        $sql = 'select distinct inquiry_time from inquiry_record where inquiry_time >= ? and inquiry_time < ? order by inquiry_time';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($startDate, $endDate));
        $data = $sth->fetchAll();
        $rtn = array();
        foreach ($data as $row) {
            $rtn[] = $row['inquiry_time'];
        }
        return $rtn;
    }

    public function getAvgPriceHistoryByState($startDate, $endDate)
    {
        $inquiry_times = $this->getInquiryTime($startDate, $endDate);
//        print_r($inquiry_times);
        $result = array();
        foreach ($inquiry_times as $inquiry_time) {
            $data = $this->getPriceByStateAndInquiryTime($inquiry_time);
//            print_r($data);
//            print_r($inquiry_time);
            foreach ($data as $state => $price) {
                $tmp = array(
                    'inquiry_time'  => $inquiry_time,
                    'max_price' => $price['max_price'],
                    'avg_price' => $price['avg_price'],
                    'start' => $state,
                );
                $result[$inquiry_time][] = $tmp;
            }
        }
        return $result;
    }

    private function getPriceByStateAndInquiryTime($inquiry_time)
    {
        $sql = 'select `state`, `zip`, `utility_provider`, max(price) as price from inquiry_record where inquiry_time = ? group by `state`, `zip`, `utility_provider`';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($inquiry_time));
        $data = $sth->fetchAll();
//        print_r($data);
        $rtn = array();
        foreach ($data as $row) {
            $state = $row['state'];
            $price = $row['price'];
            if (isset($rtn[$state])) {
                $rtn[$state]['count']++;
                $rtn[$state]['max_price'] = $rtn[$state]['max_price'] > $price ? $rtn[$state]['max_price'] : $price;
                $rtn[$state]['total_price'] = $rtn[$state]['total_price'] + $price;
            } else {
                $rtn[$state] = array('count' => 1, 'max_price' => $price, 'total_price' => $price);
            }
        }

        $result = array();
        foreach ($rtn as $state => $v) {
            $result[$state] = array(
                'state' => $state,
                'max_price' => $v['max_price'],
                'avg_price' => round($v['total_price'] / $v['count'], 2),
            );
        }

        return $result;
    }

    public function getPriceInquiryHistory($startDate, $endDate){
        $sql = 'select * from inquiry_record where inquiry_time >= ? and inquiry_time < ?';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($startDate, $endDate));
        $data = $sth->fetchAll();
        return $data;
    }

    public function getPriceChangeOver50ByState()
    {
        $lastInquiryTime = $this->getLastInquiryTime();
        if (!$lastInquiryTime) {
            return;
        }
//        print_r($lastInquiryTime);
        $currentReport = $this->getPriceByStateAndInquiryTime($this->inquiry_time);
        $lastReport = $this->getPriceByStateAndInquiryTime($lastInquiryTime);
//        print_r($currentReport);
//        print_r($lastReport);
        $states = array_unique(array_merge(array_keys($currentReport), array_keys($lastReport)));
//        print_r($states);
//        print_r(array_keys($currentReport));
        $rtn = array();
        foreach ($states as $state) {
            $tmp = array(
                'state' => $state,
                'diff' => '-',
                'last_max_price' => '-',
                'last_avg_price' => '-',
                'current_max_price' => '-',
                'current_avg_price' => '-',
            );
            if (isset($lastReport[$state])) {
                $tmp['last_max_price'] = $lastReport[$state]['max_price'];
                $tmp['last_avg_price'] = $lastReport[$state]['avg_price'];
            }
            if (isset($currentReport[$state])) {
                $tmp['current_max_price'] = $currentReport[$state]['max_price'];
                $tmp['current_avg_price'] = $currentReport[$state]['avg_price'];
            }

            //前一次和当前，一方没有价格，一方价格为0，忽略
            if (!isset($currentReport[$state]) && isset($lastReport[$state]) && intval($lastReport[$state]['avg_price']) == 0) {
                continue;
            }
            if (!isset($lastReport[$state]) && isset($currentReport[$state]) && intval($currentReport[$state]['avg_price']) == 0) {
                continue;
            }
            //前一次和当前价格都为0，忽略
            if (intval($lastReport[$state]['avg_price']) == 0 && intval($currentReport[$state]['avg_price']) == 0) {
                continue;
            }


            if (!isset($lastReport[$state]) || !isset($currentReport[$state])) {
                $rtn[] = $tmp;
                continue;
            }

            $diff = $currentReport[$state]['avg_price'] - $lastReport[$state]['avg_price'];
            if ($lastReport[$state]['avg_price'] * 10000 == 0) { //前一次价格为0时，忽略
                $tmp['diff'] = 120;
            } elseif ($diff / $lastReport[$state]['avg_price'] > -0.5 && $diff / $lastReport[$state]['avg_price'] < 0.5) { //变动幅度小于50%,忽略
                continue;
            } else { //变动幅度>=50%
                $tmp['diff'] = round($diff / $lastReport[$state]['avg_price'], 3) * 100;
            }
            $rtn[] = $tmp;
        }


        //按照变化比例生序排列
        usort($rtn, function ($a, $b) {
            if ($a['diff'] == $b['diff']) return 0;
            return $a['diff'] > $b['diff'] ? 1 : -1;
        });

        foreach ($rtn as $i => $v) {
            $rtn[$i]['diff'] = $rtn[$i]['diff'] . '%';
        }

//                print_r($rtn);

        return $rtn;
    }

    private function getLastInquiryTime()
    {
        $sql = 'select distinct inquiry_time from inquiry_record where inquiry_time < ? order by inquiry_time desc limit 1';
        $sth = $this->db->prepare($sql);
        $sth->execute(array($this->inquiry_time));
        $data = $sth->fetchAll();
        return $data ? $data[0]['inquiry_time'] : null;
    }
} 