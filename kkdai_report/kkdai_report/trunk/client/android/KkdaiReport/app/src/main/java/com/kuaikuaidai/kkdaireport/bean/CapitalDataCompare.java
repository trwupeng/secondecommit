package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/27.
 */

public class CapitalDataCompare implements Serializable {
    private float rechargeAmount;
    private float withdrawAmount;
    private String startToEnd;

    public CapitalDataCompare() {
    }

    public CapitalDataCompare(float rechargeAmount, float withdrawAmount, String startToEnd) {
        this.rechargeAmount = rechargeAmount;
        this.withdrawAmount = withdrawAmount;
        this.startToEnd = startToEnd;
    }

    public float getRechargeAmount() {
        return rechargeAmount;
    }

    public void setRechargeAmount(float rechargeAmount) {
        this.rechargeAmount = rechargeAmount;
    }

    public float getWithdrawAmount() {
        return withdrawAmount;
    }

    public void setWithdrawAmount(float withdrawAmount) {
        this.withdrawAmount = withdrawAmount;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}
