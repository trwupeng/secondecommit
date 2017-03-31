<?php
namespace Prj\Misc;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/12/14
 * Time: 11:34
 */
class Christmas {
    const debug = true; //debug模式
    const token = 'd3LUo5EwmkQ4RLwZ'; //salt
    const cd = 900; //浇灌金额900s
    const source = '圣诞狂欢'; //红包备注
    const awardVal = 5; //浇灌奖励
    public static $userId;
    public static $pid; //随机码
    public static $date = ['20161220','20170106']; //活动起始日期
    /**
     * 一天的各个阶段
     * @var array
     */
    public static $collectTime = [
        'rain' => ['雨露浇灌','000000','100000','雨水喝的饱，宝宝长得好！'],
        'sun' => ['阳光普照','100000','180000','晒太阳，暖洋洋！'],
        'soil' => ['松土施肥','180000','240000','你挠的我好痒呀！'],
    ];
    /**
     * 奖品设置
     * 2元 => [产品类型,起投金额]
     * @var array
     */
    public static $redConfig = [
        2 => ['1',1000],
        6 => ['1',3000],
        10 => ['1',5000],
        18 => ['1',8500],
        20 => ['1',10000],
        58 => ['1',26000],
        75 => ['1',37500],
        88 => ['1',38000],
        100 => ['1',42000],
    ];

    /**
     * 奖励规则
     * 营养值区间=>[投资满足(元)=>[红包金额(元)]]
     * @var array
     */
    public static $redRule = [
        '100-299' => [2],

        '300-499' => [6],
        '500-999' => [10],
        '1000-1999' => [20],

        '2000-2999' => [20,20,10],
        '3000-4999' => [75],
        '5000-5999' => [100,10,18],

        '6000-7999' => [58,100],
        '8000-9999' => [100,100,18],
        '10000' => [100,100,88],
    ];

    public static function checkDebug(){
        if(!in_array(\Sooh\Base\Ini::getInstance()->get('deploymentCode'),[10,20]))return false;
        return self::debug;
    }

    protected static function getApi(){
        //return 'http://localhost/Christmas/test?__VIEW__=json&jskey=78B5086948786C0471CB478DF1A2CDF4';
        return \Sooh\Base\Ini::getInstance()->get('uriBase')['kkd_sendRed'];
    }

    public static function log($msg){
        if(!self::$pid)self::$pid = mt_rand(1000,9999);
        error_log('sd['.self::$pid.']>>>userId:'.self::$userId.'#msg:'.$msg);
    }

    public static function sendRedPacket($tid , $userId , $yuan){
        $red = self::$redConfig[$yuan];
        if(!$red)throw new \ErrorException('无效的红包金额');
        $api = self::getApi();
        $token = self::token;
        $data = [
            'amount' => $yuan * 100,
            'customerId' => $userId,
            'days' => 30, //有效期
            'lowestAmount' => $red[1], //起投金额
            'productType' => $red[0], //适用产品 1定期宝，2房宝宝，5赎楼贷，6理财计划；多个产品以英文逗号分隔；不填为无限定
            'source' => self::source, //奖励来源
            'type' => 1 //1—抵现券，2—加息券
        ];
        asort($data,SORT_STRING);
        $str = implode('',$data);
        $data['signature'] = md5($str.$token);
        $data['tid'] = $tid;
        if(!self::checkDebug()){
            $ret = \Prj\Misc\Funcs::curl_post($api , $data);
            if(empty($ret)){
                self::log('http#retry>>>');
                usleep(500000);
                $ret = \Prj\Misc\Funcs::curl_post($api , $data);
            }
        }else{
            sleep(3);
            $ret = '{"code":0,"message":"success"}';
        }

        self::log('#sendRedPacket>>>api:'.$api);
        self::log('#sendRedPacket>>>api:'.json_encode($data));
        self::log('#sendRedPacket>>>ret:'.$ret);
        $result = json_decode($ret , true);
        $result['code'] = $result['code'] == 501 ? 0 : $result['code'];
        return $result;
    }

