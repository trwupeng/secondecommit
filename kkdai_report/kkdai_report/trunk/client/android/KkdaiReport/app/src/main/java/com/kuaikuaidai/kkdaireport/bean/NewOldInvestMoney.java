package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/9.
 */

public class NewOldInvestMoney implements Serializable {
    private float amount1Buy;
    private float amount5Buy;
    private float amount6PlusBuy;
    private int shelfId;

    public NewOldInvestMoney() {
    }

    public NewOldInvestMoney(float amount1Buy, float amount5Buy, float amount6PlusBuy, int shelfId) {
        this.amount1Buy = amount1Buy;
        this.amount5Buy = amount5Buy;
        this.amount6PlusBuy = amount6PlusBuy;
        this.shelfId = shelfId;
    }

    public float getAmount1Buy() {
        return amount1Buy;
    }

    public void setAmount1Buy(float amount1Buy) {
        this.amount1Buy = amount1Buy;
    }

    public float getAmount5Buy() {
        return amount5Buy;
    }

    public void setAmount5Buy(float amount5Buy) {
        this.amount5Buy = amount5Buy;
    }

    public float getAmount6PlusBuy() {
        return amount6PlusBuy;
    }

    public void setAmount6PlusBuy(float amount6PlusBuy) {
        this.amount6PlusBuy = amount6PlusBuy;
    }

    public int getShelfId() {
        return shelfId;
    }

    public void setShelfId(int shelfId) {
        this.shelfId = shelfId;
    }
}
