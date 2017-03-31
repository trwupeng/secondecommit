package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.CashBack;
import com.kuaikuaidai.kkdaireport.bean.CashBackDetail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 服务费解析
 */
public class CashBackParse {

    private final static String TAG = "CashBackParse";

    private static CashBackParse parse;

    private CashBackParse() {

    }

    public static CashBackParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new CashBackParse();
            }
        }
        return parse;
    }

    private List<CashBack> cashBackList;
    private List<CashBackDetail> cashBackDetailList;

    public List<CashBack> getCashBackList() {
        return cashBackList;
    }

    public void setCashBackList(List<CashBack> cashBackList) {
        this.cashBackList = cashBackList;
    }

    public List<CashBackDetail> getCashBackDetailList() {
        return cashBackDetailList;
    }

    public void setCashBackDetailList(List<CashBackDetail> cashBackDetailList) {
        this.cashBackDetailList = cashBackDetailList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (cashBackList == null) {
                    cashBackList = new ArrayList<CashBack>();
                } else {
                    cashBackList.clear();
                }
                cashBackList = JSON.parseArray(data.toString(), CashBack.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (cashBackDetailList == null) {
                    cashBackDetailList = new ArrayList<CashBackDetail>();
                } else {
                    cashBackDetailList.clear();
                }
                cashBackDetailList = JSON.parseArray(data.toString(), CashBackDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
