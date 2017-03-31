package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.AppTraffic;
import com.kuaikuaidai.kkdaireport.bean.Channel;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

/**
 * App流量解析
 */
public class AppTrafficParse {

    private final static String TAG = "AppTrafficParse";

    private static AppTrafficParse appTrafficParse;

    private AppTrafficParse() {

    }

    public static AppTrafficParse getInstance() {
        if (appTrafficParse == null) {
            if (appTrafficParse == null) {
                appTrafficParse = new AppTrafficParse();
            }
        }
        return appTrafficParse;
    }

    private List<Channel> maxChannelList;

    private Channel channelHeader;


    private List<AppTraffic> appTrafficList;

    public List<Channel> getMaxChannelList() {
        return maxChannelList;
    }

    public void setMaxChannelList(List<Channel> maxChannelList) {
        this.maxChannelList = maxChannelList;
    }

    public Channel getChannelHeader() {
        return channelHeader;
    }

    public void setChannelHeader(Channel channelHeader) {
        this.channelHeader = channelHeader;
    }


    public static AppTrafficParse getAppTrafficParse() {
        return appTrafficParse;
    }

    public static void setAppTrafficParse(AppTrafficParse appTrafficParse) {
        AppTrafficParse.appTrafficParse = appTrafficParse;
    }

    public List<AppTraffic> getAppTrafficList() {
        return appTrafficList;
    }

    public void setAppTrafficList(List<AppTraffic> appTrafficList) {
        this.appTrafficList = appTrafficList;
    }


    public void parseMax(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (maxChannelList == null) {
                        maxChannelList = new ArrayList<Channel>();
                    }
                    Map<String, String> map = new TreeMap<String, String>();
                    for (String key : object.keySet()) {
                        maxChannelList.add(new Channel(key, object.getString(key)));
                    }
                    Collections.sort(maxChannelList, new Comparator<Channel>() {
                        @Override
                        public int compare(Channel o1, Channel o2) {
                            int o1value = Integer.parseInt(o1.getChannelData());
                            int o2value = Integer.parseInt(o2.getChannelData());
                            if (o1value > o2value) {
                                return -1;
                            } else if (o1value < o2value) {
                                return 1;
                            }
                            return 0;
                        }
                    });
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseHeader(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    channelHeader = new Channel(object.keySet().toArray()[1].toString(), object.keySet().toArray()[0].toString());
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


    public void parseRem(Object data) {
        try {
            if (data != null) {
                if (appTrafficList == null) {
                    appTrafficList = new ArrayList<AppTraffic>();
                }
                JSONObject object = JSON.parseObject(data.toString());
                for (String key : object.keySet()) {
                    AppTraffic appTraffic = new AppTraffic(key);
                    JSONObject object1 = object.getJSONObject(key);
                    for (String key2 : object1.keySet()) {
                        String content = object1.getString(key2);
                        AppTraffic appTraffic1 = JSON.parseObject(content, AppTraffic.class);
                        appTraffic.setActive_user(appTraffic.getActive_user() + appTraffic1.getActive_user());
                        appTraffic.setLaunches_user(appTraffic.getLaunches_user() + appTraffic1.getLaunches_user());
                        appTraffic.setNew_user(appTraffic.getNew_user() + appTraffic1.getNew_user());
                    }
                    appTrafficList.add(appTraffic);
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
