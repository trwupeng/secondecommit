package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/29.
 */

public class NewInvestMoneyPerCapital implements Serializable {
    private NewInvestMoneyPerCapitalItem dqb;
    private NewInvestMoneyPerCapitalItem fbb;
    private NewInvestMoneyPerCapitalItem jyb;
    private String startToEnd;

    public NewInvestMoneyPerCapitalItem getDqb() {
        return dqb;
    }

    public void setDqb(NewInvestMoneyPerCapitalItem dqb) {
        this.dqb = dqb;
    }

    public NewInvestMoneyPerCapitalItem getFbb() {
        return fbb;
    }

    public void setFbb(NewInvestMoneyPerCapitalItem fbb) {
        this.fbb = fbb;
    }

    public NewInvestMoneyPerCapitalItem getJyb() {
        return jyb;
    }

    public void setJyb(NewInvestMoneyPerCapitalItem jyb) {
        this.jyb = jyb;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }

    public NewInvestMoneyPerCapital() {
    }

    public NewInvestMoneyPerCapital(NewInvestMoneyPerCapitalItem dqb, NewInvestMoneyPerCapitalItem fbb, NewInvestMoneyPerCapitalItem jyb, String startToEnd) {
        this.dqb = dqb;
        this.fbb = fbb;
        this.jyb = jyb;
        this.startToEnd = startToEnd;
    }
}
