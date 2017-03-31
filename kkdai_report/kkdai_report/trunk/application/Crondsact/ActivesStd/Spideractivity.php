<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/runactives&task=ActivesStd.Spideractivity&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/13 0013
 * Time: 上午 9:49
 */
class Spideractivity extends \Sooh\Base\Crond\Task {
    public function init() {
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 60;
        $this->_iissStartAfter = 0;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
        $this->db_p2p = \Sooh\DB\Broker::getInstance('produce');
        $this->db_rpt = \Sooh\DB\Broker::getInstance('default');
    }

    protected $db_p2p;
    protected $db_rpt;

    protected $arr_admin = ['王宁' => 17717555734,'沈涛' => 18616626758, '刘新良'=>13818656993, '张姝俪'=>15800862286];
    protected $limitValue = [200, 400, 600, 800, 1000];  // 票池剩余数量预警 TODO:
//    protected $msg2customer = '感谢您参加“2016快快贷请您看电影”活动。您已获得蜘蛛网电影通兑券一张，通兑券电子码：{num}。';
    protected $msg2customer = '感谢您参加“2016快快贷请您看电影”活动。您已获得蜘蛛网电影通兑券一张，通兑券电子码：{num}。您可进入蜘蛛网电影频道在快速购票频道选择电影和座位，并在支付页面选择使用蜘蛛系列卡券中的蜘蛛网通兑券即可完成换购！';
    protected $msg2admin = '运管：{admin}，现平台剩余{remain}个号码，请注意补仓。';

    protected $userDefine = ['非蜘蛛用户', '蜘蛛用户'];


