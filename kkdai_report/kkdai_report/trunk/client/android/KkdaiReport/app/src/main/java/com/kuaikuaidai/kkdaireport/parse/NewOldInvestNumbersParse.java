package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestNumbers;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新老用户理财人数解析
 */
public class NewOldInvestNumbersParse {

    private final static String TAG = "NewOldInvestNumbersParse";

    private static NewOldInvestNumbersParse newOldInvestNumbersParse;

    private NewOldInvestNumbersParse() {

    }

    public static NewOldInvestNumbersParse getInstance() {
        if (newOldInvestNumbersParse == null) {
            if (newOldInvestNumbersParse == null) {
                newOldInvestNumbersParse = new NewOldInvestNumbersParse();
            }
        }
        return newOldInvestNumbersParse;
    }


    private List<NewOldInvestNumbers> newOldInvestNumbersList;


    public List<NewOldInvestNumbers> getNewOldInvestNumbersList() {
        return newOldInvestNumbersList;
    }

    public void setNewOldInvestNumbersList(List<NewOldInvestNumbers> newOldInvestNumbersList) {
        this.newOldInvestNumbersList = newOldInvestNumbersList;
    }

    public void parseRecords(Object data,String type) {
        try {
            if (data != null) {
                if (newOldInvestNumbersList == null) {
                    newOldInvestNumbersList = new ArrayList<NewOldInvestNumbers>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewOldInvestNumbers newOldInvestNumbers = JSON.parseObject(object.getString(key), NewOldInvestNumbers.class);
                    newOldInvestNumbers.setStartToEnd(type);
                    newOldInvestNumbersList.add(newOldInvestNumbers);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
