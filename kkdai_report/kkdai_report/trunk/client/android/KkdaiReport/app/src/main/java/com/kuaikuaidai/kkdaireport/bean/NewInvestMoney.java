package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/28.
 */

public class NewInvestMoney implements Serializable {

    private float amountReg0Day;
    private float amountReg1To5;
    private float amountReg6To30;
    private float amountReg31Plus;
    private int shelfId;

    public NewInvestMoney() {
    }

    public NewInvestMoney(float amountReg0Day, float amountReg1To5, float amountReg6To30, float amountReg31Plus, int shelfId) {
        this.amountReg0Day = amountReg0Day;
        this.amountReg1To5 = amountReg1To5;
        this.amountReg6To30 = amountReg6To30;
        this.amountReg31Plus = amountReg31Plus;
        this.shelfId = shelfId;
    }

    public float getAmountReg0Day() {
        return amountReg0Day;
    }

    public void setAmountReg0Day(float amountReg0Day) {
        this.amountReg0Day = amountReg0Day;
    }

    public float getAmountReg1To5() {
        return amountReg1To5;
    }

    public void setAmountReg1To5(float amountReg1To5) {
        this.amountReg1To5 = amountReg1To5;
    }

    public float getAmountReg6To30() {
        return amountReg6To30;
    }

    public void setAmountReg6To30(float amountReg6To30) {
        this.amountReg6To30 = amountReg6To30;
    }

    public float getAmountReg31Plus() {
        return amountReg31Plus;
    }

    public void setAmountReg31Plus(float amountReg31Plus) {
        this.amountReg31Plus = amountReg31Plus;
    }

    public int getShelfId() {
        return shelfId;
    }

    public void setShelfId(int shelfId) {
        this.shelfId = shelfId;
    }

}
