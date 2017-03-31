<?php
namespace Prj\Consts;
/**
 * 起息方式
 *
 * @author simon.wang
 */
class InterestStart {
	/**
	 * 0:购买起息
	 */
	const whenBuy=0;
	/**
	 * 1，购买次日起息
	 */
	const afterBuy=1;
	/**
	 * 2:募集满起息
	 */
	const whenFull=2;
	/**
	 * 3:募集满次日起息
	 */
	const afterFull=3;

    public static $enum = array(
        self::whenBuy=>'购买起息',
        self::afterBuy=>'购买次日起息',
        self::whenFull=>'募集满起息',
        self::afterFull=>'募集满次日起息',
    );
}
