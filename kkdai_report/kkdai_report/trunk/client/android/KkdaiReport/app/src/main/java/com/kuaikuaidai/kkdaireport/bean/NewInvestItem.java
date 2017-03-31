package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/28.
 */

public class NewInvestItem implements Serializable{
    private float countReg0Day;
    private float countReg1To5;
    private float countReg6To30;
    private float countReg31Plus;

    public NewInvestItem() {
    }

    public NewInvestItem(float countReg0Day, float countReg1To5, float countReg6To30, float countReg31Plus) {
        this.countReg0Day = countReg0Day;
        this.countReg1To5 = countReg1To5;
        this.countReg6To30 = countReg6To30;
        this.countReg31Plus = countReg31Plus;
    }

    public float getCountReg0Day() {
        return countReg0Day;
    }

    public void setCountReg0Day(float countReg0Day) {
        this.countReg0Day = countReg0Day;
    }

    public float getCountReg1To5() {
        return countReg1To5;
    }

    public void setCountReg1To5(float countReg1To5) {
        this.countReg1To5 = countReg1To5;
    }

    public float getCountReg6To30() {
        return countReg6To30;
    }

    public void setCountReg6To30(float countReg6To30) {
        this.countReg6To30 = countReg6To30;
    }

    public float getCountReg31Plus() {
        return countReg31Plus;
    }

    public void setCountReg31Plus(float countReg31Plus) {
        this.countReg31Plus = countReg31Plus;
    }
}
