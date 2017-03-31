package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/11.
 */

public class CashBack implements Serializable {
    private String ymd;
    private String amount;

    public CashBack() {
    }

    public CashBack(String ymd, String amount) {
        this.ymd = ymd;
        this.amount = amount;
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }
}
