package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/11.
 */

public class ServiceFee implements Serializable {
    private String ymd;
    private String bid_serviceFee;

    public ServiceFee() {
    }

    public ServiceFee(String ymd, String bid_serviceFee) {
        this.ymd = ymd;
        this.bid_serviceFee = bid_serviceFee;
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getBid_serviceFee() {
        return bid_serviceFee;
    }

    public void setBid_serviceFee(String bid_serviceFee) {
        this.bid_serviceFee = bid_serviceFee;
    }
}