    /**
     * 用户领取圣诞树的奖励
     * @param \Prj\Data\HdUser $user
     */
    public static function award(\Prj\Data\HdUser $user){
        $awards = self::getAward($user->getHP());
        $userId = $user->getField('customerId');
        if(!$awards)return self::returnError('没有可以领取的红包');
        if(!$user->lock('award_lock',3))return self::returnError('提交太频繁,请稍后重试');
        $hp = $user->getHP();
        $investAmount = self::getInvestAmount($userId);
        $oldHP = $user->getField('normalHP');
        $hplog = \Prj\Data\HdHPLog::add($userId , $oldHP , -1 * $hp , \Prj\Data\HdHPLog::type_award);
        if(!$hplog)return self::returnError('系统正忙,请稍后重试');
        self::log('hplog create...');
        $redlogs = [];
        foreach ($awards as $v){
            $amount = $v['amount'];
            $tmp = \Prj\Data\HdRedLog::add($userId , $amount , $hp , $investAmount , $hplog->getField('id'));
            if($tmp){
                $redlogs[] = $tmp;
                self::log('1.redlog create...');
            }else{
                self::rollBack($hplog , $redlogs , 'redlog 插入失败');
                return self::returnError('系统正忙,请稍后重试');
            }
        }
        $user->setField('normalHP',$user->getField('normalHP') - $hp);
        try{
            $hplog->setField('statusCode' , 1);
            $hplog->setField('exp' , '正常');
            $hplog->update();
            self::log('2.hplog save success...');
            $user->update();
            self::log('3.user save success...');
        }catch (\ErrorException $e){
            self::rollBack($hplog , $redlogs , '用户更新失败#'.$e->getMessage());
            return self::returnError('系统正忙,请稍后重试');
        }
        //发券请求
        foreach ($redlogs as $redlog){
            $error = null;
            try{
                $ret = self::sendRedPacket($redlog->getField('id') , $userId , round($redlog->getField('amount') / 100));
                if($ret['code'] !== 0)$error = 'msg:'.$ret['message'];
            }catch (\ErrorException $e){
                $error = 'msg:'.$e->getMessage();
            }
            if($error){
                self::log('error#send red failed...'.$error);
                $redlog->setField('statusCode',-8);
                $redlog->setField('exp',$error);
            }else{
                self::log('4.send red success...');
                $redlog->setField('statusCode',1);
                $redlog->setField('exp','正常');
            }
            try{
                $redlog->update();
                self::log('5.redlog save success...');
            }catch (\ErrorException $e){
                self::log("###重大错误#redlog rollBack failed # ".$e->getMessage());
            }
        }
        self::log('6.award finish...');
    }

    protected static function rollBack($hplog = null , $redlogs = null , $msg = ''){
        self::log('rollback start...'.$msg);
        if($hplog){
            $hplog->setField('statusCode',-4);
            $hplog->setField('exp',$msg);
            try{
                $hplog->update();
            }catch (\ErrorException $e){
                self::log("###重大错误#hplog rollBack failed #id:".$hplog->getField('id')."#msg:".$e->getMessage());
            }
        }
        if($redlogs){
            if(is_array($redlogs)){
                foreach ($redlogs as $redlog){
                    $redlog->setField('statusCode',-4);
                    $redlog->setField('exp',$msg);
                    try{
                        $redlog->update();
                    }catch (\ErrorException $e){
                        self::log("###重大错误#redlog rollBack failed #id:".$hplog->getField('id')."#msg:".$e->getMessage());
                    }
                }
            }else{
                $redlogs->setField('statusCode',-4);
                $redlogs->setField('exp',$msg);
                try{
                    $redlogs->update();
                }catch (\ErrorException $e){
                    self::log("###重大错误#redlog rollBack failed #id:".$hplog->getField('id')."#msg:".$e->getMessage());
                }
            }
        }
    }

    protected static function returnError($msg , $code = 400){
        throw new \ErrorException($msg , $code);
    }

    /**
     * 获取投资额(单位分)
     * @return int
     */
    public static function getInvestAmount($userId){
        if(self::checkDebug())return 8888.88 * 100;
        list($start , $end) = self::$date;
        $realAmount = \Prj\Data\MiscData::getDingQiBaoAmount($userId , $start , $end)['realAmount'] - 0;
        return $realAmount;
    }

    public static function getInvestHP($userId){
        $amount = self::getInvestAmount($userId);
        $hp = floor($amount/100000) * 100;
        return $hp - 0;
    }

    public static function getAward($hp){
        $result = [];
        foreach (self::$redRule as $k => $v){
            list($start , $end) = explode('-',$k);
            if($end === null)$end = $hp + 1;
            //营养值符合条件.
            if($hp >= $start && $hp <= $end){
                $result = $v;
                break;
            }else{
                continue;
            }
        }
        $ret = [];
        foreach ($result as $v){
            $tmp = self::$redConfig[$v];
            if(empty($tmp))self::log('error#['.$v.'元]无效的红包设置!!!');
            if(!$tmp)continue;
            $arr['type'] = $tmp[0];
            $arr['limitAmount'] = $tmp[1];
            $arr['amount'] = $v * 100;
            $ret[] = $arr;
        }
        return $ret;
    }
}