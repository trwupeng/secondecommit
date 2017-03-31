<?php
namespace Prj\Consts;

/**
 * 商品的状态码
 *
 * @author simon.wang
 */
class Wares {
	/**
	 * 正式上架后撤销的
	 */
	const status_break=-2;
	/**
	 * 没正式上架的撤销的
	 */
	const status_abandon=-1;
	/**
	 * 新建
	 */
	const status_new=0;
	/**
	 * 等待上架
	 */
	const status_ready=10;
	/**
	 * 上架募集中
	 */
	const status_open=11;
	/**
	 * 募集结束
	 */
	const status_go=12;
	/**
	 * 还款中
	 */
	const status_return=13;
	/**
	 * 还款结束
	 */
	const status_close=20;
	/**
	 * 以下status 是快快的标的
	 * 已经流标
	 */
	const status_failure = 4011;
	/**
	 * 已申请
	 */
	const status_applyed = 4002;
	/**
	 * 提交
	 */

	const status_commit = 4003;
	/**
	 * 已复核
	 */
	const status_rezhenged = 4004;
	/**
	 * 已终审
	 */
	const status_checked = 4005;
	/**
	 * 标的初建
	 */
	const status_initial_a = 5001;
	/**
	 * 流标处理中
	 */
	const status_liubiao_ing = 5007;
	/**
	 * 逾期
	 */
	const status_yuqi = 5008;

    /**
     * 固定
     */
    const shelf_static = 2000;
    /**
     * 固定+浮动
     */
    const shelf_static_float = 3000;
    /**
     * 浮动
     */
    const shelf_float = 4000;
    /*
    public static $typeNameArr = array(
        '省心计划',
        '新手专享'
    );
    */

	/** 快快贷产品表在报表转换后的所有状态 */
	public static $kkdStatusCode = [
			0 => '新建',
			10 =>'待发布',
			11 => '招标中',
			12 => '满标',
			13 => '还款中',
			20 => '还款已还清',
			4002=>'已申请',
			4003=>'提交',
			4004=>'已复核',
			4005=>'已终审',
			4011 => '流标',
			5007 => '流标处理中',
			5008 => '逾期',
	];

	public static function returnStatus($statusCode=null) {
		if(!empty($statusCode)){
			if(isset(self::$kkdStatusCode[$statusCode])){
				return self::$kkdStatusCode[$statusCode];
			}else {
				return $statusCode;
			}
		}else {
			return self::$kkdStatusCode;
		}
	}


    public static $enum = array(
        self::status_open => "上架募集中",
        self::status_new => "等待审核",
        self::status_ready => "等待上架",
        self::status_abandon =>"被驳回",
        self::status_go => "募集结束",
    );

    public static $shilfIdName = array(
        self::shelf_static => '固定',
        self::shelf_static_float => '固定+浮动',
        self::shelf_float => '浮动',
    );
    /**
     * 网关错误
     */
    const gw_error = -2;
    /**
     * 网关已受理
     */
    const gw_wait = 2;
    /**
     * 网关处理成功
     */
    const gw_success = 1;
    /**
     * 网关处理失败
     */
    const gw_failed = -1;

    public static $gwEnum = [
        self::gw_failed=>'网关错误',
    ];
}
