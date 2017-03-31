<?php
namespace Prj\Misc;
/**
 * 订单计算器
 *
 * @author simon.wang
 */
class OrdersCalc {
	/**
	 * 
	 * @param \Prj\Data\Wares $wares
	 * @param number $amount 投资金额
	 * @param number $amountExt 优惠券金额
	 * @param number $amountFake
	 * @return ['interest'=>13.23, 'interestExt'=>'3.23','extDesc'=>''];
	 */
	public static function interest ($wares,$amount,$amountExt,$amountFake,$addYield = 0)
	{
		$ret = ['extDesc'=>''];
		$ret['amountExt']=$amountExt;
		$ret['amountFake']=$amountFake;
		$ret['amount']=$amount;
		$interestType = $wares->getField('interestStartType');
		$dtEnd = $wares->getField('ymdPayPlan'); //tgh 时间戳转换
		switch($interestType){
			case \Prj\Consts\InterestStart::whenBuy : 
				$dtStart = \Sooh\Base\Time::getInstance()->timestamp();
				break;
			case \Prj\Consts\InterestStart::afterBuy : 
				$dtStart = \Sooh\Base\Time::getInstance()->timestamp()+86400;
				break;
			case \Prj\Consts\InterestStart::whenFull : 
				$dtStart = $wares->getField('timeEndReal');
				if($dtStart>0){
					$dtStart = strtotime(substr($dtStart,0,8));
				}
				break;
			case \Prj\Consts\InterestStart::afterFull : 
				$dtStart = $wares->getField('timeEndReal');
				if($dtStart>0){
					$dtStart = strtotime(substr($dtStart,0,8))+86400;  //tgh +86400
				}
				break;
		}
		if($dtStart>0){
			$dayDur = ($dtEnd-$dtStart);
			$yield = $wares->getField('yieldStatic');//+$wares->getField('yieldStaticAdd')+$addYield; //券提利率
		    $yieldAdd = $wares->getField('yieldStaticAdd');
            $yieldExt = $addYield;
        }else{
			$dayDur=0;
			$yield=0;
            $yieldAdd = 0;
            $yieldExt = 0;
		}
        $amountTotal = $amount+$amountExt+$amountFake;
        $dayDur = $dayDur>0?$dayDur:0;   //单位秒
		$ret['interestStatic'] = floor($yield * $amountTotal * $dayDur/86400/360);
		$ret['interestAdd'] = floor($yieldAdd * $amountTotal * $dayDur/86400/360);
		$ret['interestExt'] = floor($yieldExt * $amountTotal * $dayDur/86400/360);

		return $ret;
	}


}
