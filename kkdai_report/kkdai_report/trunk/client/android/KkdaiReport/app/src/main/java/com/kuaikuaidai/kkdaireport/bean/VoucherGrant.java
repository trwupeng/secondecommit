package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/12.
 */

public class VoucherGrant implements Serializable {
    private String ymd;
    private String dixian_grant_amount;
    private String tixian_grant_amount;
    private String jiaxi_grant_num;
    private String fanxian_grant_amount;
    private String __PKEY__;

    public VoucherGrant() {
    }

    public VoucherGrant(String ymd, String dixian_grant_amount, String tixian_grant_amount, String jiaxi_grant_num, String fanxian_grant_amount, String __PKEY__) {
        this.ymd = ymd;
        this.dixian_grant_amount = dixian_grant_amount;
        this.tixian_grant_amount = tixian_grant_amount;
        this.jiaxi_grant_num = jiaxi_grant_num;
        this.fanxian_grant_amount = fanxian_grant_amount;
        this.__PKEY__ = __PKEY__;
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getDixian_grant_amount() {
        return dixian_grant_amount;
    }

    public void setDixian_grant_amount(String dixian_grant_amount) {
        this.dixian_grant_amount = dixian_grant_amount;
    }

    public String getTixian_grant_amount() {
        return tixian_grant_amount;
    }

    public void setTixian_grant_amount(String tixian_grant_amount) {
        this.tixian_grant_amount = tixian_grant_amount;
    }

    public String getJiaxi_grant_num() {
        return jiaxi_grant_num;
    }

    public void setJiaxi_grant_num(String jiaxi_grant_num) {
        this.jiaxi_grant_num = jiaxi_grant_num;
    }

    public String getFanxian_grant_amount() {
        return fanxian_grant_amount;
    }

    public void setFanxian_grant_amount(String fanxian_grant_amount) {
        this.fanxian_grant_amount = fanxian_grant_amount;
    }

    public String get__PKEY__() {
        return __PKEY__;
    }

    public void set__PKEY__(String __PKEY__) {
        this.__PKEY__ = __PKEY__;
    }
}
