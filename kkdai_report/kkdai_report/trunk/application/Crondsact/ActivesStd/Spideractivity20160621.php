<?php
namespace PrjCronds;
/**
 *
 * 快快贷6月21日蜘蛛活动脚本
 * php /var/www/licai_php/run/crond.php "__=crond/runactives&task=ActivesStd.Spideractivity20160621&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/13 0013
 * Time: 上午 9:49
 */
class Spideractivity20160621 extends \Sooh\Base\Crond\Task {
    public function init() {
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 300; // 5分钟发一次票
        $this->_iissStartAfter = 300;  // 每个小时的3分钟开始
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
        $this->db_p2p = \Sooh\DB\Broker::getInstance('produce');
        $this->db_rpt = \Sooh\DB\Broker::getInstance('default');
    }

    protected $db_p2p;
    protected $db_rpt;
    protected $tbname = 'db_kkrpt.tb_activity_spider_20160621';
    protected $arr_admin = [13701783121=>'童益丰', 18616626758 =>'沈涛', 15800862286=>'张姝俪', 15021075217=>'李连奇'];
    protected $limitValue = [50, 100, 200, 400, 600, 800, 1000];  // 票池剩余数量预警
    protected $msg2customer = '感谢您参加“2016快快贷请您看电影”活动。您已获得蜘蛛网电影通兑券{num}张，通兑券电子码：{tickets}。您可进入蜘蛛网电影频道在快速购票频道选择电影和座位，并在支付页面选择使用蜘蛛系列卡券中的蜘蛛网通兑券即可完成换购！';
    protected $msg2admin = '运管：{admin}，现快快贷平台剩余{remain}个号码，请注意补仓。';



