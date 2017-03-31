package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/17.
 */

public class RefundBorrower implements Serializable {
    private String PKEY__;
    private String ymdPayment;
    private String nickname;
    private String realname;
    private String waresId;
    private String waresName;
    private String shelfId;
    private String billNum;
    private String ymdShouldPay;
    private String principal;
    private String interest;
    private String serviceCharge;
    private String penaltyInteret;
    private String overheadCharges;
    private String sumAmount;
    private String paymentMoney;
    private String finish;

    public RefundBorrower() {
    }

    public String getPKEY__() {
        return PKEY__;
    }

    public void setPKEY__(String PKEY__) {
        this.PKEY__ = PKEY__;
    }

    public String getYmdPayment() {
        return ymdPayment;
    }

    public void setYmdPayment(String ymdPayment) {
        this.ymdPayment = ymdPayment;
    }

    public String getNickname() {
        return nickname;
    }

    public void setNickname(String nickname) {
        this.nickname = nickname;
    }

    public String getRealname() {
        return realname;
    }

    public void setRealname(String realname) {
        this.realname = realname;
    }

    public String getWaresId() {
        return waresId;
    }

    public void setWaresId(String waresId) {
        this.waresId = waresId;
    }

    public String getWaresName() {
        return waresName;
    }

    public void setWaresName(String waresName) {
        this.waresName = waresName;
    }

    public String getShelfId() {
        return shelfId;
    }

    public void setShelfId(String shelfId) {
        this.shelfId = shelfId;
    }

    public String getBillNum() {
        return billNum;
    }

    public void setBillNum(String billNum) {
        this.billNum = billNum;
    }

    public String getYmdShouldPay() {
        return ymdShouldPay;
    }

    public void setYmdShouldPay(String ymdShouldPay) {
        this.ymdShouldPay = ymdShouldPay;
    }

    public String getPrincipal() {
        return principal;
    }

    public void setPrincipal(String principal) {
        this.principal = principal;
    }

    public String getInterest() {
        return interest;
    }

    public void setInterest(String interest) {
        this.interest = interest;
    }

    public String getServiceCharge() {
        return serviceCharge;
    }

    public void setServiceCharge(String serviceCharge) {
        this.serviceCharge = serviceCharge;
    }

    public String getPenaltyInteret() {
        return penaltyInteret;
    }

    public void setPenaltyInteret(String penaltyInteret) {
        this.penaltyInteret = penaltyInteret;
    }

    public String getOverheadCharges() {
        return overheadCharges;
    }

    public void setOverheadCharges(String overheadCharges) {
        this.overheadCharges = overheadCharges;
    }

    public String getSumAmount() {
        return sumAmount;
    }

    public void setSumAmount(String sumAmount) {
        this.sumAmount = sumAmount;
    }

    public String getPaymentMoney() {
        return paymentMoney;
    }

    public void setPaymentMoney(String paymentMoney) {
        this.paymentMoney = paymentMoney;
    }

    public String getFinish() {
        return finish;
    }

    public void setFinish(String finish) {
        this.finish = finish;
    }
}
