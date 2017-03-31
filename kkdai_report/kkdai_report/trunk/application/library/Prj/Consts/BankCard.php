<?php
namespace Prj\Consts;
/**
 * BankCard
 *
 * @author simon.wang
 */
class BankCard {
	/**
	 * 放弃的
	 */
	const abandon = -1;
	/**
	 * 待验证的
	 */
	const checking = 0;
	/**
	 * 禁用的
	 */
	const disabled = 4;

	/**
	 * 启用的
	 */
	const enabled = 16;

    public static $enum = array(
        self::abandon=>'放弃的',
        self::checking=>'待验证的',
        self::disabled=>'禁用的',
        self::enabled=>'启用的',
    );
}
