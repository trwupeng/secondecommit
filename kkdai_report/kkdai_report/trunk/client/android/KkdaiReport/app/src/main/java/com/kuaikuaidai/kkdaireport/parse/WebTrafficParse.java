package com.kuaikuaidai.kkdaireport.parse;


import android.text.TextUtils;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.WebTraffic;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 网页流量解析
 */
public class WebTrafficParse {

    private final static String TAG = "WebTrafficParse";

    private static WebTrafficParse webTrafficParse;

    private WebTrafficParse() {

    }

    public static WebTrafficParse getInstance() {
        if (webTrafficParse == null) {
            if (webTrafficParse == null) {
                webTrafficParse = new WebTrafficParse();
            }
        }
        return webTrafficParse;
    }

    private String pvCountName;
    private String visitorName;
    private String ipContName;
    private List<WebTraffic> webTrafficList;

    public String getPvCountName() {
        return pvCountName;
    }

    public void setPvCountName(String pvCountName) {
        this.pvCountName = pvCountName;
    }

    public String getVisitorName() {
        return visitorName;
    }

    public void setVisitorName(String visitorName) {
        this.visitorName = visitorName;
    }

    public String getIpContName() {
        return ipContName;
    }

    public void setIpContName(String ipContName) {
        this.ipContName = ipContName;
    }

    public List<WebTraffic> getWebTrafficList() {
        return webTrafficList;
    }

    public void setWebTrafficList(List<WebTraffic> webTrafficList) {
        this.webTrafficList = webTrafficList;
    }

    public void parseCategory(Object data) {
        try {
            if (data != null) {
                List<String> list = JSON.parseArray(data.toString(), String.class);
                setPvCountName(list.get(0));
                setVisitorName(list.get(1));
                setIpContName(list.get(2));
            }
        } catch (Exception e) {
            Logger.e(TAG, "parseCategory has Error:" + e.getMessage());
        }
    }

    public void parseRs(Object data) {
        try {
            if (data != null && !TextUtils.isEmpty(data.toString())) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (webTrafficList == null) {
                        webTrafficList = new ArrayList<WebTraffic>();
                    }
                    for (String temp : object.keySet()) {
                        WebTraffic webTraffic = (WebTraffic) JSON.parseObject(object.getString(temp), WebTraffic.class);
                        webTraffic.setStartToEnd(temp);
                        webTrafficList.add(webTraffic);
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parseRs has Error:" + e.getMessage());
        }
    }

    public void clear(){
        pvCountName=null;
        visitorName=null;
        ipContName=null;
        webTrafficList=null;
    }
}
