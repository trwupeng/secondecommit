<?php
namespace Prj\Consts;
/**
 * 还款方式
 *
 * @author simon.wang
 */
class InvestReturn {
	/**
	 * 待定
	 */
	const unknown=0;
	/**
	 * 一次性还本付息
	 */
	const once = 1;
	/**
	 * 按月付息，到期还本
	 */
	const mInterest=2;
	public static $enum=[
		self::unknown => '待定',
		self::once => '一次性还本付息',
		self::mInterest=>'按月付息，到期还本',
	];
}
