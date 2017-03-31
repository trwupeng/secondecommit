package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.CapitalDataCompare;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 资金数据对比解析
 */
public class CapitalDataCompareParse {

    private final static String TAG = "CapitalDataCompareParse";

    private static CapitalDataCompareParse capitalDataCompareParse;

    private CapitalDataCompareParse() {

    }

    public static CapitalDataCompareParse getInstance() {
        if (capitalDataCompareParse == null) {
            if (capitalDataCompareParse == null) {
                capitalDataCompareParse = new CapitalDataCompareParse();
            }
        }
        return capitalDataCompareParse;
    }


    private List<CapitalDataCompare> capitalDataCompareList;

    public List<CapitalDataCompare> getCapitalDataCompareList() {
        return capitalDataCompareList;
    }

    public void setCapitalDataCompareList(List<CapitalDataCompare> capitalDataCompareList) {
        this.capitalDataCompareList = capitalDataCompareList;
    }


    public void parseRecord(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (capitalDataCompareList == null) {
                        capitalDataCompareList = new ArrayList<CapitalDataCompare>();
                    }
                    for (String key : object.keySet()) {
                        CapitalDataCompare capitalDataCompare = JSON.parseObject(object.getString(key), CapitalDataCompare.class);
                        capitalDataCompare.setStartToEnd(key);
                        capitalDataCompareList.add(capitalDataCompare);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
