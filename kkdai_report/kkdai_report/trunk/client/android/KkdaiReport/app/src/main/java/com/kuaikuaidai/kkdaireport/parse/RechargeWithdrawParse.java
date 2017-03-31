package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.RechargeWithdraw;
import com.kuaikuaidai.kkdaireport.bean.RwDetail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 充值-提现统计
 */
public class RechargeWithdrawParse {

    private final static String TAG = "RechargeWithdrawParse";

    private static RechargeWithdrawParse parse;

    private RechargeWithdrawParse() {

    }

    public static RechargeWithdrawParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new RechargeWithdrawParse();
            }
        }
        return parse;
    }

    private List<RechargeWithdraw> list;
    private List<RwDetail> rwDetailList;

    public List<RechargeWithdraw> getList() {
        return list;
    }

    public void setList(List<RechargeWithdraw> list) {
        this.list = list;
    }

    public List<RwDetail> getRwDetailList() {
        return rwDetailList;
    }

    public void setRwDetailList(List<RwDetail> rwDetailList) {
        this.rwDetailList = rwDetailList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<RechargeWithdraw>();
                } else {
                    list.clear();
                }
                list = JSON.parseArray(data.toString(), RechargeWithdraw.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (rwDetailList == null) {
                    rwDetailList = new ArrayList<RwDetail>();
                } else {
                    rwDetailList.clear();
                }
                rwDetailList = JSON.parseArray(data.toString(), RwDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
