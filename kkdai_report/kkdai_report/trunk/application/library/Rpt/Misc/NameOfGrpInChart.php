<?php
namespace Rpt\Misc;
/* 
 * 日报中图片报表中二级分组对应名称的处理
 */
class NameOfGrpInChart{
	public static function copartnerId($val,$forACT=null)
	{
		$s = \Sooh\DB\Broker::getInstance()->getOne('tb_copartner_0', 'copartnerName',['copartnerId'=>$val]);
		return empty($s)?"自然流量":$s;
	}
	public static function clienttype($val,$forACT=null)
	{
		return \Prj\Consts\ClientType::clientTypes($val);
	}
	public static function flgext01($val,$forACT=null)
	{
		if(substr($forACT, 0,3)=='Buy' || substr($forACT, 0,4)=='Prdt') {
            return self::flgext_prdtType($val);
        }elseif(substr($forACT, 0,7)=='Finance'){
            return self::flgext01_finance($val);
        }else{
			$f = 'flgext01_'.strtolower($forACT);
			return self::$f($val);
		}
	}
	public static function flgext02($val,$forACT=null)
	{
		if(substr($forACT, 0,3)=='Buy' || substr($forACT, 0,5)=='NewRe' || substr($forACT, 0,5)=='Accou'){
			return self::flgext01_accounts($val);
		}else{
			throw new \ErrorException('unknown flgext02 for '.$forACT);
		}
	}
	protected static function flgext_prdtType($val)
	{
		switch($val){
			case 0: return '天天赚';
			case 1:return '定期宝';
			case 2:return '房宝宝';
			case 5:return '精英宝';
			default : return '产品code'.$val;
		}
	}
	protected static function flgext01_accounts($val)
	{
		switch ($val){
			case 3:return '推荐用户';
			case 2:return '内部员工';
			case 1:return '◎系统◎';
			default: return '普通用户';
		}
	}

    protected static function flgext01_finance($val)
    {
        switch($val){
            case 100:return '收入';
            case 101:return '支出';
            case 102:return '存量';
            case 103:return '投资人本金存量';
            case 104:return '借款人还借款本金存量';
            case 105:return '借款人服务费存量';
            case 106:return '借款人保证金存量';
            case 107:return '借款人贷款利息存量';
            case 108:return '中介费存量';
            case 109:return '其它收入存量';
            case 110:return '借款人贷款金额存量';
            case 111:return '支付投资人理财利息存量';
            case 112:return '退还投资人本金存量';
            case 113:return '退还借款人保证金存量';
            case 114:return '中介返佣存量';
            case 115:return '其它支出存量';
            default: return '收入*';
        }
    }
}