    public function free() {
        parent::init();
        $this->db_p2p=null;
        $this->db_rpt=null;
    }
    protected function onRun($dt) {
        $spiderActivityName = ['code'=>'spider20160621', 'name'=>'蜘蛛20160621活动'];
        $spider_from = '2016-06-20 17:00:00';
        $spider_to = '2016-12-31 23:59:59';
        $spider_first_buy_limit = 400000;  // 蜘蛛用户首投最低金额 分
        $spider_fist_buy_num = 3; // 蜘蛛用户满足首投条件送票送几张
        $arr_activity_source = ['spider'];
        $product_type = [1, 2];

        if($this->_isManual) {
            $ymd = date('Y-m-d', $dt->timestamp());
            $orderTimeFrom = $ymd.' 00:00:00';
            $orderTimeTo = $ymd.' 23:59:59';
        }else {
            // 搜索一个半小时之前的订单
            $orderTimeTo = date('Y-m-d H:i:s', $dt->timestamp());
            $orderTimeFrom = date('Y-m-d H:i:s', $dt->timestamp() - 5400);  // 抓取订单的点单时间范围
        }
        error_log('#############蜘蛛活动抓取订单时间 [ '.$orderTimeFrom.' -- '.$orderTimeTo.' ]');

        /**
         * 1.
         * 抓取时间段内所有投资用户（已经排除掉超级用户和投资体验标的用户）
         */
        $super = $this->db_p2p->getCol('phoenix.customer', 'customer_id', ['flag'=>1]); // 超级用户
        $success_status = [601, 603, 610]; // 成功订单状态

        $where = [
            'create_time]'   =>$orderTimeFrom,
            'create_time['  =>$orderTimeTo,
            'poi_status'    =>$success_status,
            'customer_id!'  =>$super,
            'poi_type'      =>0,
        ];
        $userIds = $this->db_p2p->getCol('phoenix.bid_poi', 'distinct(customer_id)', $where);
        //error_log(\Sooh\DB\Broker::lastCmd());
        $lastOrderTime = $this->db_p2p->getOne('phoenix.bid_poi', 'create_time', ['poi_status'=>$success_status], 'rsort create_time');
        error_log('最后一次成功订单时间：'.$lastOrderTime);
        if(empty($userIds)) {
            error_log('无购买的用户');
            error_log("############# 执行完毕！");
            return true;
        }


        foreach ($userIds as $uid) {

            /**
             * 2.
             * 蜘蛛首投送电影券活动
             * 送票要送就送够，不够一张也不送
             */
            $spiderMan = 0;
            $userInfo =  $this->db_p2p->getRecord('phoenix.customer',
                'customer_id, customer_realname,source, customer_cellphone', ['customer_id'=>$uid]);
            if(in_array($userInfo['source'], $arr_activity_source)) {
                $spiderMan = 1;
            }

            if($spiderMan) {
                // 首投是否发过电影票
                $isGived = $this->db_rpt->getRecordCount($this->tbname,
                    ['userId'=>$uid, 'flagGranted'=>1, 'activityName'=>$spiderActivityName['code']]);
                if($isGived) {
                    error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 已经发放过蜘蛛首投送的票了');
                }else {
                    $tmp = $this->db_p2p->newWhereBuilder();
                    $tmp->init('OR');
                    $tmp->append('bid_type!', 501);
                    $tmp->append('bid_type', null);
                    $where_order = $this->db_p2p->newWhereBuilder();
                    $where_order->init('AND');
                    $where_order->append(null, $tmp);
                    $where_order->append(['poi_status'=>$success_status, 'poi_type'=>0, 'customer_id'=>$uid]);

                    $order = $this->db_p2p->getRecord('phoenix.bid_poi', 'customer_id, pay_amount, poi_id, bid_id,create_time', $where_order, 'sort create_time');
                    if(!empty($order)) {
                        $bidProductType = $this->db_p2p->getOne ('phoenix.bid', 'product_type', ['bid_id'=>$order['bid_id']]);
                        if(in_array($bidProductType, $product_type) && $order['create_time'] >= $spider_from && $order['create_time'] <= $spider_to && $order['pay_amount'] >= $spider_first_buy_limit){
                            $tickets = $this->lockTicket($userInfo, $spider_fist_buy_num, $spiderActivityName['code']);
                            if(!empty($tickets)) {
                                error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 取票成功');
                                $msg_tickets = implode('、', array_keys($tickets));
                                $msg = str_replace(['{num}', '{tickets}'], [$spider_fist_buy_num, $msg_tickets], $this->msg2customer);
//var_log($msg, 'msg 2 customer>>');
                                // 给用户发送电影券短信
                                $sendRs = $this->sendMsg($userInfo['customer_cellphone'], $msg);
                                if($sendRs) {
                                    error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 发送短信成功');

                                    $updRs = $this->db_rpt->updRecords($this->tbname, ['flagMsg'=>1],
                                        ['ticketSerialNo'=>array_keys($tickets), 'userId'=>$uid, 'activityName'=>$spiderActivityName['code']]);
                                    if($updRs!== true) {
                                        error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 更新发送短信成功标识数据库成功');
                                    }else{
                                        error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 更新发送短信成功标识数据库失败');
                                    }
                                }else {
                                    error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 发送短信失败');
                                }

                                // 给运管发送补仓短信
                                $left = $this->db_rpt->getRecordCount($this->tbname, ['flagGranted'=>0]);
                                error_log('剩余票数:'.$left);
                                foreach($this->limitValue as $limit) {
                                    if(($left < $limit && ($left+$spider_fist_buy_num) > $limit) || $left == $limit) {
                                        error_log('开始给运管发送短信');
                                        $this->sendMsgToAdmin($left);
                                        break;
                                    }
                                }

                            }

                        }else {
                            error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 首单金额不满足活动金额或首单不在活动时间内');
                        }
                    }else{
                        error_log($spiderActivityName['name']. '用户'.$uid.' '.$userInfo['customer_realname'].' 没有符合订单（体验标）');
                    }
                }
            }else {
                error_log('用户'.$uid.' '.$userInfo['customer_realname'].' 不是'.$spiderActivityName['name'].'要求的渠道用户');
            }

            /**
             * 3.
             * 为累计投资金额活动留的
             *
             */

        }

        $this->lastMsg = $this->ret->toString();
        error_log("############# 执行完毕！");
        return true;
    }


