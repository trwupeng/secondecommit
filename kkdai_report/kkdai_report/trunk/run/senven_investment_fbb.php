<?php
/**
 *
 *@author wu.peng 
 *
 */

define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
include dirname(__DIR__).'/conf/globals.php';
include dirname(__DIR__).'/conf/autoload.php';
set_time_limit(0);
if (!function_exists('var_log')) {
    function var_log($var, $msg = '')
    {
        error_log($msg . ' ' . var_export($var, true));
    }
}
if (!function_exists('echo_log')) {
    function echo_log($var, $msg = '')
    {
        echo "<pre>" . $msg . ' ' . var_export($var, true) . "</pre>";
    }
}


$fileNameToSave = './'.date('Y-m-d').'until_seven_fbb.txt';
if(file_exists($fileNameToSave)){
    unlink($fileNameToSave);
}
file_put_contents($fileNameToSave, '截止7月房宝宝在投金额'."\n", FILE_APPEND);


$db_rpt = \Sooh\DB\Broker::getInstance('produce');

$sql='SELECT bid_id from phoenix.bid WHERE bid_status!=4011 and bid_title like "房宝宝%"';
$rs = $db_rpt->execCustom(['sql'=>$sql]);
$bid_id=$db_rpt->fetchAssocThenFree($rs);
//$arr_coustom_id=$db->getCol('phoenix.customer','customer_id',$where);

if(!empty($bid_id)){
	 
    $timeTo = '2016-07-31 23:59:59';
    foreach ($bid_id as $bid){
          
        $tmp = $db_rpt->newWhereBuilder();
        $tmp->init('OR');
        $tmp->append('bid_type!', 501);
        $tmp->append('bid_type', null);
        $where_order = $db_rpt->newWhereBuilder();
        $where_order->init('AND');
        $where_order->append(null, $tmp);

        $where_order->append(['poi_status'=>[601, 603], 'poi_type'=>0, 'create_time['=>$timeTo,'bid_id'=>$bid]);
        
        $fdbamount=$db_rpt->getRecords('phoenix.bid_poi','sum(amount) as fdbamount',$where_order);
        var_log($fdbamount,'fbbamount>>>>>>>>>');
        var_log(\SOOH\DB\Broker::lastCmd(),'sql>>>>>>');
        
        if(!empty($fdbamount)){
            
            $str = ($fdbamount/100)."\n";
            file_put_contents($fileNameToSave, $str, FILE_APPEND);
        }
    }
}





