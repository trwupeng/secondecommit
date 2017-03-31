package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/12.
 */

public class VoucherUse implements Serializable {
    private String ymd;
    private String dixian_use_amount;
    private String tixian_use_amount;
    private String jiaxi_use_num;
    private String fanxian_use_amount;
    private String __PKEY__;

    public VoucherUse() {
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getDixian_use_amount() {
        return dixian_use_amount;
    }

    public void setDixian_use_amount(String dixian_use_amount) {
        this.dixian_use_amount = dixian_use_amount;
    }

    public String getTixian_use_amount() {
        return tixian_use_amount;
    }

    public void setTixian_use_amount(String tixian_use_amount) {
        this.tixian_use_amount = tixian_use_amount;
    }

    public String getJiaxi_use_num() {
        return jiaxi_use_num;
    }

    public void setJiaxi_use_num(String jiaxi_use_num) {
        this.jiaxi_use_num = jiaxi_use_num;
    }

    public String getFanxian_use_amount() {
        return fanxian_use_amount;
    }

    public void setFanxian_use_amount(String fanxian_use_amount) {
        this.fanxian_use_amount = fanxian_use_amount;
    }

    public String get__PKEY__() {
        return __PKEY__;
    }

    public void set__PKEY__(String __PKEY__) {
        this.__PKEY__ = __PKEY__;
    }
}
