package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewOldInvestPerCapital implements Serializable {
    private NewOldInvestPerCapitalItem dqb;
    private NewOldInvestPerCapitalItem fbb;
    private NewOldInvestPerCapitalItem jyb;
    private String startToEnd;

    public NewOldInvestPerCapital() {
    }

    public NewOldInvestPerCapital(NewOldInvestPerCapitalItem dqb, NewOldInvestPerCapitalItem fbb, NewOldInvestPerCapitalItem jyb, String startToEnd) {
        this.dqb = dqb;
        this.fbb = fbb;
        this.jyb = jyb;
        this.startToEnd = startToEnd;
    }

    public NewOldInvestPerCapitalItem getDqb() {
        return dqb;
    }

    public void setDqb(NewOldInvestPerCapitalItem dqb) {
        this.dqb = dqb;
    }

    public NewOldInvestPerCapitalItem getFbb() {
        return fbb;
    }

    public void setFbb(NewOldInvestPerCapitalItem fbb) {
        this.fbb = fbb;
    }

    public NewOldInvestPerCapitalItem getJyb() {
        return jyb;
    }

    public void setJyb(NewOldInvestPerCapitalItem jyb) {
        this.jyb = jyb;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}
