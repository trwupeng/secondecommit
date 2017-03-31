package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/27.
 */

public class RtiConversionRatio implements Serializable {
    private String startToEnd;
    private float realnameCount;
    private float bindcardCount;
    private float newRechargeCount;
    private float newBuyCount;

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }

    public float getRealnameCount() {
        return realnameCount;
    }

    public void setRealnameCount(float realnameCount) {
        this.realnameCount = realnameCount;
    }

    public float getBindcardCount() {
        return bindcardCount;
    }

    public void setBindcardCount(float bindcardCount) {
        this.bindcardCount = bindcardCount;
    }

    public float getNewRechargeCount() {
        return newRechargeCount;
    }

    public void setNewRechargeCount(float newRechargeCount) {
        this.newRechargeCount = newRechargeCount;
    }

    public float getNewBuyCount() {
        return newBuyCount;
    }

    public void setNewBuyCount(float newBuyCount) {
        this.newBuyCount = newBuyCount;
    }

    public RtiConversionRatio() {
    }

    public RtiConversionRatio(String startToEnd, float realnameCount, float bindcardCount, float newRechargeCount, float newBuyCount) {
        this.startToEnd = startToEnd;
        this.realnameCount = realnameCount;
        this.bindcardCount = bindcardCount;
        this.newRechargeCount = newRechargeCount;
        this.newBuyCount = newBuyCount;
    }
}
