<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/29 0029
 * Time: 上午 10:00
 */
namespace Rpt\DataDig;
class BaidutongjiDataDig {

    public function importData ($domain, $start_date, $end_date) {

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $metrics=['pv_count','visitor_count','ip_count'];

        $apiBaiduTongji = new \Api\Baidutongji\ApiBaiduTongJi();
        $isLogined = $apiBaiduTongji->login();
        if(!$isLogined) {
            return false;
        }
        $receiveData = $apiBaiduTongji->getSiteData($domain, $start_date, $end_date, 'trend/time/a', implode(',', $metrics));
        if (($dayNum = $receiveData['body']['data'][0]['result']['total']) > 0) {
            $data = $receiveData['body']['data'][0]['result']['items'];
            for ($i = 0; $i < $dayNum; $i++) {
                $fields = ['ymd'=>str_replace('/', '', $data[0][$i][0])];
                foreach($data[1][$i] as $k => $v) {
                    if(!is_numeric($v)) {
                        $v = 0;
                    }
                    $fields[$metrics[$k]] = $v;
                }

                try {
                    \Sooh\DB\Broker::errorMarkSkip();
                    $db->addRecord(\Rpt\Tbname::tb_baidutongji, $fields);

                }catch (\ErrorException $e) {
                    if (\Sooh\DB\Broker::errorIs($e)){
                        $ymd = $fields['ymd'];
                        unset($fields['ymd']);
                        $db->updRecords(\Rpt\Tbname::tb_baidutongji, $fields, ['ymd'=>$ymd]);
                    }else {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                        return false;
                    }
                }
            }

            return true;
        }else {
            return false;
        }

    }


    public static function getData ($ymdFrom, $ymdTo) {
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $records = $db->getAssoc(\Rpt\Tbname::tb_baidutongji, 'ymd', 'pv_count,visitor_count,ip_count', ['ymd]'=>$ymdFrom, 'ymd['=>$ymdTo], 'sort ymd');
        return $records;
    }
}
