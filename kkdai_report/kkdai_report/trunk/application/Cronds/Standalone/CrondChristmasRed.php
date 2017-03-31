<?php
namespace PrjCronds;
/**
 * 检查发送失败的红包,重发
 *  php /var/www/fk_php/run/crond.php "__=crond/run&task=Standalone.CrondChristmasRed" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
use Prj\Misc\Christmas as Chris;
class CrondChristmasRed extends \Sooh\Base\Crond\Task{
    public function init() {
        parent::init();
        $this->toBeContinue=true;
        $this->_secondsRunAgain=600;//每10分钟执行一次
        $this->_iissStartAfter=100;//每小时01分后启动
        $this->ret = new \Sooh\Base\Crond\Ret();
    }
    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        $request = \Prj\Misc\ViewFK::$_request;
        list($start , $end) = \Prj\Misc\Christmas::$date;
        if($request && $request->get('type') == 'awardAll'){
            if($dt->YmdFull <= $end && !Chris::checkDebug()){
                error_log('###[warning]圣诞树活动红包领取清扫###活动尚未结束'.$dt->YmdFull.':'.$start.'-'.($end+1));
                $this->toBeContinue = false;
                return true;
            }
            $this->awardAll(); //帮每个人领取红包
            return true;
        }
        if(($dt->YmdFull < $start || $dt->YmdFull > $end+1) && !Chris::checkDebug()){
            error_log('###[warning]圣诞树活动红包发放检查###非活动时间'.$dt->YmdFull.':'.$start.'-'.($end+1));
            $this->toBeContinue = false;
            return true;
        }
        if($this->_isManual){
            $m='manual';
        }else{
            $m='auto';
        }
        if($this->_counterCalled==1){
            error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
        }else{
            error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
        }
        $this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        $this->updateData();
        return true;
    }

    protected function updateData(){
        error_log('###[warning]圣诞树活动红包发放检查#start###');
        $cache = \Prj\Misc\CacheFK::getCopy('CrondChristmasRed');
        $info = (array)$cache->getData();
        $info['lastYmd'] = date('YmdHis');
        $rs = \Prj\Data\HdRedLog::getRecords(['statusCode' => -8] , 'rsort createYmd' , 100);
        if($rs){
            foreach ($rs as $v){
                Chris::$userId = $v['customerId'];
                $ret = Chris::sendRedPacket($v['id'] , $v['customerId'] , floor($v['amount'] / 100));
                $code = $ret['code'];
                $msg = $ret['message'];
                $redlog = \Prj\Data\HdRedLog::getCopy($v['id']);
                $redlog->load();
                if($redlog->exists()){
                    $exp = $redlog->getField('exp');
                    $tmp = explode('|',$exp);
                    $tmp[1] = 'msg:'.$msg;
                    $newExp = implode('|',$tmp);
                    $redlog->setField('exp',$newExp);
                    $statusCode = $code === 0 ? 1 : -8;
                    if($statusCode == 1)$info['num']++;
                    $redlog->setField('statusCode' , $statusCode);
                    try{
                        $redlog->update();
                    }catch (\ErrorException $e){
                        Chris::log('redlog update errror#msg:'.$e->getMessage());
                    }
                }else{
                    Chris::log('error#redlog unexists!');
                }
            }
        }
        $cache->save($info);
        error_log('###[warning]圣诞树活动红包发放检查#end###');
    }

    protected function awardAll(){
        set_time_limit(0);
        error_log('###[warning]圣诞树活动红包领取清扫#start###');
        $rs = \Prj\Data\HdUser::getRecords([] , 'sort lastUpdateYmd' , 5000);
        //var_log($rs ,'>>>');
        foreach ($rs as $v){
            $userId = $v['customerId'];
            $user = \Prj\Data\HdUser::getCopy($userId);
            $user->load();
            Chris::$userId = $userId;
            if($user->exists()){
                $investHP = Chris::getInvestHP($userId);
                if($investHP > $user->getField('investHP')){
                    $user->setField('investHP' , $investHP);
                    Chris::log('同步用户的投资营养值');
                }
                $user->setField('lastUpdateYmd' , date('YmdHis'));
                try{
                    $user->update();
                }catch (\ErrorException $e){
                    Chris::log('error#'.$e->getMessage());
                }
                if($user->getHP() >= 100){
                    //发送红包
                    try{
                        $ret = Chris::award($user);
                    }catch (\ErrorException $e){
                        Chris::log($e->getMessage());
                    }
                }
            }else{
                Chris::log('error#用户不存在');
            }
        }
        error_log('###[warning]圣诞树活动红包领取清扫#end###');
    }

}
