package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/13.
 */

public class Bid implements Serializable {
    private String ymd;
    private String amount_succ_normal;
    private String count_succ_normal;
    private String amount_fail_normal;
    private String count_fail_normal;
    private String amount_succ_super;
    private String count_succ_super;
    private String amount_fail_super;
    private String count_fail_super;
    private String sumSuccAmount;
    private String sumSuccCount;
    private String sumFailedAmount;
    private String sumFailedCount;
    private String __PKEY__;

    public Bid() {
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getAmount_succ_normal() {
        return amount_succ_normal;
    }

    public void setAmount_succ_normal(String amount_succ_normal) {
        this.amount_succ_normal = amount_succ_normal;
    }

    public String getCount_succ_normal() {
        return count_succ_normal;
    }

    public void setCount_succ_normal(String count_succ_normal) {
        this.count_succ_normal = count_succ_normal;
    }

    public String getAmount_fail_normal() {
        return amount_fail_normal;
    }

    public void setAmount_fail_normal(String amount_fail_normal) {
        this.amount_fail_normal = amount_fail_normal;
    }

    public String getCount_fail_normal() {
        return count_fail_normal;
    }

    public void setCount_fail_normal(String count_fail_normal) {
        this.count_fail_normal = count_fail_normal;
    }

    public String getAmount_succ_super() {
        return amount_succ_super;
    }

    public void setAmount_succ_super(String amount_succ_super) {
        this.amount_succ_super = amount_succ_super;
    }

    public String getCount_succ_super() {
        return count_succ_super;
    }

    public void setCount_succ_super(String count_succ_super) {
        this.count_succ_super = count_succ_super;
    }

    public String getAmount_fail_super() {
        return amount_fail_super;
    }

    public void setAmount_fail_super(String amount_fail_super) {
        this.amount_fail_super = amount_fail_super;
    }

    public String getCount_fail_super() {
        return count_fail_super;
    }

    public void setCount_fail_super(String count_fail_super) {
        this.count_fail_super = count_fail_super;
    }

    public String getSumSuccAmount() {
        return sumSuccAmount;
    }

    public void setSumSuccAmount(String sumSuccAmount) {
        this.sumSuccAmount = sumSuccAmount;
    }

    public String getSumSuccCount() {
        return sumSuccCount;
    }

    public void setSumSuccCount(String sumSuccCount) {
        this.sumSuccCount = sumSuccCount;
    }

    public String getSumFailedAmount() {
        return sumFailedAmount;
    }

    public void setSumFailedAmount(String sumFailedAmount) {
        this.sumFailedAmount = sumFailedAmount;
    }

    public String getSumFailedCount() {
        return sumFailedCount;
    }

    public void setSumFailedCount(String sumFailedCount) {
        this.sumFailedCount = sumFailedCount;
    }

    public String get__PKEY__() {
        return __PKEY__;
    }

    public void set__PKEY__(String __PKEY__) {
        this.__PKEY__ = __PKEY__;
    }
}
