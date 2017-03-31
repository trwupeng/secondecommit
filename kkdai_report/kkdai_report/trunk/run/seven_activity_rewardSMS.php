<?php
/**
 * 根据tb_activity_seven_20160812reward给参加快快贷7月活动获奖用户发送短信
 *
 *
 *@param wu.peng
 *
 *@param time 2016/08/16
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
//$msg ='尊敬的快快贷用户：恭喜您获得“快快贷，让暑假不寂寞”活动三个月腾讯视频会员兑换券一张，兑换码为：{num}。您可进入腾讯视频会员个人中心，在兑换VIP会员页面，输入兑换码兑换！快快贷后续活动依然奖品丰厚，请保持关注哦！';
$db_rpt = \Sooh\DB\Broker::getInstance('default');
$db_produce = \Sooh\DB\Broker::getInstance('produce');
$where = ['flag'=>0];
$recordCount = $db_rpt->getRecordCount('db_kkrpt.tb_activity_seven_20160812reward', $where);
error_log('total：'.$recordCount);
$pagesize = 50;
$pageCount = ceil($recordCount/$pagesize);
$sendSmsNum = 0;
$sendSmsFailedNum= 0;
$num = 1;
for($i=0; $i<$pageCount; $i++) {
   
    $due=$db_rpt->getRecords('db_kkrpt.tb_activity_seven_20160812reward','phone,ticketNo,reward',$where);
//     var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>');
     var_log($due,'due>>>>>>>>>');
   
        if(!empty($due)) {
            foreach ($due as $v){
              //$v['ticketNo'];
             
              if($v['reward']=='哈根达斯兑换券'){
                  var_log($v['ticketNo'],'>>>>>>>>>>>');
                  
                  $msg="尊敬的快快贷用户：恭喜您获得“快快贷，让暑假不寂寞”活动哈根达斯兑换券两张，兑换码为：".$v['ticketNo']."。您可凭此兑换码于2017年03月31日前，到哈根达斯专卖店领取单球2份。快快贷后续活动依然奖品丰厚，请保持关注哦！";
                  //var_log($msg,'msg>>>>>>>>>');
                  
                  $sendMsg= sms($v['phone'],$msg);
                  if(!$sendMsg){
                      error_log('第  '.$num++.' 哈根达斯券用户手机号  '.' '.$v['phone'].' 提交成功');
                      $sendSmsFailedNum++;
                  }
                
                  $upd = [
                      'dtLastNotice'=>date('YmdHis'),
                      'flag'=>1,
                  ];
                  $db_rpt->updRecords('db_kkrpt.tb_activity_seven_20160812reward', $upd, ['phone'=>$v['phone'], 'flag'=>0]);
                  
                  error_log('第  '.$num++.' 哈根达斯券用户手机号 '.' '.$v['phone'].' 提交成功');
                  
              }
              elseif($v['reward']=='腾讯视频会员'){
                  var_log($v['ticketNo'],'>>>>>>>>>>>');
                  $msg="尊敬的快快贷用户：恭喜您获得“快快贷，让暑假不寂寞”活动三个月腾讯视频会员兑换券一张，兑换码为：".$v['ticketNo']."。您可进入腾讯视频会员个人中心，在兑换VIP会员页面，输入兑换码兑换！快快贷后续活动依然奖品丰厚，请保持关注哦！";
                  
                  $sendMsg= sms($v['phone'],$msg);
                  //var_log($sendMsg,'send<<<<<<<<');
                  
                  if(!$sendMsg){
                      error_log('第  '.$num++.' 腾讯视频会员用户手机号  '.' '.$v['phone'].' 提交成功');
                      $sendSmsFailedNum++;
                  }
                  
                 
                  $upd_one = [
                      'dtLastNotice'=>date('YmdHis'),
                      'flag'=>1,
                  ];
                  $db_rpt->updRecords('db_kkrpt.tb_activity_seven_20160812reward', $upd_one, ['phone'=>$v['phone'], 'flag'=>0]);
                  
                  error_log('第 '.$num++.' 腾讯视频会员用户手机号  '.' '.$v['phone'].' 提交成功');
              }
          $sendSmsNum++;
        }
     }
}
error_log('执行完毕! 短信提交失败数：'.$sendSmsFailedNum);
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