    /**
     * 给用户取票
     * @param $userInfo
     * @param $num
     * @param $activityName
     * @return array
     */
    protected function  lockTicket ($userInfo, $givenum, $activityName) {
        $num = $givenum;
        $ticketGot = [];
        $dt = date('YmdHis');
        while ($num) {
            $No = $givenum-$num +1;
            $retry = 5;
            while ($retry) {
                $tickets = $this->db_rpt->getRecords($this->tbname, 'ticketSerialNo,userId',
                    ['flagGranted'=>0], null, $givenum*2+5);
                $k = array_rand($tickets);
                $temp_ticket = $tickets[$k];
                if(empty($temp_ticket)) {
                    error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].' 取到第 '.$No.' 张票, 票不够了');
                    $retry = 0;
                    break;
                }

                try {
                    \Sooh\DB\Broker::errorMarkSkip();
                    $rs =  $this->db_rpt->updRecords($this->tbname,
                        [
                            'userId'        =>$userInfo['customer_id'],
                            'realname'      => $userInfo['customer_realname'],
                            'phone'         => $userInfo['customer_cellphone'],
                            'createTime'    =>$dt,
                            'flagGranted'   =>1,
                            'activityName'  =>$activityName,
                            'num'           =>$No
                        ],
                        ['ticketSerialNo'=>$temp_ticket['ticketSerialNo'], 'userId'=>$temp_ticket['userId'], 'flagGranted'=>0]);
                    if($rs !== true) {
                        $ticketGot[$temp_ticket['ticketSerialNo']] = $temp_ticket;
                        break;
                    }else {
                        $retry--;
                        if($retry==0) {
                            error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].'第 '.$No.' 张票 取票超出最大重试次数');
                        }
                        continue;
                    }
                }catch(\ErrorException $e) {
                    // 如果是另一个进程也在给这个人发票，或者出现异常 回滚。
                    if (\Sooh\DB\Broker::errorIs($e)){
                        error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].'第 '.$No.' 有另一个进程在同时取票');
                    }else{
                        error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].'第 '.$No.' 出现异常');
                    }
                    $retry = 0;
                    break;
                }
            }

            if($retry == 0) {
                error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].' 开始回滚');
                $this->rollbackTicket($ticketGot, $userInfo);
                $ticketGot = [];
                break;
            }

            $num--;
        }
        return $ticketGot;
    }

    /**
     * 票回滚
     * @param $tickets
     * @param $userInfo
     */
    protected function rollbackTicket ($tickets, $userInfo) {
        if(empty($tickets)) {
            return;
        }
        error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].' 需要回滚的票( '.sizeof($tickets).'张 ):'.implode('、', array_keys($tickets)));
        $failedRollbacek = [];
        foreach($tickets as $t) {
            $retry = 5;
            while($retry){
                $rs = $this->db_rpt->updRecords($this->tbname,
                    [
                        'userId'        => $t['userId'],
                        'realname'      => '',
                        'phone'         => 0,
                        'createTime'    => 0,
                        'flagGranted'   => 0,
                        'num'           => 0,
                        'activityName'  => '',
                    ],
                    ['ticketSerialNo'=>$t['ticketSerialNo'], 'userId'=>$userInfo['customer_id'], 'flagGranted'=>1]);
                if($rs !== true) {
                    break;
                }
                $retry--;
            }
            if($retry == 0) {
                $failedRollbacek[] = $t['ticketSerialNo'];
                error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].' '.$t['ticketSerialNo'].' 回滚失败！');
            }
        }

        if(empty($failedRollbacek)) {
            error_log('error###'.$userInfo['customer_id'].' '.$userInfo['customer_realname'].' 全部回滚成功！');
        }
    }

    /**
     * 发短信
     * @param $phone
     * @param $msg
     * @return bool
     */
    protected function sendMsg($phone, $msg) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }


    /**
     * 给运管发送短信
     * @param $left
     */
    protected function sendMsgToAdmin ($left){
        foreach($this->arr_admin as $p => $name) {
            $msg = str_replace(['{admin}', '{remain}'], [$name, $left], $this->msg2admin);
            $this->sendMsg($p, $msg);
        }

        reset($this->arr_admin);
    }

}
