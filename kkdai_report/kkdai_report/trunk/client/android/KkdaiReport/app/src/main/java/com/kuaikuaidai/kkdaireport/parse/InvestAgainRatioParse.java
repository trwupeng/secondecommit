package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.InvestAgainRatio;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 复投率
 */
public class InvestAgainRatioParse {

    private final static String TAG = "InvestAgainRatioParse";

    private static InvestAgainRatioParse investAgainRatioParse;

    private InvestAgainRatioParse() {

    }

    public static InvestAgainRatioParse getInstance() {
        if (investAgainRatioParse == null) {
            if (investAgainRatioParse == null) {
                investAgainRatioParse = new InvestAgainRatioParse();
            }
        }
        return investAgainRatioParse;
    }




    private List<InvestAgainRatio> investAgainRatioList;


    public List<InvestAgainRatio> getInvestAgainRatioList() {
        return investAgainRatioList;
    }

    public void setInvestAgainRatioList(List<InvestAgainRatio> investAgainRatioList) {
        this.investAgainRatioList = investAgainRatioList;
    }



    public void parseRecord(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (investAgainRatioList == null) {
                        investAgainRatioList = new ArrayList<InvestAgainRatio>();
                    }
                    for (String key : object.keySet()) {
                        InvestAgainRatio investAgainRatio = JSON.parseObject(object.getString(key), InvestAgainRatio.class);
                        investAgainRatio.setStartToEnd(key);
                        investAgainRatioList.add(investAgainRatio);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
