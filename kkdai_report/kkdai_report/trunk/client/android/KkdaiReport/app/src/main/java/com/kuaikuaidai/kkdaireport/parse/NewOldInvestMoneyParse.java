package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestMoney;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新老用户理财金额解析
 */
public class NewOldInvestMoneyParse {

    private final static String TAG = "NewOldInvestMoneyParse";

    private static NewOldInvestMoneyParse newOldInvestMoneyParse;

    private NewOldInvestMoneyParse() {

    }

    public static NewOldInvestMoneyParse getInstance() {
        if (newOldInvestMoneyParse == null) {
            if (newOldInvestMoneyParse == null) {
                newOldInvestMoneyParse = new NewOldInvestMoneyParse();
            }
        }
        return newOldInvestMoneyParse;
    }


    private List<NewOldInvestMoney> newOldInvestMoneyList1, newOldInvestMoneyList2;

    public List<NewOldInvestMoney> getNewOldInvestMoneyList1() {
        return newOldInvestMoneyList1;
    }

    public void setNewOldInvestMoneyList1(List<NewOldInvestMoney> newOldInvestMoneyList1) {
        this.newOldInvestMoneyList1 = newOldInvestMoneyList1;
    }

    public List<NewOldInvestMoney> getNewOldInvestMoneyList2() {
        return newOldInvestMoneyList2;
    }

    public void setNewOldInvestMoneyList2(List<NewOldInvestMoney> newOldInvestMoneyList2) {
        this.newOldInvestMoneyList2 = newOldInvestMoneyList2;
    }

    public void parseRecords(Object data, int type) {
        try {
            if (data != null) {
                switch (type) {
                    case 1:
                        if (newOldInvestMoneyList1 == null) {
                            newOldInvestMoneyList1 = new ArrayList<NewOldInvestMoney>();
                        }else{
                            newOldInvestMoneyList1.clear();
                        }
                        break;
                    case 2:
                        if (newOldInvestMoneyList2 == null) {
                            newOldInvestMoneyList2 = new ArrayList<NewOldInvestMoney>();
                        }else{
                            newOldInvestMoneyList2.clear();
                        }
                        break;
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewOldInvestMoney newOldInvestMoney = JSON.parseObject(object.getString(key), NewOldInvestMoney.class);
                    switch (type) {
                        case 1:
                            newOldInvestMoneyList1.add(newOldInvestMoney);
                            break;
                        case 2:
                            newOldInvestMoneyList2.add(newOldInvestMoney);
                            break;
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
