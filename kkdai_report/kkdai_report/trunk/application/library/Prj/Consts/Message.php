<?php
namespace Prj\Consts;

/**
 * 消息通知
 * Class Message
 * @package Prj\Consts
 * @author LTM <605415184@qq.com>
 */
class Message {

	/**
	 * 未读状态
	 */
	const status_unread = 0;

	/**
	 * 已读状态
	 */
	const status_read = 1;

	/**
	 * 已经删除-废弃
	 */
	const status_abandon = -1;

	/**
	 * 投标
	 */
	const type_bid = 1;

	/**
	 * 合同下发
	 */
	const type_contractIssued = 2;

	/**
	 * 项目回款
	 */
	const type_repayment = 3;

	/**
	 * 提现
	 */
	const type_withdrawal = 4;

	/**
	 * 红包
	 */
	const type_redPacket = 5;

	/**
	 * 返利
	 */
	const type_rebate = 6;
}