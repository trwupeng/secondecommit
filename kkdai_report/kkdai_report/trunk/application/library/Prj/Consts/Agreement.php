<?php
namespace Prj\Consts;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/13
 * Time: 11:51
 */
class Agreement {
	/**
	 * 注册协议
	 */
	const type_register = 1;

	/**
	 * 投资协议
	 */
	const type_invest = 2;

	/**
	 * 启用协议
	 */
	const status_enable = 1;
	/**
	 * 禁用协议
	 */
	const status_disable = 0;

	/**
	 * 显示模版-html
	 */
	const tpl_html = 1;
	/**
	 * 显示模版-excel
	 */
	const tpl_excel = 2;
}