package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.RtiConversionRatio;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 注册至理财转化率解析
 */
public class RtiConversionRatioParse {

    private final static String TAG = "RtiConversionRatioParse";

    private static RtiConversionRatioParse rtiConversionRatioParse;

    private RtiConversionRatioParse() {

    }

    public static RtiConversionRatioParse getInstance() {
        if (rtiConversionRatioParse == null) {
            if (rtiConversionRatioParse == null) {
                rtiConversionRatioParse = new RtiConversionRatioParse();
            }
        }
        return rtiConversionRatioParse;
    }


    private List<RtiConversionRatio> rtiConversionRatioList;


    public List<RtiConversionRatio> getRtiConversionRatioList() {
        return rtiConversionRatioList;
    }

    public void setRtiConversionRatioList(List<RtiConversionRatio> rtiConversionRatioList) {
        this.rtiConversionRatioList = rtiConversionRatioList;
    }


    public void parseRecord(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (rtiConversionRatioList == null) {
                        rtiConversionRatioList = new ArrayList<RtiConversionRatio>();
                    }else{
                        rtiConversionRatioList.clear();
                    }
                    for (String key : object.keySet()) {
                        RtiConversionRatio rtiConversionRatio = JSON.parseObject(object.getString(key), RtiConversionRatio.class);
                        rtiConversionRatio.setStartToEnd(key);
                        rtiConversionRatioList.add(rtiConversionRatio);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
