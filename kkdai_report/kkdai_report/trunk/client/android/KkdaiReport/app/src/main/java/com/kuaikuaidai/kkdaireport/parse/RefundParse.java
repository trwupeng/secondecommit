package com.kuaikuaidai.kkdaireport.parse;

import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.RefundBorrower;
import com.kuaikuaidai.kkdaireport.bean.RefundDetail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by zhong.jiye on 2016/10/17.
 */

public class RefundParse {

    private final static String TAG = "RefundParse";

    private static RefundParse parse;

    private RefundParse() {

    }

    public static RefundParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new RefundParse();
            }
        }
        return parse;
    }

    private List<RefundBorrower> borrowerList, investorList;
    private List<RefundDetail> refundDetailList;

    public List<RefundBorrower> getBorrowerList() {
        return borrowerList;
    }

    public void setBorrowerList(List<RefundBorrower> borrowerList) {
        this.borrowerList = borrowerList;
    }

    public List<RefundBorrower> getInvestorList() {
        return investorList;
    }

    public void setInvestorList(List<RefundBorrower> investorList) {
        this.investorList = investorList;
    }

    public List<RefundDetail> getRefundDetailList() {
        return refundDetailList;
    }

    public void setRefundDetailList(List<RefundDetail> refundDetailList) {
        this.refundDetailList = refundDetailList;
    }

    public void parseRefundBorrower(Object data) {
        try {
            if (data != null) {
                if (borrowerList == null) {
                    borrowerList = new ArrayList<RefundBorrower>();
                } else {
                    borrowerList.clear();
                }
                borrowerList = JSON.parseArray(data.toString(), RefundBorrower.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseRefundInvestor(Object data) {
        try {
            if (data != null) {
                if (investorList == null) {
                    investorList = new ArrayList<RefundBorrower>();
                } else {
                    investorList.clear();
                }
                investorList = JSON.parseArray(data.toString(), RefundBorrower.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (refundDetailList == null) {
                    refundDetailList = new ArrayList<RefundDetail>();
                } else {
                    refundDetailList.clear();
                }
                refundDetailList = JSON.parseArray(data.toString(), RefundDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
