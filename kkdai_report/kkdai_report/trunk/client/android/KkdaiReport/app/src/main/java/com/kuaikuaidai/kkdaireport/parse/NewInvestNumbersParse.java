package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.NewInvestNumber;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 新增理财人数解析
 */
public class NewInvestNumbersParse {

    private final static String TAG = "NewInvestNumbersParse";

    private static NewInvestNumbersParse newInvestNumbersParse;

    private NewInvestNumbersParse() {

    }

    public static NewInvestNumbersParse getInstance() {
        if (newInvestNumbersParse == null) {
            if (newInvestNumbersParse == null) {
                newInvestNumbersParse = new NewInvestNumbersParse();
            }
        }
        return newInvestNumbersParse;
    }


    private List<NewInvestNumber> newInvestNumberList;


    public List<NewInvestNumber> getNewInvestNumberList() {
        return newInvestNumberList;
    }

    public void setNewInvestNumberList(List<NewInvestNumber> newInvestNumberList) {
        this.newInvestNumberList = newInvestNumberList;
    }

    public void parseRecords(Object data,String type) {
        try {
            if (data != null) {
                if (newInvestNumberList == null) {
                    newInvestNumberList = new ArrayList<NewInvestNumber>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    NewInvestNumber newInvestNumber = JSON.parseObject(object.getString(key), NewInvestNumber.class);
                    newInvestNumber.setStartToEnd(type);
                    newInvestNumberList.add(newInvestNumber);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
