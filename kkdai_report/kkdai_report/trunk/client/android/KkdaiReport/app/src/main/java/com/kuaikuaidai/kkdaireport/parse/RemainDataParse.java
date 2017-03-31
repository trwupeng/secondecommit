package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.RemainData;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 留存数据解析
 */
public class RemainDataParse {

    private final static String TAG = "RemainDataParse";

    private static RemainDataParse remainDataParse;

    private RemainDataParse() {

    }

    public static RemainDataParse getInstance() {
        if (remainDataParse == null) {
            if (remainDataParse == null) {
                remainDataParse = new RemainDataParse();
            }
        }
        return remainDataParse;
    }


    private List<RemainData> remainDataList;

    public List<RemainData> getRemainDataList() {
        return remainDataList;
    }

    public void setRemainDataList(List<RemainData> remainDataList) {
        this.remainDataList = remainDataList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (remainDataList == null) {
                        remainDataList = new ArrayList<RemainData>();
                    }
                    for (String key : object.keySet()) {
                        RemainData remainData = JSON.parseObject(object.getString(key), RemainData.class);
                        remainData.setStartToEnd(key);
                        remainDataList.add(remainData);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
