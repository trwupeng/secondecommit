package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/13.
 */

public class RechargeWithdraw implements Serializable {
    private String ymd;
    private String superuesrrecharge;
    private String normaluesrrecharge;
    private String superuesrwithdraw;
    private String normaluesrwithdraw;
    private String rechargeAmount;
    private String withdrawAmount;
    private String withdrawcount;
    private String winwithdrawcount;
    private String withdrawusvoucher;
    private String copAmount;

    public RechargeWithdraw() {
    }

    public String getYmd() {
        return ymd;
    }

    public void setYmd(String ymd) {
        this.ymd = ymd;
    }

    public String getSuperuesrrecharge() {
        return superuesrrecharge;
    }

    public void setSuperuesrrecharge(String superuesrrecharge) {
        this.superuesrrecharge = superuesrrecharge;
    }

    public String getNormaluesrrecharge() {
        return normaluesrrecharge;
    }

    public void setNormaluesrrecharge(String normaluesrrecharge) {
        this.normaluesrrecharge = normaluesrrecharge;
    }

    public String getSuperuesrwithdraw() {
        return superuesrwithdraw;
    }

    public void setSuperuesrwithdraw(String superuesrwithdraw) {
        this.superuesrwithdraw = superuesrwithdraw;
    }

    public String getNormaluesrwithdraw() {
        return normaluesrwithdraw;
    }

    public void setNormaluesrwithdraw(String normaluesrwithdraw) {
        this.normaluesrwithdraw = normaluesrwithdraw;
    }

    public String getRechargeAmount() {
        return rechargeAmount;
    }

    public void setRechargeAmount(String rechargeAmount) {
        this.rechargeAmount = rechargeAmount;
    }

    public String getWithdrawAmount() {
        return withdrawAmount;
    }

    public void setWithdrawAmount(String withdrawAmount) {
        this.withdrawAmount = withdrawAmount;
    }

    public String getWithdrawcount() {
        return withdrawcount;
    }

    public void setWithdrawcount(String withdrawcount) {
        this.withdrawcount = withdrawcount;
    }

    public String getWinwithdrawcount() {
        return winwithdrawcount;
    }

    public void setWinwithdrawcount(String winwithdrawcount) {
        this.winwithdrawcount = winwithdrawcount;
    }

    public String getWithdrawusvoucher() {
        return withdrawusvoucher;
    }

    public void setWithdrawusvoucher(String withdrawusvoucher) {
        this.withdrawusvoucher = withdrawusvoucher;
    }

    public String getCopAmount() {
        return copAmount;
    }

    public void setCopAmount(String copAmount) {
        this.copAmount = copAmount;
    }
}
