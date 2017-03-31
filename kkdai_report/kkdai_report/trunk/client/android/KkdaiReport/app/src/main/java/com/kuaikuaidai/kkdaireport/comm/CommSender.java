//This file is generated by dic2comm. Don't mofity it.
package com.kuaikuaidai.kkdaireport.comm;

import android.content.Context;

import java.util.HashMap;

public class CommSender {
	//登录
	public static void login(
		String u, //手机号
		String p, //密码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == u )
		{
			return ;
		}
		if ( null == p )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("manage/manager/login");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "u", u );
			param.put( "p", p );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "manage/manager/login", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//获取菜单
	public static void getMenu(
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("manage/manager/index");
			CommCtrl.send(url, null, callback, CommConstant.CT_UI, ctx, null, false, null, "manage/manager/index", null );
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//网页流量
	public static void webTraffic(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/pcsitetraffic/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/pcsitetraffic/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//App流量
	public static void appTraffic(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/umengdata/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/umengdata/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//注册至理财人数
	public static void registToInvestNumbers(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String selectedContractId, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/regtoinvestmenttrans/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != selectedContractId )
				param.put( "selectedContractId", selectedContractId );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/regtoinvestmenttrans/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//注册至理财转化率
	public static void registToInvestConversionRatio(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String selectedContractId, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/regtoinvestmenttransrate/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != selectedContractId )
				param.put( "selectedContractId", selectedContractId );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/regtoinvestmenttransrate/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新增理财人数
	public static void newInvestNumbers(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/newfinancial/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/newfinancial/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新增理财金额
	public static void newInvestMoney(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String selectedContractId, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/newlicaiamount/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != selectedContractId )
				param.put( "selectedContractId", selectedContractId );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/newlicaiamount/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新增理财人均
	public static void newInvestMoneyPerCapita(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/newfinancialavg/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/newfinancialavg/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新老用户理财人数
	public static void newOldInvestNumbers(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/oldandnewfinancial/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/oldandnewfinancial/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新老用户理财金额
	public static void newOldInvestMoney(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String selectedContractId, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/oldandnewfinancialamount/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != selectedContractId )
				param.put( "selectedContractId", selectedContractId );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/oldandnewfinancialamount/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//新老用户理财人均
	public static void newOldInvestMoneyPerCapita(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/oldandnewfinancialavg/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/oldandnewfinancialavg/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//资金数据对比
	public static void capitalDataCompare(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/fundsdata/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/fundsdata/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//留存数据
	public static void remainData(
		String ymdTo, //截止日期
		String huanbi,//环比
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/retaineddata/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdTo", ymdTo );
			param.put( "huanbi", huanbi );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/retaineddata/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//复投人数
	public static void investAgainNumbers(
		String ymdTo, //截止日期
		String huanbi,//环比
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/compounddata/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdTo", ymdTo );
			param.put( "huanbi", huanbi );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/compounddata/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//复投率
	public static void investAgainRatio(
		String ymdTo, //截止日期
		String huanbi,//环比
		String select, //渠道名
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdTo )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/compoundrate/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdTo", ymdTo );
			param.put( "huanbi", huanbi );
			if ( null != select )
				param.put( "select", select );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/compoundrate/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//投标统计
	public static void bidStatistics(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/bidstatistics/monthbid");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/bidstatistics/monthbid", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//投标详情
	public static void bidDetails(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/bidstatistics/dailybid");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			param.put( "hide", "1" );
			param.put( "liubiao_tiyanbiao", "1" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/bidstatistics/dailybid", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//流标统计
	public static void failBidStatistics(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/liubiaostatistics/summary");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/liubiaostatistics/summary", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//流标详情
	public static void failBidDetails(
		String waresId, //
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == waresId )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/bidstatistics/dailybid");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "waresId", waresId );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/bidstatistics/dailybid", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//充值-提现统计
	public static void rechargeWithdrawStatistics(
		String _ymdForm_g2, //起始日期
		String _ymdTo_l2, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/rechdraw/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/rechdraw/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//充值-提现详情
	public static void rechargeWithdrawDetails(
		String _pkey_val_, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _pkey_val_ )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/rechdraw/chardrawdetails");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "_pkey_val_", _pkey_val_ );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/rechdraw/chardrawdetails", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//还款统计-投资人
	public static void repaymentInvestorStatistics(
		String waresId, //标的ID
		String waresName, //标的名称
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/paymentofinvestor/summary");
			HashMap< String, String > param = new HashMap< String, String >();
			if ( null != waresId )
				param.put( "waresId", waresId );
			if ( null != waresName )
				param.put( "waresName", waresName );
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/paymentofinvestor/summary", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//还款详情
	public static void refundDetail(
		String billId, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == billId )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/paymentofinvestor/detail");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "billId", billId );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/paymentofinvestor/detail", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//还款统计-借款人
	public static void repaymentBorrwoerStatistics(
		String shelfId, //产品类型
		String realname, //借款人姓名
		String waresName, //标的名称
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/paymentofborrower/summary");
			HashMap< String, String > param = new HashMap< String, String >();
			if ( null != shelfId )
				param.put( "shelfId", shelfId );
			if ( null != realname )
				param.put( "realname", realname );
			if ( null != waresName )
				param.put( "waresName", waresName );
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/paymentofborrower/summary", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//管理费
	public static void managementFeeStatistics(
		String _ymdForm_g2, //起始日期
		String _ymdTo_l2, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/servicecharge/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/servicecharge/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//管理费详情
	public static void managementFeeDetails(
		String _pkey_val_, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _pkey_val_ )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/servicecharge/details");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "_pkey_val_", _pkey_val_ );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/servicecharge/details", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//优惠券发放
	public static void voucherGrantStatistics(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/vouchergrant/summary");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/vouchergrant/summary", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//优惠券发放详情
	public static void voucherGrantDetails(
		String ymdCreate, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdCreate )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/vouchergrant/detail");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdCreate", ymdCreate );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/vouchergrant/detail", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//优惠券使用
	public static void voucherUseStatistics(
		String ymdFrom, //起始日期
		String ymdTo, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdFrom )
		{
			return ;
		}
		if ( null == ymdTo )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/voucheruse/summary");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdFrom", ymdFrom );
			param.put( "ymdTo", ymdTo );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/voucheruse/summary", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//优惠券使用详情
	public static void voucherUseDetails(
		String ymdUsed, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == ymdUsed )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/voucheruse/detail");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "ymdUsed", ymdUsed );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/voucheruse/detail", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//好友返现
	public static void cashBackStatistics(
		String _ymdForm_g2, //起始日期
		String _ymdTo_l2, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/cashbackmonth/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/cashback/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//好友返现详情
	public static void cashBackDetail(
		String _pkey_val_, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _pkey_val_ )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/cashback/details");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "_pkey_val_", _pkey_val_ );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/cashback/details", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//标的放款明细
	public static void loanDetail(
		String _ymdForm_g2, //起始日期
		String _ymdTo_l2, //截止日期
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/fangkuan/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/fangkuan/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//服务费
	public static void serviceFeeStatistics(
			String _ymdForm_g2, //起始日期
			String _ymdTo_l2, //截止日期
			String pageId, //页码
			CallbackInterface callback, //callback function
			Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/servicefee/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/servicefee/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//服务费详情
	public static void serviceFeeDetailStatistics(
		String _pkey_val_, //key
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _pkey_val_ )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/servicefee/details");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "_pkey_val_", _pkey_val_ );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/servicefee/details", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

	//用户理财明细(新浪)
	public static void userFinancialDetails(
		String _ymdForm_g2, //起始日期
		String _ymdTo_l2, //截止日期
		String _UsrCustId_l2, //用户名/已验证手机
		String pageId, //页码
		CallbackInterface callback, //callback function
		Context ctx //waiting view's parent
		)
	{
		if ( null == _ymdForm_g2 )
		{
			return ;
		}
		if ( null == _ymdTo_l2 )
		{
			return ;
		}
		if ( null == _UsrCustId_l2 )
		{
			return ;
		}
		if ( null == pageId )
		{
			return ;
		}

		try{
			CommCtrl.start();
			String url = CommCtrl.makeApi("report/userfinancialdetails/index");
			HashMap< String, String > param = new HashMap< String, String >();
			param.put( "__formguid__", "default" );
			param.put( "_ymdForm_g2", _ymdForm_g2 );
			param.put( "_ymdTo_l2", _ymdTo_l2 );
			param.put( "_UsrCustId_l2", _UsrCustId_l2 );
			param.put( "pageId", pageId );
			param.put( "pageSize", "20" );
			CommCtrl.send(url, param, callback, CommConstant.CT_UI, ctx, null, false, null, "report/userfinancialdetails/index", null );
			param = null;
		} catch( Exception e ){
			e.printStackTrace();
		}
	}

}
