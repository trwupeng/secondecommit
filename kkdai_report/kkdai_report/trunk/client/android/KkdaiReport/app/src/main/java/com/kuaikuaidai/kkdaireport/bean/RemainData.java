package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/27.
 */

public class RemainData implements Serializable {
    private float notLicaiHasBalance;
    private float licaiNoBalance;
    private float licaiHasBalance;
    private String startToEnd;

    public float getNotLicaiHasBalance() {
        return notLicaiHasBalance;
    }

    public void setNotLicaiHasBalance(float notLicaiHasBalance) {
        this.notLicaiHasBalance = notLicaiHasBalance;
    }

    public float getLicaiNoBalance() {
        return licaiNoBalance;
    }

    public void setLicaiNoBalance(float licaiNoBalance) {
        this.licaiNoBalance = licaiNoBalance;
    }

    public float getLicaiHasBalance() {
        return licaiHasBalance;
    }

    public void setLicaiHasBalance(float licaiHasBalance) {
        this.licaiHasBalance = licaiHasBalance;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }

    public RemainData() {
    }

    public RemainData(float notLicaiHasBalance, float licaiNoBalance, float licaiHasBalance, String startToEnd) {
        this.notLicaiHasBalance = notLicaiHasBalance;
        this.licaiNoBalance = licaiNoBalance;
        this.licaiHasBalance = licaiHasBalance;
        this.startToEnd = startToEnd;
    }
}
