package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/28.
 */

public class NewInvestNumber implements Serializable {
    private NewInvestItem fbb;
    private NewInvestItem dqb;
    private NewInvestItem jyb;
    private String startToEnd;


    public NewInvestNumber() {
    }

    public NewInvestNumber(NewInvestItem fbb, NewInvestItem dqb, NewInvestItem jyb, String startToEnd) {
        this.fbb = fbb;
        this.dqb = dqb;
        this.jyb = jyb;
        this.startToEnd = startToEnd;
    }

    public NewInvestItem getFbb() {
        return fbb;
    }

    public void setFbb(NewInvestItem fbb) {
        this.fbb = fbb;
    }

    public NewInvestItem getDqb() {
        return dqb;
    }

    public void setDqb(NewInvestItem dqb) {
        this.dqb = dqb;
    }

    public NewInvestItem getJyb() {
        return jyb;
    }

    public void setJyb(NewInvestItem jyb) {
        this.jyb = jyb;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}
