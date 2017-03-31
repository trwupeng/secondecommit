package com.kuaikuaidai.kkdaireport.comm;

import com.kuaikuaidai.kkdaireport.activity.finance.BidStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.CashBackStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.FailBidStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.LoanDetailsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.ManagementFeeStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.RechargeWithdrawStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.RefundBorrowerStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.RefundInvestorStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.ServiceFeeStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.UserFinancialActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.VoucherGrantStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.finance.VoucherUseStatisticsActivity;
import com.kuaikuaidai.kkdaireport.activity.kpi.KpiHomeActivity;
import com.kuaikuaidai.kkdaireport.activity.report.AppTrafficActivity;
import com.kuaikuaidai.kkdaireport.activity.report.CapitalDataCompareActivity;
import com.kuaikuaidai.kkdaireport.activity.report.InvestAgainNumbersActivity;
import com.kuaikuaidai.kkdaireport.activity.report.InvestAgainRatioActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewInvestMoneyActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewInvestMoneyPerCapitalActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewInvestNumbersActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewOldInvestMoneyActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewOldInvestMoneyPerCapitalActivity;
import com.kuaikuaidai.kkdaireport.activity.report.NewOldInvestNumbersActivity;
import com.kuaikuaidai.kkdaireport.activity.report.RemainDataActivity;
import com.kuaikuaidai.kkdaireport.activity.report.RtiConversionRatioActivity;
import com.kuaikuaidai.kkdaireport.activity.report.RtiNumActivity;
import com.kuaikuaidai.kkdaireport.activity.report.WebTrafficActivity;

import java.util.HashMap;

/**
 * Created by zhong.jiye on 2016/9/23.
 */

public class CommUrlConstant {
    public final static String LOGIN = "manage/manager/login";
    public final static String MENU = "manage/manager/index";
    public final static String WEB_TRAFFIC = "report/pcsitetraffic/index";
    public final static String APP_TRAFFIC = "report/umengdata/index";
    public final static String REGIST_TO_INVEST_NUMBERS = "report/regtoinvestmenttrans/index";
    public final static String REGIST_TO_INVEST_CONVERSION_RATIO = "report/regtoinvestmenttransrate/index";
    public final static String NEW_INVEST_NUMBERS = "report/newfinancial/index";
    public final static String NEW_INVEST_MONEY = "report/newlicaiamount/index";
    public final static String NEW_INVEST_MONEY_PER_CAPITA = "report/newfinancialavg/index";
    public final static String NEW_OLD_INVEST_NUMBERS = "report/oldandnewfinancial/index";
    public final static String NEW_OLD_INVEST_MONEY = "report/oldandnewfinancialamount/index";
    public final static String NEW_OLD_INVEST_MONEY_PER_CAPITA = "report/oldandnewfinancialavg/index";
    public final static String CAPITAL_DATA_COMPARE = "report/fundsdata/index";
    public final static String REMAIN_DATA = "report/retaineddata/index";
    public final static String INVEST_AGAIN_RATIO = "report/compoundrate/index";
    public final static String INVEST_AGAIN_NUMBERS = "report/compounddata/index";
    public final static String BID_STATIATICS = "report/bidstatistics/monthbid";//投标统计
    public final static String BID_DETAILS = "report/bidstatistics/dailybid";//投标详情
    public final static String FAIL_BID_STATIATICS = "report/liubiaostatistics/summary";//流标统计
    public final static String FAIL_BID_DETAILS = "report/bidstatistics/dailybid";//流标详情
    public final static String RECHARGE_WITHDRAW_STATIATICS = "report/rechdraw/index";//充值-提现统计
    public final static String RECHARGE_WITHDRAW_DETAILS = "report/rechdraw/chardrawdetails";//充值-提现详情
    public final static String REFUND_INVESTOR_STATIATICS = "report/paymentofinvestor/summary";//还款统计-投资人
    public final static String REFUND_BORROWER_STATIATICS = "report/paymentofborrower/summary";//还款统计-借款人
    public final static String REFUND_DETAILS = "report/paymentofinvestor/detail";//还款详情
    public final static String MANAGEMENT_FEE_STATIATICS = "report/servicecharge/index";//管理费
    public final static String MANAGEMENT_FEE_DETAILS = "report/servicecharge/details";//管理费详情
    public final static String VOUCHER_GRANT_STATIATICS = "report/vouchergrant/summary";//优惠券发放
    public final static String VOUCHER_GRANT_DETAILS = "report/vouchergrant/detail";//优惠券发放详情
    public final static String VOUCHER_USE_STATIATICS = "report/voucheruse/summary";//优惠券使用
    public final static String VOUCHER_USE_DETAILS = "report/voucheruse/detail";//优惠券使用
    public final static String CASH_BACK_STATIATICS = "report/cashbackmonth/index";//好友返现
    public final static String CASH_BACK_DETAILS = "report/cashback/details";//好友返现详情
    public final static String LOAN_DETAIL_STATIATICS = "report/fangkuan/index";//标的放款明细
    public final static String SERVICE_FEE_STATIATICS = "report/servicefee/index";//服务费
    public final static String SERVICE_FEE_DETAILS = "report/servicefee/details";//服务费详情
    public final static String USER_FINANCIAL_DETAILS = "report/userfinancialdetails/index";//用户理财明细
    public final static String WORK_LOG = "plan/perfdailylog/index";//工作日志
    public final static String NEWS_CENTER = "plan/message/index";//消息中心
    public final static String WORK_TARGET = "plan/perfdst/indextargetTab=day";//目标管理
    public final static String EC_STATIATICS = "plan/ecstat/index";//EC统计

