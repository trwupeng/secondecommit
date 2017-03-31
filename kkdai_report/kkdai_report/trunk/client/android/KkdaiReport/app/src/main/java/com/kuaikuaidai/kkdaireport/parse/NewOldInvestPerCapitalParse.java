package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestPerCapital;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新老用户理财人均金额解析
 */
public class NewOldInvestPerCapitalParse {

    private final static String TAG = "NewOldInvestPerCapitalParse";

    private static NewOldInvestPerCapitalParse newOldInvestPerCapitalParse;

    private NewOldInvestPerCapitalParse() {

    }

    public static NewOldInvestPerCapitalParse getInstance() {
        if (newOldInvestPerCapitalParse == null) {
            if (newOldInvestPerCapitalParse == null) {
                newOldInvestPerCapitalParse = new NewOldInvestPerCapitalParse();
            }
        }
        return newOldInvestPerCapitalParse;
    }


    private List<NewOldInvestPerCapital> newOldInvestPerCapitalList;


    public List<NewOldInvestPerCapital> getNewOldInvestPerCapitalList() {
        return newOldInvestPerCapitalList;
    }

    public void setNewOldInvestPerCapitalList(List<NewOldInvestPerCapital> newOldInvestPerCapitalList) {
        this.newOldInvestPerCapitalList = newOldInvestPerCapitalList;
    }

    public void parseRecords(Object data,String type) {
        try {
            if (data != null) {
                if (newOldInvestPerCapitalList == null) {
                    newOldInvestPerCapitalList = new ArrayList<NewOldInvestPerCapital>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewOldInvestPerCapital newOldInvestPerCapital = JSON.parseObject(object.getString(key), NewOldInvestPerCapital.class);
                    newOldInvestPerCapital.setStartToEnd(type);
                    newOldInvestPerCapitalList.add(newOldInvestPerCapital);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
