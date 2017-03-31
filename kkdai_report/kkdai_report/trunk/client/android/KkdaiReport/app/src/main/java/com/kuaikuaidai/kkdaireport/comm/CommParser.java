package com.kuaikuaidai.kkdaireport.comm;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.activity.LoginActivity;
import com.kuaikuaidai.kkdaireport.cusview.SuperCustomToast;
import com.kuaikuaidai.kkdaireport.parse.AppTrafficParse;
import com.kuaikuaidai.kkdaireport.parse.BidParse;
import com.kuaikuaidai.kkdaireport.parse.CapitalDataCompareParse;
import com.kuaikuaidai.kkdaireport.parse.CashBackParse;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.FailBidParse;
import com.kuaikuaidai.kkdaireport.parse.InvestAgainNumbersParse;
import com.kuaikuaidai.kkdaireport.parse.InvestAgainRatioParse;
import com.kuaikuaidai.kkdaireport.parse.LoanDetailParse;
import com.kuaikuaidai.kkdaireport.parse.ManagementFeeParse;
import com.kuaikuaidai.kkdaireport.parse.MenuParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestMoneyParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestMoneyPerCapitalParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestNumbersParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestMoneyParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestNumbersParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestPerCapitalParse;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.RechargeWithdrawParse;
import com.kuaikuaidai.kkdaireport.parse.RefundParse;
import com.kuaikuaidai.kkdaireport.parse.RemainDataParse;
import com.kuaikuaidai.kkdaireport.parse.RtiConversionRatioParse;
import com.kuaikuaidai.kkdaireport.parse.RtiNumParse;
import com.kuaikuaidai.kkdaireport.parse.ServiceFeeParse;
import com.kuaikuaidai.kkdaireport.parse.TitleParse;
import com.kuaikuaidai.kkdaireport.parse.UserFinancialParse;
import com.kuaikuaidai.kkdaireport.parse.VoucherGrantParse;
import com.kuaikuaidai.kkdaireport.parse.VoucherUseParse;
import com.kuaikuaidai.kkdaireport.parse.WebTrafficParse;
import com.kuaikuaidai.kkdaireport.util.AppManager;
import com.kuaikuaidai.kkdaireport.util.CommUtil;
import com.kuaikuaidai.kkdaireport.util.Logger;
import com.kuaikuaidai.kkdaireport.util.SpUtil;

import java.util.Iterator;
import java.util.Set;

@SuppressLint("DefaultLocale")
public class CommParser {
    static int _code = 0;
    static String _key = null;

    public static void parse(long httpCode, String data, Exception e, int type, boolean needErr, CallbackInterface cb, long sn, Context act, String api, String userData) {
        if (null != e) {
            Logger.i("CommParser", "[sn:" + sn + "]");
            e.printStackTrace();
            if (showErr("network_error4", needErr, type, act)) {

            }

            if (null != cb) {
                cb.onCallback(-1, null, e, api, userData);
            }

            return;
        }

        if (200 != httpCode) {
            Logger.i("CommParser", "[sn:" + sn + "]http error(" + httpCode + "):" + data);
            if (CommUtil.isDebug() && null != data) //debug
            {
                if (showErr(data, needErr, type, act)) {

                }
            } else {
                if (showErr("network_error2", needErr, type, act)) {

                }
            }

            if (null != cb) {
                int code = -1;
                if (CommConstant.CODE_NO_NETWORK == httpCode) {
                    code = CommConstant.CODE_NO_NETWORK;
                }
                cb.onCallback(code, data, e, api, userData);
            }

            return;
        }

        JSONObject obj = null;
        try {
            obj = JSON.parseObject(data);
        } catch (Exception except) {
            Logger.i("CommParser", "[sn:" + sn + "]parse error:" + data);
            except.printStackTrace();
            if (showErr("network_error2", needErr, type, act)) {

            }

            if (null != cb) {
                cb.onCallback(-1, data, except, api, userData);
            }

            return;
        }

        if (null == obj) {
            Logger.i("CommParser", "[sn:" + sn + "]parse error2:" + data);
            if (showErr("network_error2", needErr, type, act)) {

            }

            if (null != cb) {
                cb.onCallback(-1, data, null, api, userData);
            }

            return;
        }

        Logger.i("CommParser", "[sn:" + sn + "]recv:" + data);
        com.orhanobut.logger.Logger.json(data);

        String msg = doParse(obj, userData, api);
        if (null != cb) {
            if (200 != _code) {
                showErr(msg, needErr, type, act);
                if(301==_code){
                    SpUtil.clearData();
                    act.startActivity(new Intent(act,LoginActivity.class));
                    AppManager.getAppManager().finishExceptActivity("com.kuaikuaidai.kkdaireport.activity.LoginActivity");
                }
            }
            cb.onCallback(_code, msg, null, api, userData);
        }
    }

