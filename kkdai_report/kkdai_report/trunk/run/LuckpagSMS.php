<?php
/**
 * 根据tb_activity_luckpag给没有玩福袋的用户发送短信
 *
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/6/27 0027
 * Time: 下午 2:45
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

$msg = '您有20元的现金福袋还未领取。福袋活动正火热进行中，每天可领取20元现金福袋，投资更有多款苹果热销商品等您拿。赶紧来点爆链接参与吧：http://www.kuaikuaidai.com/h5/sixactivity.html';


$ymdActivity = $argv[1];
if(empty($ymdActivity) || !checkDateFormat($ymdActivity)) {
    die('请输入活动的日期 格式：yyyymmdd'."\n");
}



$db_rpt = \Sooh\DB\Broker::getInstance('default');
$db_produce = \Sooh\DB\Broker::getInstance('produce');

$where = ['flag'=>0, 'ymdStart'=>$ymdActivity];
$recordCount = $db_rpt->getRecordCount('db_kkrpt.tb_activity_luckpag', $where);
error_log('total：'.$recordCount);
$pagesize = 500;
$pageCount = ceil($recordCount/$pagesize);
$sendSmsNum = 0;
$sendSmsFailedNum= 0;
$num = 1;
for($i=0; $i<$pageCount; $i++) {
    $users = $db_rpt->getCol('db_kkrpt.tb_activity_luckpag', 'userId', $where, null, $pagesize, $i*$pagesize);
    foreach($users as $uid) {
        $tmp_cus_id = $db_produce->getOne('phoenix.activity_tmp_cus', 'tmp_cus_id', ['customer_id'=>$uid]);
        $phone = $db_produce->getOne('phoenix.customer', 'customer_cellphone', ['customer_id'=>$uid]);
        if(empty($tmp_cus_id)) {
            $sendRs = sms($phone, $msg);
            if(!$sendRs) {
                error_log('第'.$num++.' '.$uid.' '.$phone.' 提交信息失败');
                $sendSmsFailedNum++;
            }else{
                $upd = [
                    'dtLastNotice'=>date('YmdHis'),
                ];
                $db_rpt->updRecords('db_kkrpt.tb_activity_luckpag', $upd, ['ymdStart'=>$ymdActivity, 'userId'=>$uid]);
                error_log('第'.$num++.' '.$uid.' '.$phone.' 提交成功');
            }
            $sendSmsNum++;
        }else{
            $ymdActivityFullTime = date('Y-m-d H:i:s', strtotime($ymdActivity));
            $lastActivity = $db_produce->getOne('phoenix.activity_luckpag_gift',
                'create_date', ['tmp_cus_id'=>$tmp_cus_id], 'rsort create_date', 1);

            $upd = [
                'tmpCusId'=>$tmp_cus_id,
            ];
            if($lastActivity <$ymdActivityFullTime) {

                $sendRs = sms($phoe, $msg);
                if(!$sendRs){
                    error_log('第'.$num++.' '.$uid.' '.$phone.' 提交信息失败');
                    $sendSmsFailedNum++;
                }else {
                    $upd['dtLastNotice']=date('YmdHis');
                    error_log('第'.$num++.' '.$uid.' '.$phone.' 提交成功');
                }
                $sendSmsNum++;
            }else{
                $upd['flag'] =1;
                error_log('第'.$num++.' '.$uid.' '.$phone.' 玩福袋了');
            }

            $db_rpt->updRecords('db_kkrpt.tb_activity_luckpag', $upd, ['ymdStart'=>$ymdActivity, 'userId'=>$uid]);
        }
    }
    sleep(1);
}

error_log('执行完毕! 仍有 '.$sendSmsNum.' 个用户没有玩福袋, 短信提交失败数：'.$sendSmsFailedNum);

// 发送短信

function sms($phone, $msg) {
    $retry = 5;
    while ($retry > 0) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Exception $e) {
            $retry--;
            continue;
        }
        return true;
    }

    if($retry==0) {
        return false;
    }
}


function checkDateFormat($date) {
    if(strlen($date) != 8) {
        return false;
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, -2);
    return checkdate($month, $day, $year);
}

