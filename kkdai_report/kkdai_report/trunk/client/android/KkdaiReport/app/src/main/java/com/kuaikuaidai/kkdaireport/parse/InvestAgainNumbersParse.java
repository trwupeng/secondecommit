package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.InvestAgainNumbers;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 复投人数
 */
public class InvestAgainNumbersParse {

    private final static String TAG = "InvestAgainNumbersParse";

    private static InvestAgainNumbersParse investAgainNumbersParse;

    private InvestAgainNumbersParse() {

    }

    public static InvestAgainNumbersParse getInstance() {
        if (investAgainNumbersParse == null) {
            if (investAgainNumbersParse == null) {
                investAgainNumbersParse = new InvestAgainNumbersParse();
            }
        }
        return investAgainNumbersParse;
    }




    private List<InvestAgainNumbers> investAgainNumbersList;



    public List<InvestAgainNumbers> getInvestAgainNumbersList() {
        return investAgainNumbersList;
    }

    public void setInvestAgainNumbersList(List<InvestAgainNumbers> investAgainNumbersList) {
        this.investAgainNumbersList = investAgainNumbersList;
    }



    public void parseRecord(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (investAgainNumbersList == null) {
                        investAgainNumbersList = new ArrayList<InvestAgainNumbers>();
                    }
                    for (String key : object.keySet()) {
                        InvestAgainNumbers investAgainNumbers = JSON.parseObject(object.getString(key), InvestAgainNumbers.class);
                        investAgainNumbers.setStartToEnd(key);
                        investAgainNumbersList.add(investAgainNumbers);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
