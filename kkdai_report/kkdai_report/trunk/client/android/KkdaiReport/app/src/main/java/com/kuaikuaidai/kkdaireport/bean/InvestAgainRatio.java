package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/26.
 */

public class InvestAgainRatio implements Serializable {
    private float n1;
    private float n2;
    private float n3;
    private float n4;
    private String startToEnd;

    public InvestAgainRatio() {
    }

    public InvestAgainRatio(float n1, float n2, float n3, float n4) {
        this.n1 = n1;
        this.n2 = n2;
        this.n3 = n3;
        this.n4 = n4;
    }

    public float getN1() {
        return n1;
    }

    public void setN1(float n1) {
        this.n1 = n1;
    }

    public float getN2() {
        return n2;
    }

    public void setN2(float n2) {
        this.n2 = n2;
    }

    public float getN3() {
        return n3;
    }

    public void setN3(float n3) {
        this.n3 = n3;
    }

    public float getN4() {
        return n4;
    }

    public void setN4(float n4) {
        this.n4 = n4;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}
