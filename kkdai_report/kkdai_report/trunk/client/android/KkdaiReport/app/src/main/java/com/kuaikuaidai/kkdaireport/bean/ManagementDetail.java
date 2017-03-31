package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/12.
 */

public class ManagementDetail implements Serializable {
    private String ymd;
    private String bid_title;
    private String bid_amount;
    private String bid_unit;
    private String service_recharge;

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getBid_title() {
        return bid_title;
    }

    public void setBid_title(String bid_title) {
        this.bid_title = bid_title;
    }

    public String getBid_amount() {
        return bid_amount;
    }

    public void setBid_amount(String bid_amount) {
        this.bid_amount = bid_amount;
    }

    public String getBid_unit() {
        return bid_unit;
    }

    public void setBid_unit(String bid_unit) {
        this.bid_unit = bid_unit;
    }

    public String getService_recharge() {
        return service_recharge;
    }

    public void setService_recharge(String service_recharge) {
        this.service_recharge = service_recharge;
    }

    public ManagementDetail() {
    }

    public ManagementDetail(String ymd, String bid_title, String bid_amount, String bid_unit, String service_recharge) {
        this.ymd = ymd;
        this.bid_title = bid_title;
        this.bid_amount = bid_amount;
        this.bid_unit = bid_unit;
        this.service_recharge = service_recharge;
    }
}
