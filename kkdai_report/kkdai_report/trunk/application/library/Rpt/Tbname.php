<?php
namespace Rpt;
/**
 * Rpt数据库，表的常量定义
 * 
 * TODO:管理员账号表 以后待修改，现在用的是分表
 */
class Tbname {
//  从库
    const db_p2p = 'produce';
    const db_nest = 'nest';
    const db_p2ptmp = 'producetmp';
	const customer = 'phoenix.customer';
	const bid = 'phoenix.bid';
	const bid_poi = 'phoenix.bid_poi';
	const account_info= 'phoenix.account_info';
	const business_lending_memo = 'phoenix.business_lending_memo';
	const customer_invite = 'phoenix.customer_invite';
	const yuebao = 'phoenix.yuebao';
	const yuebao_poi = 'phoenix.yuebao_poi';
	const yuebao_customer = 'phoenix.yuebao_customer';
	const recharge_enchashment_water = 'phoenix.recharge_enchashment_water';
	const account_card_binding = 'phoenix.account_card_binding';
	const customer_coupon = 'phoenix.customer_coupon';
	const account_packet = 'phoenix.account_packet';
	const user_behavior = 'phoenix.user_behavior';
	const licai_core_data = 'phoenix.licai_core_data';
	const account_bill = 'phoenix.account_bill';
	const financial = 'nest.finance';
	const order = 'nest.order';

//	报表数据库
	const db_rpt = 'default';
// 用户表
	const tb_user_final = 'db_kkrpt.tb_user_final';
// 临时表 放那些pay_type = APP的老用户对应的客户端类型
	const tb_old_user_clienttype = 'db_kkrpt.tb_old_user_clienttype';
//	日常报表
	const tb_evtdaily = 'db_kkrpt.tb_evtdaily';
//	月报表
	const tb_evtmonthly = 'db_kkrpt.tb_evtmonthly';	
//	订单表
	const tb_orders_final = 'db_kkrpt.tb_orders_final';
//	余额宝订单表
	const tb_yuebao_out = 'db_kkrpt.tb_yuebao_out';
//	余额宝产品
	const tb_yuebao_final = 'db_kkrpt.tb_yuebao_final';
//	余额宝用户表
	const tb_yuebao_user_final = 'db_kkrpt.tb_yuebao_user_final';

//	充值提现表
	const tb_recharges_final = 'db_kkrpt.tb_recharges_final';

//	产品表
	const tb_products_final = 'db_kkrpt.tb_products_final';
	
//	产品表新表
    const tb_financial_final = 'db_kkrpt.tb_financial_final';

// 	银行卡表
	const tb_bankcard_final = 'db_kkrpt.tb_bankcard_final';
// 	券发放使用表
	const tb_vouchers_final = 'db_kkrpt.tb_vouchers_final';
//	红包流水表
	const tb_packet_water = 'db_kkrpt.tb_packet_water';


//	渠道导入量表
	const tb_copartner_worth = 'db_kkrpt.tb_copartner_worth';
// 旧渠道转换表
	const tb_copartners_trans = 'db_kkrpt.tb_copartners_trans';

	const tb_contract = 'db_kkrpt.tb_contract_0';
	const tb_copartner = 'db_kkrpt.tb_copartner_0';

// 百度统计表
	const tb_baidutongji = 'db_kkrpt.tb_baidutongji';
//umeng数据统计
    const tb_umeng_data='db_kkrpt.tb_umeng_data';
	const tb_account_bill='db_kkrpt.tb_account_bill';
}

class Fields {

	public static $produce_fields_user_behavior = ['id','customer_id','bank','channel','summary','create_time'];

	public static $produce_fields_account_packet = ['id','customer_id','amount','type','packet_desc','start_time','end_time'];

	public static $produce_fields_customer_coupon = ['id','customer_id','create_date','begin_date','end_date','amount',
					'title','type','status','source','poi_id','handle_date','channel','max_interst','max_amount','lowest_amount'];

	public static $produce_fields_bid_poid = ['poi_id','bid_id','customer_id','create_time','bid_amount','amount','poi_percent',
					'poi_status','poi_description','expect_amount','pay_amount','channel','bid_type','poi_type'];

