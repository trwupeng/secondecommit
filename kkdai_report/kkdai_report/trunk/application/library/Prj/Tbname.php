<?php
namespace Prj;
/**
 * 定义一些数据库，表的常量，便于代码查找管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Tbname {
	public static function getItemsEnum()
	{
		return array(
			'ShopPoint'=>'积分',
			'VoucherInterest'=>'本金券'
		);
	}
	
	const db_fore = 'mssql';
	const db_rpt = 'dbForRpt';
	const db_log = 'dbForLog';
	const db_api = 'dbForLog';
	
	/**
	 * 管理员账号表
	 */
	const tb_manager='db_rpt.tb_managers';
	/**
	 * 杨森要的每年12个月的报表用的
	 */
	const tbsum_allusr_daily='db_rpt.tbsum_allusr_daily';
	/**
	 * 杨森要的每年12个月的报表用的
	 */
	const tbrpt_basic_month='db_rpt.tbrpt_basic_month';
	
	const tbsum_allusr_dayonce='db_rpt.tbsum_allusr_dayonce';
	/**
	 * 每天统计的：事件-客户端-推广活动
	 */
	const tbsum_evt_daily='db_rpt.tbrpt_actclientbiz_daily';
	/**
	 * TODO:每月统计的：事件-客户端-推广活动
	 */
	const tbsum_evt_monthly='db_rpt.tbrpt_actclientbiz_monthly';
	/**
	 * 在线情况统计
	 */
	const tb_online = 'db_rpt.tb_online';
	/**
	 * 用户最终状态
	 */
	const tb_user_final='db_rpt.tb_user_final';
	/**
	 * 用户各类产品购买的最终状态
	 */
	const tb_user_buy_daily='db_rpt.tb_user_buy_daily';
	/**
	 * 配置表
	 */
	const tb_config = 'db_rpt.tb_config';
	/**
	 * 合作商列表
	 */
	const tb_copartners = 'db_rpt.tb_copartners';	
	/**
	 * 合作商协议列表
	 */
	const tb_contracts = 'db_rpt.tb_contracts';	
	/**
	 * 成功订单表
	 */
	const tb_order_final = 'db_rpt.tb_orders_final';
	/**
	 * 发布的产品的列表
	 */
	const tb_products_final = 'db_rpt.tb_products_final';	
}
