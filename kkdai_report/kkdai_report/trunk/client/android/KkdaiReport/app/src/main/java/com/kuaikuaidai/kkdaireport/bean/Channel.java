package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/26.
 */

public class Channel implements Serializable {
    private String channelName;
    private String channelData;

    public String getChannelName() {
        return channelName;
    }

    public void setChannelName(String channelName) {
        this.channelName = channelName;
    }

    public String getChannelData() {
        return channelData;
    }

    public void setChannelData(String channelData) {
        this.channelData = channelData;
    }

    public Channel(String channelName, String channelData) {
        this.channelName = channelName;
        this.channelData = channelData;
    }
}