    public function free() {
        parent::init();
        $this->db_p2p=null;
        $this->db_rpt=null;
    }
    protected function onRun($dt) {
        $spider_amount_limit = 100000;  // 蜘蛛用户送票的最低首投金额【元】
        $investmentStep = 50000; // 每累计50000发一张票 TODO:

        $spider_active_from = '2016-01-13 00:00:00';  // 蜘蛛活动时间
        $spider_active_to   = '2016-06-20 23:59:59';
        $arr_activity_source = ['spider', '102520160121300000', '101320160121300000']; // 参与蜘蛛首投送票的渠道

        $active_from = '2016-02-26 00:00:00'; // 累计投资活动时间 TODO:
        $active_to = '2016-03-25 23:59:59';

        $dtNow = date('Y-m-d H:i:s', $dt->timestamp());
        $dtFrom = date('Y-m-d H:i:s', $dt->timestamp() - 5400);  // 抓取订单的点单时间范围
        $dtTo = date('Y-m-d H:i:s', $dt->timestamp() + 5);
		
		if($this->_isManual) {
			$dtFrom = date('Y-m-d 00:00:00', $dt->timestamp());
			$dtTo = date('Y-m-d 23:59:59', $dt->timestamp());
		}
		
var_log($dtFrom, 'dtFrom>>>>>>');
var_log($dtTo, 'dtTo>>>>');		
		$super_user = $this->db_p2p->getCol('phoenix.customer', 'customer_id', ['flag'=>1]);
		
        $poi_success_status = [601, 603, 610];

        $where = ['create_time]'=>$dtFrom, 'create_time['=>$dtTo, 'poi_status'=>$poi_success_status, 'customer_id!'=>$super_user, 'poi_type'=>0];
        $userIds = $this->db_p2p->getCol('phoenix.bid_poi', 'distinct(customer_id)', $where);

        error_log('############################################################################################');
        error_log('购买用户数目【'.$dtFrom.'，'.$dtTo.'】：'.sizeof($userIds));
        $lastOrderTime = $this->db_p2p->getOne('phoenix.bid_poi', 'create_time', ['poi_status'=>$poi_success_status], 'rsort create_time');
        error_log('最后一次成功订单时间：'.$lastOrderTime);
        if (empty($userIds)) {
            return true;
        }

        foreach($userIds as $uid) {
            error_log('==============================================================================');
            $isSpider = $this->db_p2p->getRecord('phoenix.customer', 'customer_realname,source, customer_cellphone', ['customer_id'=>$uid]);
            if(in_array($isSpider['source'], $arr_activity_source)) {
                $flagSpider = 1;
            }else{
                $flagSpider = 0;
            }


            /**
             *
             * 是蜘蛛用户，在蜘蛛活动时间内进行首投奖励操作
             */
            if ($flagSpider && $dtNow >= $spider_active_from && $dtNow <= $spider_active_to) {
                $isGived = $this->db_rpt->getRecordCount('db_kkrpt.tb_activity_spider', ['userId'=>$uid, 'flag_msg'=>1, 'isSpiderFirst'=>1]);
                if($isGived >0 ) {
                    $this->printLog($isSpider['customer_realname'], $isSpider['customer_cellphone'], $isSpider[$uid], $this->userDefine[$flagSpider], '已经发放过首投奖励');
                }else {
                    $where = ['create_time]'=>$spider_active_from, 'create_time['=>$spider_active_to, 'poi_status'=>$poi_success_status, 'customer_id'=>$uid, 'poi_type'=>0];
                    $record = $this->db_p2p->getRecord('phoenix.bid_poi', 'bid_id, pay_amount', $where, 'sort create_time');
                    $prdt =  $this->db_p2p->getOne('phoenix.bid', 'bid_id', ['bid_type!'=>501, 'product_type'=>[1,2], 'bid_id'=>$record['bid_id']]);
                    if (empty($prdt)) {
                        $first_amount = 0;
                    }else {
                        $first_amount = $record['pay_amount'];
                    }

//error_log(\Sooh\DB\Broker::lastCmd());
                    $first_amount = empty($first_amount)?0:$first_amount;
                    if($first_amount >= $spider_amount_limit) {
                        $this->sendTicketAndMsg($uid, $isSpider['customer_cellphone'], $isSpider['customer_realname'], $flagSpider, 1);
                    }else{
                        $this->printLog($isSpider['customer_realname'], $isSpider['customer_cellphone'], $uid, $this->userDefine[$flagSpider], '首单金额【'.$first_amount.'元】不能发放电影券');
                    }
                }
            }

            /**
             *
             * 所有用户，活动时间内进行累计投资奖励操作
             */
            if ($dtNow >= $active_from && $dtNow <= $active_to) {
                $where = ['create_time]'=>$active_from, 'create_time['=>$active_to, 'poi_status'=>$poi_success_status, 'customer_id'=>$uid, 'poi_type'=>0];


                $records = $this->db_p2p->getAssoc('phoenix.bid_poi', 'poi_id', 'bid_id, amount', $where);
                $sumAmount = 0;
                if (!empty($records)) {
                    $tmp = [];
                    foreach($records as $r) {
                        $tmp[] = $r['bid_id'];
                    }
                    reset($records);

                    $tmp = array_unique($tmp);
                    $prdt = $this->db_p2p->getCol('phoenix.bid', 'bid_id', ['bid_type!'=>501, 'product_type'=>[1,2], 'bid_id'=>$tmp]);
//        var_log($prdt, 'prdt>>>>>>>');
                    if (!empty($prdt)) {
                        foreach($records as $r) {
                            if (in_array($r['bid_id'], $prdt)) {
                                $sumAmount += $r['amount']/100;
                            }
                        }
                    }
                }
//        var_log($sumAmount, 'sumAmount>>>>>>>');
                $this->printLog($isSpider['customer_realname'], $isSpider['customer_cellphone'], $uid, $this->userDefine[$flagSpider], '累计投资金额：'.$sumAmount);
                if ($sumAmount >= $investmentStep) {
                    // 已经发送电影券的数量
                    $ticktsGivedNum = $this->db_rpt->getRecordCount('db_kkrpt.tb_activity_spider', ['userId'=>$uid, 'isSpiderFirst'=>0]);
                    // 活动期间内累计投资应该发送的电影券数量
                    $num = floor($sumAmount / $investmentStep);
                    $numGive = $num-$ticktsGivedNum;
                    $this->printLog($isSpider['customer_realname'], $isSpider['customer_cellphone'], $uid, $this->userDefine[$flagSpider], '已经发送电影券：'.$ticktsGivedNum.'，应发总数：'.$num.'，待发：'.$numGive);
                    while($numGive > 0) {
                        $this->sendTicketAndMsg($uid, $isSpider['customer_cellphone'], $isSpider['customer_realname'], $flagSpider);
                        $numGive--;
                    }

                }else {
                    $this->printLog($isSpider['customer_realname'], $isSpider['customer_cellphone'], $uid, $this->userDefine[$flagSpider], '不能发放电影券，投资总额：'.$sumAmount);
                }
            }
        }

        $this->lastMsg = $this->ret->toString();
        error_log("#### 执行完毕！");
        return true;
    }

