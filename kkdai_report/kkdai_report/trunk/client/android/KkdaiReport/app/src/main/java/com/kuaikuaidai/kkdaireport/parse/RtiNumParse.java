package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.RtiNum;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 注册至理财转化率解析
 */
public class RtiNumParse {

    private final static String TAG = "RtiNumParse";

    private static RtiNumParse rtiNumParse;

    private RtiNumParse() {

    }

    public static RtiNumParse getInstance() {
        if (rtiNumParse == null) {
            if (rtiNumParse == null) {
                rtiNumParse = new RtiNumParse();
            }
        }
        return rtiNumParse;
    }


    private List<RtiNum> rtiNumList;

    public List<RtiNum> getRtiNumList() {
        return rtiNumList;
    }

    public void setRtiNumList(List<RtiNum> rtiNumList) {
        this.rtiNumList = rtiNumList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (rtiNumList == null) {
                    rtiNumList = new ArrayList<RtiNum>();
                }
                RtiNum rtiNum = JSON.parseObject(data.toString(),RtiNum.class);
                rtiNumList.add(rtiNum);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
