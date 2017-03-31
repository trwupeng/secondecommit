<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/20
 * Time: 16:35
 */
namespace Rpt\Misc\Base;

class Daily {
    public $db = null;
    public $tb = 'db_kkrpt.tb_finance_0';
    public $dbConf = 'dbForRpt';
    public $key = '';
    public $ymd = 0;
    static $result = [];
    protected $configMap = [
        'FinanceK'=>'amountK',
        'FinanceM'=>'amountM',
        'FinanceXS'=>'amountX',
        'FinanceXX'=>'amountG',
    ];
    protected $typeMap = [
        'FinanceK'=>\Prj\Consts\Finance::type_kkd,
        'FinanceM'=>\Prj\Consts\Finance::type_my,
        'FinanceXS'=>\Prj\Consts\Finance::type_xstb,
        'NewFinanceK'=>\Prj\Consts\Finance::type_kkd,
        'NewFinanceM'=>\Prj\Consts\Finance::type_my,
        'NewFinanceXS'=>\Prj\Consts\Finance::type_xstb,
    ];

    public static function getCopy($tbName){
        $o = new Daily();
        $o->tb = $tbName;
        $o->db = \Sooh\DB\Broker::getInstance($o->dbConf);
        return $o;
    }

    public function getTotal($col,$where){
        if(is_array($col))$col = implode('+',$col);
        $rs = $this->db->getRecord($this->tb,'sum('.$col.') as total',$where);
        return $rs['total']-0;
    }

    public function save($key,$col,$where,$date,$extFlg1=0,$extFlg2=0){
        $total = $this->getTotal($col,$where);
        self::setlogs($this->ymd.'.'.$this->key.'.'.$extFlg1.'.'.$total/100);
        $tmp = \Rpt\EvtDaily\Base::getCopy($key);
        $tmp->reset();
        $tmp->add($total/100,'','',$extFlg1,$extFlg2);
        $tmp->save($this->db,$date);

        return $this;
    }

    public function  saveLite($total,$extFlg1 = 0){
        self::setlogs($this->ymd.'.'.$this->key.'.'.$extFlg1.'.'.$total/100);
        $tmp = \Rpt\EvtDaily\Base::getCopy($this->key);
        $tmp->reset();
        $tmp->add($total/100,'','',$extFlg1);
        $tmp->save($this->db,$this->ymd);

        unset($tmp);
    }

    protected static function setlogs($str){
        self::$result[] = $str;
    }

    public static function submit($key,$ymd,$extFlg1 = 0){
        $tmp = self::getCopy('');
        $tmp->key = $key;
        $tmp->ymd = $ymd;
        if(in_array($key,['FinanceK','FinanceM','FinanceXS'])){
            $tmp->tb = 'db_kkrpt.tb_finance_0';
            $tmp->doFinance($extFlg1,$tmp->typeMap[$key]);
            return;
        }elseif(in_array($key,['FinanceXX','NewFinanceXX'])) {
            $tmp->tb = 'db_kkrpt.tb_finance_ground_0';
        }elseif(in_array($key,['NewFinanceK','NewFinanceM','NewFinanceXS'])){
            $tmp->tb = 'db_kkrpt.tb_finance_0';
            $tmp->doNewFinance($extFlg1,$tmp->typeMap[$key]);
            return;
        }else{
            throw new \ErrorException('unknown_key');
        }
        if(!method_exists($tmp,'do'.$key)){
            throw new \ErrorException('unknown_method');
        }else{
            $method = 'do'.$key;
            $tmp->{$method}($extFlg1);
        }
    }

    protected function doFinance($extFlg1 = 100,$type = \Prj\Consts\Finance::type_kkd){
        if($extFlg1==100){
            $this->save($this->key,'income',['type'=>$type,'date'=>$this->ymd],$this->ymd,$extFlg1);
        }elseif($extFlg1==101){
            $this->save($this->key,'payment',['type'=>$type,'date'=>$this->ymd],$this->ymd,$extFlg1);
        }elseif($extFlg1==102){
            $total = \Prj\Data\Config::get($this->configMap[$this->key])+\Prj\Data\Finance::getRemainAfter($this->ymd,$type);
            $this->saveLite($total,$extFlg1);
        }else{
            //var_log($this->key.' #extFlg1# '.$extFlg1.' #被忽略>>>');

        }
    }

    protected $income = ['userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest','borrowerAgency','incomeOT'];

    protected $payment = ['payLoan','payInterest','payAmount','payMargin','payAgency','payOT'];

    protected function doFinanceXX($extFlg1 = 100){
        $extFlgs = ['103','104','105','106','107','108','109','110','111','112','113','114','115'];
        $income = $this->income;
        $payment = $this->payment;
        $remainMap = array_flip(\Prj\Data\FinanceGround::$mapArr);
        $arrMap = array_combine($extFlgs,array_merge($income,$payment));

        if($extFlg1==100){
            $this->save($this->key,$income,['date'=>$this->ymd],$this->ymd,$extFlg1);
        }elseif($extFlg1==101){
            $this->save($this->key,$payment,['date'=>$this->ymd],$this->ymd,$extFlg1);
        }elseif(in_array($extFlg1,$extFlgs)){ //uAmountRemain 投资人本金存量
            $config = \Prj\Data\Config::get($arrMap[$extFlg1]);
            $remain = \Prj\Data\FinanceGround::getRemainsAfter($this->ymd)[$remainMap[$arrMap[$extFlg1]]];
            if($this->ymd=='20160125')var_log($config+$remain,$this->ymd.'>>>'.$extFlg1);
            $this->saveLite($config+$remain,$extFlg1);
        }else{
            //var_log($this->key.' #extFlg1# '.$extFlg1.' #被忽略>>>');

        }
    }

   protected function doNewFinance($extFlg1,$type = \Prj\Consts\Finance::type_kkd){
       if($extFlg1==0){
           $newTotal = \Prj\Data\Finance::getRemainAfter($this->ymd,$type)-\Prj\Data\Finance::getRemainBefore($this->ymd,$type);
           $this->saveLite($newTotal);
       }
    }

    protected function doNewFinanceXX($extFlg1){
        if($extFlg1==0){
            $remainsBefore = \Prj\Data\FinanceGround::getRemainsBefore($this->ymd);
            $remainsAfter = \Prj\Data\FinanceGround::getRemainsAfter($this->ymd);
            $newTotal = array_sum($remainsAfter)-array_sum($remainsBefore);
            $this->saveLite($newTotal,$extFlg1);
        }
    }
}

