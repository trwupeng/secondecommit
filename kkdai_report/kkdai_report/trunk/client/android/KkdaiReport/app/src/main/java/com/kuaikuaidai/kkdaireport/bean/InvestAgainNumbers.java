package com.kuaikuaidai.kkdaireport.bean;

/**
 * Created by zhong.jiye on 2016/9/26.
 */

public class InvestAgainNumbers extends InvestAgainRatio {
    private float n5;

    public float getN5() {
        return n5;
    }

    public void setN5(float n5) {
        this.n5 = n5;
    }

    public InvestAgainNumbers() {
    }

    public InvestAgainNumbers(float n1, float n2, float n3, float n4, float n5) {
        super(n1, n2, n3, n4);
        this.n5 = n5;
    }
}
