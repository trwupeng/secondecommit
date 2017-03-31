<?php
namespace Prj\Consts;

/**
 * 订单的状态
 *
 * @author simon.wang
 */
class OrderStatus {
    /**
     * 进行中的订单
     */
    public static $running = [
        self::waiting, //2
        self::waitingGW,//3
        self::payed, //8
        self::going, //10
        self::igoing, //21
        self::delay, //20
        self::advanced, //38
        self::done, //39
    ];

	/**
	 * 初建（保留）
	 */
	const created=0;
	/**
	 * 流标
	 */
	const flow = -3;
	/**
	 * 中断，废弃的（系统状态）
	 */
	const abandon=-1;
    /**
     * 支付失败
     */
    const failed=4;



	/**
	 * 订单已受理，等待处理结果
	 */
	const waiting=2;

    /**
     * 订单已受理，等待支付网关处理结果
     */
    const waitingGW=3;

	/**
	 * 支付成功,起息前
	 */
	const payed=8;

	/**
	 * 起息后，回款中
	 */
	const going=10;
    /**
     * 正常回款（延期由平台垫付）
     */
    const igoing=21;
	/**
	 * 延期回款中
	 */
	const delay=20;
	/**
	 * 提前还款
	 */
	const advanced=38;
	/**
	 * 结束：已全部回款，提现成功，充值成功
	 */
	const done=39;
	
	/**
	 * 充值订单专用:成功充值，但需要更新用户的钱包余额
	 */
	const nextUpdateUserWallet=37;


	public static $enum = array(
        self::abandon=>'废弃',
        self::advanced=>'提前还款',
        self::created=>'废弃',
        self::delay=>'延期回款中',
        self::done=>'结束：已全部回款',
        self::failed=>'支付失败',
		self::going=>'放款成功',
        self::igoing=>'正常回款（延期由平台垫付）',
        self::payed=>'支付成功',
        self::waiting=>'购买成功(等待网关)',
        self::waitingGW=>'网关处理中...',
        self::done=>'已还清',
		self::flow=>'流标',
		607 => '退款成功',
		608 => '投标失败',
		606 => '等待退款',
    );

	/** 所有成功状态 */
	public static $succOrderStatus = [
		self::payed,
		self::going,
		self::done,
	];

	/**
	 * 订单的最终状态
	 */
	public static $finalOrderStatus = [
		self::done, // 还款已经还清
		self::flow, // 流标
		607, // 退款成功 (快快贷从库中的状态码)
		608, // 投标失败 (快快贷从库中的状态码)

	];

}
