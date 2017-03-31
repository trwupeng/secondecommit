package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewOldInvestNumbersItem implements Serializable{
    private int count1Buy;
    private int count5Buy;
    private int count6PlusBuy;

    public NewOldInvestNumbersItem() {
    }

    public int getCount1Buy() {
        return count1Buy;
    }

    public void setCount1Buy(int count1Buy) {
        this.count1Buy = count1Buy;
    }

    public int getCount5Buy() {
        return count5Buy;
    }

    public void setCount5Buy(int count5Buy) {
        this.count5Buy = count5Buy;
    }

    public int getCount6PlusBuy() {
        return count6PlusBuy;
    }

    public void setCount6PlusBuy(int count6PlusBuy) {
        this.count6PlusBuy = count6PlusBuy;
    }

    public NewOldInvestNumbersItem(int count1Buy, int count5Buy, int count6PlusBuy) {
        this.count1Buy = count1Buy;
        this.count5Buy = count5Buy;
        this.count6PlusBuy = count6PlusBuy;
    }
}
