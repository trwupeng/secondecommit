<?php
namespace Prj\Consts;
/**
 * 订单类型，投资的，充值的，提现的，券的
 *
 * @author simon.wang
 */
class OrderType {
	const investment=10;//投资
	const recharges=20;//充值
	const withdraw=30;//提现
	const payback=40;//回款
	const binding=70;//绑卡
	const vouchers=90;//券
	public static function classFor($orderId)
	{
		switch (substr($orderId,0,2)){
			case self::investment:return '\Prj\Data\Investment';
			case self::recharges:return '\Prj\Data\Recharges';
			case self::vouchers:return '\Prj\Data\Vouchers';
			case self::payback:return '\Prj\Data\Payback';
			default: throw new \ErrorException('unknown orders');
		}
	}

    public static $enum = [
        self::investment=>'投资',
        self::recharges=>'充值',
        self::withdraw=>'提现',
        self::payback=>'回款',
        self::binding=>'绑卡',
        self::vouchers=>'券',
    ];
}
