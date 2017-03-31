package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewOldInvestNumbers implements Serializable {
    private NewOldInvestNumbersItem dqb;
    private NewOldInvestNumbersItem fbb;
    private NewOldInvestNumbersItem jyb;
    private String startToEnd;

    public NewOldInvestNumbers() {
    }

    public NewOldInvestNumbers(NewOldInvestNumbersItem dqb, NewOldInvestNumbersItem fbb, NewOldInvestNumbersItem jyb, String startToEnd) {
        this.dqb = dqb;
        this.fbb = fbb;
        this.jyb = jyb;
        this.startToEnd = startToEnd;
    }

    public NewOldInvestNumbersItem getDqb() {
        return dqb;
    }

    public void setDqb(NewOldInvestNumbersItem dqb) {
        this.dqb = dqb;
    }

    public NewOldInvestNumbersItem getFbb() {
        return fbb;
    }

    public void setFbb(NewOldInvestNumbersItem fbb) {
        this.fbb = fbb;
    }

    public NewOldInvestNumbersItem getJyb() {
        return jyb;
    }

    public void setJyb(NewOldInvestNumbersItem jyb) {
        this.jyb = jyb;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}
