package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewOldInvestPerCapitalItem implements Serializable {

    private float avgAmount1Buy;
    private float avgAmount5Buy;
    private float avgAmount6PlusBuy;

    public NewOldInvestPerCapitalItem() {
    }

    public NewOldInvestPerCapitalItem(float avgAmount1Buy, float avgAmount5Buy, float avgAmount6PlusBuy) {
        this.avgAmount1Buy = avgAmount1Buy;
        this.avgAmount5Buy = avgAmount5Buy;
        this.avgAmount6PlusBuy = avgAmount6PlusBuy;
    }

    public float getAvgAmount1Buy() {
        return avgAmount1Buy;
    }

    public void setAvgAmount1Buy(float avgAmount1Buy) {
        this.avgAmount1Buy = avgAmount1Buy;
    }

    public float getAvgAmount5Buy() {
        return avgAmount5Buy;
    }

    public void setAvgAmount5Buy(float avgAmount5Buy) {
        this.avgAmount5Buy = avgAmount5Buy;
    }

    public float getAvgAmount6PlusBuy() {
        return avgAmount6PlusBuy;
    }

    public void setAvgAmount6PlusBuy(float avgAmount6PlusBuy) {
        this.avgAmount6PlusBuy = avgAmount6PlusBuy;
    }
}