    // 发电影票并且发短信
     protected function sendTicketAndMsg ($uid, $phone, $realname, $flagSpider=0, $isSpiderFirst=0) {
         $retry = 5;
         while ($retry>0) {
             $ticket_info = $this->db_rpt->getRecords('db_kkrpt.tb_activity_spider', 'ticketSerialNo,userId', ['flag_msg'=>0], null, 10);
             $k = array_rand($ticket_info);
             $ticket_info = $ticket_info[$k];
             if (!empty($ticket_info)) {
                 $record = ['userId' => $uid, 'flag_msg' => 1];
                 if($isSpiderFirst) {
                     $record['isSpiderFirst'] = 1;
                 }
                 try {
                     \Sooh\DB\Broker::errorMarkSkip();
                     $r = $this->db_rpt->updRecords('db_kkrpt.tb_activity_spider', $record, ['ticketSerialNo'=>$ticket_info['ticketSerialNo'],'flag_msg'=>0]);
                     // 发送短信
                     if($r !== true) {
                         $msg = str_replace('{num}',$ticket_info['ticketSerialNo'], $this->msg2customer);
                         $sendResult = $this->sendMsg($phone,$msg);
                         if (!$sendResult) {
                             $record = ['userId'=>$ticket_info['userId'], 'flag_msg'=>0, 'isSpiderFirst'=>0];
                             $this->db_rpt->updRecords('db_kkrpt.tb_activity_spider', $record, ['ticketSerialNo'=>$ticket_info['ticketSerialNo']]);
                             $retry--;
                             continue;
                         }
                         if ($isSpiderFirst) {
                             $msg = ' 首投奖励电影券和短信发放成功';
                         }else {
                             $msg = ' 电影券和短信发放成功';
                         }
                         $this->printLog($realname, $phone, $uid, $this->userDefine[$flagSpider], 'retry：'.$retry.$msg);
                         $leftTicktsNum = $this->db_rpt->getRecordCount ('db_kkrpt.tb_activity_spider', ['flag_msg'=>0]);
                         error_log('还剩余票数：'.$leftTicktsNum);
                         if (in_array($leftTicktsNum, $this->limitValue)) {
                             foreach($this->arr_admin as $adminName => $phone) {
                                 $msg = str_replace(['{admin}', '{remain}'], [$adminName,$leftTicktsNum], $this->msg2admin);
                                 /**
                                  * 发短信给管理员
                                  */
                                 $this->sendMsg($phone,$msg);
                             }
                         }

                         break;
                     }
                 }catch(\ErrorException $e){
                     if (\Sooh\DB\Broker::errorIs($e)){
                         $retry--;
                         continue;
                     }
                 }
             }
             $retry--;
         }

         if ($retry == 0) {
             $this->printLog($realname, $phone, $uid, $this->userDefine[$flagSpider], '发送电影券失败');
         }

     }
    // 发短信
    protected function sendMsg($phone, $msg) {
        try {
//            $smsReg = \Sooh\DB\Cases\SMSCode::getCopy($phone);
//            $smsReg->sendCode($msg);
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
// error_log('send msg success!');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    protected function printLog ( $realname, $phone, $uid, $msg1='',$msg2=''){
        error_log($msg1.'【'.$realname.'】【'.$phone.'】【'.$uid.'】'.$msg2);
    }

}