    public final static HashMap<String, Class<?>> matchMap = new HashMap<>();

    static {
        matchMap.put(WEB_TRAFFIC, WebTrafficActivity.class);
        matchMap.put(APP_TRAFFIC, AppTrafficActivity.class);
        matchMap.put(REGIST_TO_INVEST_NUMBERS, RtiNumActivity.class);
        matchMap.put(REGIST_TO_INVEST_CONVERSION_RATIO, RtiConversionRatioActivity.class);
        matchMap.put(NEW_INVEST_NUMBERS, NewInvestNumbersActivity.class);
        matchMap.put(NEW_INVEST_MONEY, NewInvestMoneyActivity.class);
        matchMap.put(NEW_INVEST_MONEY_PER_CAPITA, NewInvestMoneyPerCapitalActivity.class);
        matchMap.put(NEW_OLD_INVEST_NUMBERS, NewOldInvestNumbersActivity.class);
        matchMap.put(NEW_OLD_INVEST_MONEY, NewOldInvestMoneyActivity.class);
        matchMap.put(NEW_OLD_INVEST_MONEY_PER_CAPITA, NewOldInvestMoneyPerCapitalActivity.class);
        matchMap.put(CAPITAL_DATA_COMPARE, CapitalDataCompareActivity.class);
        matchMap.put(REMAIN_DATA, RemainDataActivity.class);
        matchMap.put(INVEST_AGAIN_NUMBERS, InvestAgainNumbersActivity.class);
        matchMap.put(INVEST_AGAIN_RATIO, InvestAgainRatioActivity.class);
        matchMap.put(BID_STATIATICS, BidStatisticsActivity.class);
        matchMap.put(FAIL_BID_STATIATICS, FailBidStatisticsActivity.class);
        matchMap.put(RECHARGE_WITHDRAW_STATIATICS, RechargeWithdrawStatisticsActivity.class);
        matchMap.put(REFUND_INVESTOR_STATIATICS, RefundInvestorStatisticsActivity.class);
        matchMap.put(REFUND_BORROWER_STATIATICS, RefundBorrowerStatisticsActivity.class);
        matchMap.put(USER_FINANCIAL_DETAILS, UserFinancialActivity.class);
        matchMap.put(MANAGEMENT_FEE_STATIATICS, ManagementFeeStatisticsActivity.class);
        matchMap.put(VOUCHER_GRANT_STATIATICS, VoucherGrantStatisticsActivity.class);
        matchMap.put(VOUCHER_USE_STATIATICS, VoucherUseStatisticsActivity.class);
        matchMap.put(CASH_BACK_STATIATICS, CashBackStatisticsActivity.class);
        matchMap.put(LOAN_DETAIL_STATIATICS, LoanDetailsActivity.class);
        matchMap.put(SERVICE_FEE_STATIATICS, ServiceFeeStatisticsActivity.class);
        matchMap.put(WORK_TARGET, KpiHomeActivity.class);
        matchMap.put(WORK_LOG, KpiHomeActivity.class);
        matchMap.put(NEWS_CENTER, KpiHomeActivity.class);
        matchMap.put(EC_STATIATICS, KpiHomeActivity.class);
    }

}
