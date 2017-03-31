package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewInvestMoneyPerCapitalItem implements Serializable {
    private float avgAmountReg0Day;
    private float avgAmountReg1To5;
    private float avgAmountReg6To30;
    private float avgAmountReg31Plus;

    public float getAvgAmountReg0Day() {
        return avgAmountReg0Day;
    }

    public void setAvgAmountReg0Day(float avgAmountReg0Day) {
        this.avgAmountReg0Day = avgAmountReg0Day;
    }

    public float getAvgAmountReg1To5() {
        return avgAmountReg1To5;
    }

    public void setAvgAmountReg1To5(float avgAmountReg1To5) {
        this.avgAmountReg1To5 = avgAmountReg1To5;
    }

    public float getAvgAmountReg6To30() {
        return avgAmountReg6To30;
    }

    public void setAvgAmountReg6To30(float avgAmountReg6To30) {
        this.avgAmountReg6To30 = avgAmountReg6To30;
    }

    public float getAvgAmountReg31Plus() {
        return avgAmountReg31Plus;
    }

    public void setAvgAmountReg31Plus(float avgAmountReg31Plus) {
        this.avgAmountReg31Plus = avgAmountReg31Plus;
    }

    public NewInvestMoneyPerCapitalItem(float avgAmountReg0Day, float avgAmountReg1To5, float avgAmountReg6To30, float avgAmountReg31Plus) {
        this.avgAmountReg0Day = avgAmountReg0Day;
        this.avgAmountReg1To5 = avgAmountReg1To5;
        this.avgAmountReg6To30 = avgAmountReg6To30;
        this.avgAmountReg31Plus = avgAmountReg31Plus;
    }

    public NewInvestMoneyPerCapitalItem() {
    }
}