    public static boolean showErr(String show, boolean needErr, int type, Context act) {
        if (null == act) {
            return false;
        }
        if (null == show || show.isEmpty()) {
            return false;
        }
        if (needErr) {
            return false;
        }

        if (CommConstant.CT_UI != type) {
            return false;
        }
        SuperCustomToast.getInstance(act).show(show, 1500);
        return true;
    }

    static boolean EQUAL(String key) {
        return (_key.toLowerCase().equals(key.toLowerCase()));
    }

    static String doParse(JSONObject obj, String userData, String api) {
        String msg = null;
        Set<String> keys = obj.keySet();
        Iterator<String> itr = keys.iterator();
        while (itr.hasNext()) {
            _key = itr.next();
            Object value = obj.get(_key);
            if (null == value) {
                continue;
            }
            if (EQUAL("statusCode")) {
                _code = Integer.parseInt(value.toString());
            } else if (EQUAL("message")) {
                msg = value.toString();
            } else if (EQUAL("pager")) {
                PagerParse.getInstance().parsePager(value);
            } else if (EQUAL("menus")) {
                MenuParse.getInstance().parseMenu(value);
            } else if (EQUAL("category")) {
                switch (api) {
                    case CommUrlConstant.WEB_TRAFFIC:
                        WebTrafficParse.getInstance().parseCategory(value);
                        break;
                }
            } else if (EQUAL("rs")) {
                switch (api) {
                    case CommUrlConstant.WEB_TRAFFIC:
                        WebTrafficParse.getInstance().parseRs(value);
                        break;
                    case CommUrlConstant.REGIST_TO_INVEST_CONVERSION_RATIO:
                        RtiConversionRatioParse.getInstance().parseRecord(value);
                        break;
                }
            } else if (EQUAL("max")) {
                AppTrafficParse.getInstance().parseMax(value);
            } else if (EQUAL("header")) {
                AppTrafficParse.getInstance().parseHeader(value);
            } else if (EQUAL("rem")) {
                switch (api) {
                    case CommUrlConstant.APP_TRAFFIC:
                        AppTrafficParse.getInstance().parseRem(value);
                        break;
                    default:
                        ContractIdsParse.getInstance().parseContractIds(value);
                        break;
                }
            } else if (EQUAL("rem1")) {
                AppTrafficParse.getInstance().parseRem(value);
            } else if (EQUAL("record") || EQUAL("record1")) {
                switch (api) {
                    case CommUrlConstant.INVEST_AGAIN_RATIO:
                        InvestAgainRatioParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.NEW_INVEST_MONEY_PER_CAPITA:
                        if (EQUAL("record")) {
                            NewInvestMoneyPerCapitalParse.getInstance().parseRecords(value,"1");
                        }else{
                            NewInvestMoneyPerCapitalParse.getInstance().parseRecords(value,"2");
                        }
                        break;
                    case CommUrlConstant.NEW_OLD_INVEST_NUMBERS:
                        if (EQUAL("record")) {
                            NewOldInvestNumbersParse.getInstance().parseRecords(value,"1");
                        }else{
                            NewOldInvestNumbersParse.getInstance().parseRecords(value,"2");
                        }
                        break;
                    case CommUrlConstant.NEW_OLD_INVEST_MONEY_PER_CAPITA:
                        if (EQUAL("record")) {
                            NewOldInvestPerCapitalParse.getInstance().parseRecords(value,"1");
                        }else{
                            NewOldInvestPerCapitalParse.getInstance().parseRecords(value,"2");
                        }
                        break;
                }
            } else if (EQUAL("records") || EQUAL("records1")) {
                switch (api) {
                    case CommUrlConstant.SERVICE_FEE_STATIATICS:
                        ServiceFeeParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.SERVICE_FEE_DETAILS:
                        ServiceFeeParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.MANAGEMENT_FEE_STATIATICS:
                        ManagementFeeParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.MANAGEMENT_FEE_DETAILS:
                        ManagementFeeParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.CASH_BACK_STATIATICS:
                        CashBackParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.CASH_BACK_DETAILS:
                        CashBackParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.INVEST_AGAIN_NUMBERS:
                        InvestAgainNumbersParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.CAPITAL_DATA_COMPARE:
                        CapitalDataCompareParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.REMAIN_DATA:
                        RemainDataParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.NEW_INVEST_NUMBERS:
                        if (EQUAL("records")) {
                            NewInvestNumbersParse.getInstance().parseRecords(value,"1");
                        } else {
                            NewInvestNumbersParse.getInstance().parseRecords(value,"2");
                        }
                        break;
                    case CommUrlConstant.VOUCHER_GRANT_STATIATICS:
                        VoucherGrantParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.VOUCHER_GRANT_DETAILS:
                        VoucherGrantParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.VOUCHER_USE_STATIATICS:
                        VoucherUseParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.VOUCHER_USE_DETAILS:
                        VoucherUseParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.USER_FINANCIAL_DETAILS:
                        UserFinancialParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.RECHARGE_WITHDRAW_STATIATICS:
                        RechargeWithdrawParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.BID_STATIATICS:
                        BidParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.LOAN_DETAIL_STATIATICS:
                        LoanDetailParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.BID_DETAILS:
                        BidParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.RECHARGE_WITHDRAW_DETAILS:
                        RechargeWithdrawParse.getInstance().parseDetail(value);
                        break;
                    case CommUrlConstant.FAIL_BID_STATIATICS:
                        FailBidParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.REFUND_BORROWER_STATIATICS:
                        RefundParse.getInstance().parseRefundBorrower(value);
                        break;
                    case CommUrlConstant.REFUND_INVESTOR_STATIATICS:
                        RefundParse.getInstance().parseRefundInvestor(value);
                        break;
                    case CommUrlConstant.REFUND_DETAILS:
                        RefundParse.getInstance().parseDetail(value);
                        break;
                }
            } else if (EQUAL("contractIds")) {
                ContractIdsParse.getInstance().parseContractIds(value);
            } else if (EQUAL("channel")) {
                ContractIdsParse.getInstance().parseChannel(value);
            } else if (EQUAL("rs1")) {
                switch (api) {
                    case CommUrlConstant.NEW_INVEST_MONEY:
                        NewInvestMoneyParse.getInstance().parseRecords1(value);
                        break;
                    case CommUrlConstant.REGIST_TO_INVEST_NUMBERS:
                        RtiNumParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.NEW_OLD_INVEST_MONEY:
                        NewOldInvestMoneyParse.getInstance().parseRecords(value, 1);
                        break;
                }
            } else if (EQUAL("rs2")) {
                switch (api) {
                    case CommUrlConstant.NEW_INVEST_MONEY:
                        NewInvestMoneyParse.getInstance().parseRecords2(value);
                        break;
                    case CommUrlConstant.REGIST_TO_INVEST_NUMBERS:
                        RtiNumParse.getInstance().parseRecord(value);
                        break;
                    case CommUrlConstant.NEW_OLD_INVEST_MONEY:
                        NewOldInvestMoneyParse.getInstance().parseRecords(value, 2);
                        break;
                }
            } else if (EQUAL("rs1TitleText")) {
                NewInvestMoneyParse.getInstance().setRs1TitleText(value.toString());
            } else if (EQUAL("rs2TitleText")) {
                NewInvestMoneyParse.getInstance().setRs2TitleText(value.toString());
            } else if (EQUAL("dtime2")) {
                TitleParse.getInstance().setDate1(value.toString());
            } else if (EQUAL("dtime3")) {
                TitleParse.getInstance().setDate2(value.toString());
            } else if (EQUAL("rs1SubText")) {
                TitleParse.getInstance().setDate3(value.toString());
            } else if (EQUAL("rs2SubText")) {
                TitleParse.getInstance().setDate4(value.toString());
            } else if (EQUAL("sumDixian")) {
                VoucherGrantParse.getInstance().getVoucherGrantInstance().setSumDixian(value.toString());
            } else if (EQUAL("sumTixian")) {
                VoucherGrantParse.getInstance().getVoucherGrantInstance().setSumTixian(value.toString());
            } else if (EQUAL("sumJiaXi")) {
                VoucherGrantParse.getInstance().getVoucherGrantInstance().setSumJiaXi(value.toString());
            } else if (EQUAL("sumFanxian")) {
                VoucherGrantParse.getInstance().getVoucherGrantInstance().setSumFanxian(value.toString());
            } else if (EQUAL("succ_super_amount")) {
                BidParse.getInstance().getBidSumInstance().setSucc_super_amount(value.toString());
            } else if (EQUAL("succ_super_count")) {
                BidParse.getInstance().getBidSumInstance().setSucc_super_count(value.toString());
            } else if (EQUAL("succ_normal_amount")) {
                BidParse.getInstance().getBidSumInstance().setSucc_normal_amount(value.toString());
            } else if (EQUAL("succ_normal_count")) {
                BidParse.getInstance().getBidSumInstance().setSucc_normal_count(value.toString());
            } else if (EQUAL("sumSuccAmount")) {
                BidParse.getInstance().getBidSumInstance().setSumSuccAmount(value.toString());
            } else if (EQUAL("sumSuccCount")) {
                BidParse.getInstance().getBidSumInstance().setSumSuccCount(value.toString());
            }
        }
        return msg;
    }

}