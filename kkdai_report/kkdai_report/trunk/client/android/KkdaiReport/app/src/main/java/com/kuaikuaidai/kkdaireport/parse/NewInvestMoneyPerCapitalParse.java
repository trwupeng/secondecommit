package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewInvestMoneyPerCapital;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新增理财人均金额解析
 */
public class NewInvestMoneyPerCapitalParse {

    private final static String TAG = "NewInvestMoneyPerCapitalParse";

    private static NewInvestMoneyPerCapitalParse newInvestMoneyPerCapitalParse;

    private NewInvestMoneyPerCapitalParse() {

    }

    public static NewInvestMoneyPerCapitalParse getInstance() {
        if (newInvestMoneyPerCapitalParse == null) {
            if (newInvestMoneyPerCapitalParse == null) {
                newInvestMoneyPerCapitalParse = new NewInvestMoneyPerCapitalParse();
            }
        }
        return newInvestMoneyPerCapitalParse;
    }


    private List<NewInvestMoneyPerCapital> newInvestMoneyPerCapitalList;


    public List<NewInvestMoneyPerCapital> getNewInvestMoneyPerCapitalList() {
        return newInvestMoneyPerCapitalList;
    }

    public void setNewInvestMoneyPerCapitalList(List<NewInvestMoneyPerCapital> newInvestMoneyPerCapitalList) {
        this.newInvestMoneyPerCapitalList = newInvestMoneyPerCapitalList;
    }

    public void parseRecords(Object data,String type) {
        try {
            if (data != null) {
                if (newInvestMoneyPerCapitalList == null) {
                    newInvestMoneyPerCapitalList = new ArrayList<NewInvestMoneyPerCapital>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewInvestMoneyPerCapital newInvestNumber = JSON.parseObject(object.getString(key), NewInvestMoneyPerCapital.class);
                    newInvestNumber.setStartToEnd(type);
                    newInvestMoneyPerCapitalList.add(newInvestNumber);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
