package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewInvestMoney;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新增理财金额解析
 */
public class NewInvestMoneyParse {

    private final static String TAG = "NewInvestMoneyParse";

    private static NewInvestMoneyParse newInvestNumbersParse;

    private NewInvestMoneyParse() {

    }

    public static NewInvestMoneyParse getInstance() {
        if (newInvestNumbersParse == null) {
            if (newInvestNumbersParse == null) {
                newInvestNumbersParse = new NewInvestMoneyParse();
            }
        }
        return newInvestNumbersParse;
    }


    private List<NewInvestMoney> newInvestMoneyList1;

    private List<NewInvestMoney> newInvestMoneyList2;

    private String rs1TitleText;
    private String rs2TitleText;

    public String getRs1TitleText() {
        return rs1TitleText;
    }

    public void setRs1TitleText(String rs1TitleText) {
        this.rs1TitleText = rs1TitleText;
    }

    public String getRs2TitleText() {
        return rs2TitleText;
    }

    public void setRs2TitleText(String rs2TitleText) {
        this.rs2TitleText = rs2TitleText;
    }

    public List<NewInvestMoney> getNewInvestMoneyList1() {
        return newInvestMoneyList1;
    }

    public void setNewInvestMoneyList1(List<NewInvestMoney> newInvestMoneyList1) {
        this.newInvestMoneyList1 = newInvestMoneyList1;
    }

    public List<NewInvestMoney> getNewInvestMoneyList2() {
        return newInvestMoneyList2;
    }

    public void setNewInvestMoneyList2(List<NewInvestMoney> newInvestMoneyList2) {
        this.newInvestMoneyList2 = newInvestMoneyList2;
    }

    public void parseRecords1(Object data) {
        try {
            if (data != null) {
                if (newInvestMoneyList1 == null) {
                    newInvestMoneyList1 = new ArrayList<NewInvestMoney>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewInvestMoney newInvestNumber = JSON.parseObject(object.getString(key), NewInvestMoney.class);
                    newInvestMoneyList1.add(newInvestNumber);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseRecords2(Object data) {
        try {
            if (data != null) {
                if (newInvestMoneyList2 == null) {
                    newInvestMoneyList2 = new ArrayList<NewInvestMoney>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewInvestMoney newInvestNumber = JSON.parseObject(object.getString(key), NewInvestMoney.class);
                    newInvestMoneyList2.add(newInvestNumber);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
