<?php
/**
 * 福袋活动抓取数据
 * 最后一次投资在xx月xx日（包含）之前没有参与过yy月yy日开启的福袋活动
 * php xxxxx/LuckpagDataGrab.php 20160307 20160607
 * 第一个日期参数：最后投资的日期
 * 第二个日期参数：福袋开始的活动日期
 *
 * 将筛选出的用户保存在tb_activity_luckpag里
 * 如果此活动日期已经抓过数据需要重抓的话，需要删除这个活动日期的数据才能重抓
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/6/27 0027
 * Time: 上午 11:30
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

$db_rpt = \Sooh\DB\Broker::getInstance('default');
$db_produce = \Sooh\DB\Broker::getInstance('produce');

$ymdLastBuy = $argv[1];  // 最后一次购买日期
$ymdActiviyStart = $argv[2]; // 福袋活动开始日期
if(empty($ymdLastBuy) || sizeof($argv)!=3 || empty($ymdActiviyStart)) {
    die('格式应为：php xxxxx.php yyyymmdd yyyymmdd'."\n");
}

if(!checkDateFormat($ymdLastBuy) || !checkDateFormat($ymdActiviyStart)) {
    die('日期格式应为：php xxxxx.php yyyymmdd yyyymmdd'."\n");
}

$isGrabed = $db_rpt->getRecordCount('db_kkrpt.tb_activity_luckpag', ['ymdStart'=>$ymdActiviyStart]);
if($isGrabed){
    die($ymdActiviyStart.'日的已经抓去过了，可以删除这一天的所有记录，再重新抓取'."\n");
}


$where = ['ymdLastBuy['=>$ymdLastBuy, 'ymdLastBuy>'=>0, 'flagUser!'=>1];
$total = $db_rpt->getRecordCount('db_kkrpt.tb_user_final', $where);
error_log('total:'.$total);
if(!$total) {
    die('没有满足条件的用户也');
}
$pagesize = 500;
$pageCount = ceil($total/$pagesize);

$activityStartFullTime = date('y-m-d H:i:s', strtotime($ymdActiviyStart));
$num = 1;
$add=0;
for($i=0; $i<$pageCount; $i++) {
    $users = $db_rpt->getCol('db_kkrpt.tb_user_final', 'userId', $where, null, $pagesize, $pagesize*$i);
    foreach($users as $uid) {
        error_log('No.'.$num++);
        $tmp_cus_id = $db_produce->getOne('phoenix.activity_tmp_cus', 'tmp_cus_id', ['customer_id'=>$uid]);
        $record = [];
        $record['ymdStart'] = $ymdActiviyStart;
        $record['userId']=$uid;
        if(empty($tmp_cus_id)) {
            $db_rpt->addRecord('db_kkrpt.tb_activity_luckpag', $record);
            $add++;
        }else {

            $lastActivity = $db_produce->getOne('phoenix.activity_luckpag_gift',
                'create_date', ['tmp_cus_id'=>$tmp_cus_id], 'rsort create_date', 1);
            if($lastActivity < $activityStartFullTime) {
                $record['tmpCusId'] = $tmp_cus_id;
                $db_rpt->addRecord('db_kkrpt.tb_activity_luckpag', $record);
                $add++;
            }
        }
    }
    sleep(1);
}

error_log('执行完毕，总共添加 '.$add.' 条记录');

function checkDateFormat($date) {
    if(strlen($date) != 8) {
        return false;
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, -2);
    return checkdate($month, $day, $year);
}