	public static $produce_fields_customer = ['customer_id','customer_name','customer_creditgrade','customer_code',
					'customer_type','customer_cellphone','customer_email1','customer_email2','customer_ip',
					'customer_status','customer_source','customer_integral','UsrCustId','UsrId','customer_realname',
					'customer_idno','add_date','pay_type','source','flag','download_source','customer_pinyin',
					'phone_ownership','customer_origin', 'cp_id', 'realname_time'];

	public static $produce_fields_account_info = ['balance','financial_principal', 'interest','punitive_interest','frozen_assets','packet','total_income','bid_income',
					'fbb_principal','experience_principal', 'customer_id'];

	public static $produce_fields_yuebao_poi = ['poi_id','customer_id','yuebao_id','amount','create_date','type','status'];

	public static $produce_fields_yuebao = ['yuebao_id','title','valid_date','end_date','interest','amount','per_day_deposit_amount',
					'per_day_withdraw_amount','per_day_people_deposit_amount','per_day_people_withdraw_amount','customer_id',
					'free_amount','create_date','number','sell_percent','lowest_amount','order_index'];

	public static $produce_fields_yuebao_customer = ['customer_id','total_amount','valid_amount','invalid_amount','income_date',
					'create_date','update_date','type','status'];

	public static $produce_fields_bid = ['bid_id','bid_title','bid_type','bid_create_date','bid_update_date','bid_publish_startdate','product_type',
					'bid_publish_enddate','bid_status','bid_amount','bid_free_amount','bid_interest','bid_interest_type','bid_month_principal',
					'bid_month_interest','bid_percent','bid_period','bid_serviceFee','max_amount','lowest_amount','amount_multiple','bid_amount_line',
					'is_newbie,is_jia_xi,x_interest,x_rate,y_interest,y_rate,bid_period_add'];


	public static $produce_fields_recharge_enchashment_water = ['id','customer_id','amount','trade_type','user_fee','update_time','finish_time',
					'summary','status','flag','card_id','balance','coupon_id','pay_method','channel'];

	public static $produce_fields_account_card_binding = ['binding_id','customer_id','card_no','bank','add_date','binding_date','status','cellphone',
					'channel'];

	public static $produce_fieldds_account_bill = ['bill_id', 'ahead_amount','bid_id','bill_date','bill_num','bill_type','customer_id','interest','lending_id',
			'overhead_charges','payment_date','payment_money','payment_status','penalty_interet','principal','service_charge','shouldpay_date','bid_type','finish',
			'cust_interest','cust_principal','cust_penalty_interet', 'freeze_amount', 'freeze_ord_id', 'unfreeze_ord_id', 'freeze_status',
	];

	
	public static $nest_fields_finance = ['bid_id','bid_title','bid_type','bid_create_date','bid_update_date','bid_publish_startdate','product_type',
					'bid_publish_enddate','bid_status','bid_amount','bid_free_amount','bid_interest','bid_interest_type','bid_month_principal',
					'bid_month_interest','bid_percent','bid_period','bid_serviceFee','max_amount','lowest_amount','amount_multiple','bid_amount_line',
					'is_newbie,is_jia_xi,x_interest,x_rate,y_interest,y_rate,bid_period_add'];
	
	public static $nest_fields_order = ['poi_id','bid_id','customer_id','create_time','bid_amount','amount','poi_percent',
	    'poi_status','poi_description','expect_amount','pay_amount','channel','bid_type','poi_type'];
	
//	以下旧的
	public static $tb_bankcard_produce_fields = array('orderId','userId','bankId','bankCard','isDefault','statusCode','timeCreate',
					'resultMsg','resultTime','idCardType','idCardSN','realName','phone','cardId');
	
	public static $tb_orders_produce_fields = array('ordersId','waresId','waresName','shelfId','userId','nickname','amount','amountExt',
                    'amountFake', 'yieldStaticAdd','yieldStatic','yieldExt','interest',
					'interestStatic','interestAdd','interestFloat','interestExt','interestSub',
					'returnAmount','returnInterest','brief','extDesc','orderTime','orderStatus','codeCreate',
					'descCreate','vouchers','firstTime','returnType','returnNext','returnPlan');
	public static $tb_vouchers_produce_fields = ['voucherId','userId','voucherType','amount','timeCreate','dtUsed','orderId'];
	
	public  static  $tb_umeng_data=[
	    'ymd','channels','ids','new_user','active_user','launches_user',
	    'clientType'
	];
	
}