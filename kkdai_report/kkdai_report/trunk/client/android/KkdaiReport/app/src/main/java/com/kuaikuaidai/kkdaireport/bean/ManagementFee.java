package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/11.
 */

public class ManagementFee implements Serializable {
    private String ymd;
    private String servicecharge;

    public ManagementFee() {
    }

    public ManagementFee(String ymd, String servicecharge) {
        this.ymd = ymd;
        this.servicecharge = servicecharge;
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getServicecharge() {
        return servicecharge;
    }

    public void setServicecharge(String servicecharge) {
        this.servicecharge = servicecharge;
    }
}
