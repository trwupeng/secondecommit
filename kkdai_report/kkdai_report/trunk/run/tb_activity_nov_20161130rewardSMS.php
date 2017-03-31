<?php
/**
 * 根据tb_activity_september_20161019reward给参加快快贷9月活动获奖用户发送短信
 *
 *
 *@param wu.peng
 *
 *@param time 2016/10/19
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
$recordCount = $db_rpt->getRecordCount('db_kkrpt.tb_activity_nov_20161130reward', $where);
error_log('total：'.$recordCount);
$pagesize = 50;
$pageCount = ceil($recordCount/$pagesize);
$sendSmsNum = 0;
$sendSmsFailedNum= 0;
$num = 1;
for($i=0; $i<$pageCount; $i++) {
   
    $due=$db_rpt->getRecords('db_kkrpt.tb_activity_nov_20161130reward','phone,ticketNo,reward,kahao,mima',$where);
//     var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>');
     var_log($due,'due>>>>>>>>>');
   
        if(!empty($due)) {
            foreach ($due as $v){
              //$v['ticketNo'];
             
              if($v['reward']=='微信征稿'){
                  var_log($v['ticketNo'],'>>>>>>>>>>>');
                  
                  $msg=" 尊敬的用户：您好！感谢您参加快快贷“征稿|你我之间的故事”的活动。您已获得蜘蛛网电影票通兑券一张，通兑券电子码：".$v['ticketNo']."。您可进入蜘蛛网电影频道在快速购票频道选择电影和座位，并在支付页面选择使用蜘蛛系列卡券中的蜘蛛网通兑券完成换购！如您有任何疑问，都可直接联系官方客服：4000-137-000。";
                  //var_log($msg,'msg>>>>>>>>>');
                  
                  $sendMsg= sms($v['phone'],$msg);
                  if(!$sendMsg){
                      error_log('第  '.$num++.' 个获得微信征稿电影券手机号  '.' '.$v['phone'].' 提交成功');
                      $sendSmsFailedNum++;
                  }
                
                  $upd = [
                      'dtLastNotice'=>date('YmdHis'),
                      'flag'=>1,
                  ];
                  $db_rpt->updRecords('db_kkrpt.tb_activity_nov_20161130reward', $upd, ['phone'=>$v['phone'], 'flag'=>0]);
                  
                  error_log('第  '.$num++.' 个获得微信征稿电影券手机号 '.' '.$v['phone'].' 提交成功');
                  
              }
              elseif($v['reward']=='土豪榜'){
                  var_log($v['kahao'],'kahao>>>>>>>>>>>');
                  var_log($v['mima'],'mima#############');
                  $msg="恭喜您获得“给礼！给利！双11+感恩节·三重豪礼重磅回馈”活动奖品：京东E卡500元（卡号：".$v['kahao']." 密码：".$v['mima']."），2019-12-01前使用有效。您可进入京东网的“我的京东”页面，点击“京东卡/E卡”，输入卡号/卡密进行绑定，可用于选购京东自营商品！快快贷后续活动依然奖品丰厚，请保持关注哦！";
              
                  $sendMsg= sms($v['phone'],$msg);
                  //var_log($sendMsg,'send<<<<<<<<');
              
                  if(!$sendMsg){
                      error_log('第  '.$num++.' 个获得土豪榜京东E卡手机号  '.' '.$v['phone'].' 提交成功');
                      $sendSmsFailedNum++;
                  }
              
                   
                  $upd_one = [
                      'dtLastNotice'=>date('YmdHis'),
                      'flag'=>1,
                  ];
                  $db_rpt->updRecords('db_kkrpt.tb_activity_nov_20161130reward', $upd_one, ['phone'=>$v['phone'], 'flag'=>0]);
              
                  error_log('第 '.$num++.' 个获得土豪榜京东E卡手机号  '.' '.$v['phone'].' 提交成功');
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