package com.kuaikuaidai.kkdaireport.bean;

/**
 * Created by zhong.jiye on 2016/10/17.
 */

public class RefundDetail {
    private String ordersId;
    private String userId;
    private String realname;
    private String phone;
    private String waresName;
    private String billNum;
    private String ymdShouldPay;
    private String ymdPayment;
    private String orderAmount;
    private String orderAmountExt;
    private String orderAmountSum;
    private String amount;
    private String interest;
    private String addInterest;
    private String penaltyInteret;
    private String deadLine;

    public RefundDetail() {
    }

    public String getOrdersId() {
        return ordersId;
    }

    public void setOrdersId(String ordersId) {
        this.ordersId = ordersId;
    }

    public String getUserId() {
        return userId;
    }

    public void setUserId(String userId) {
        this.userId = userId;
    }

    public String getRealname() {
        return realname;
    }

    public void setRealname(String realname) {
        this.realname = realname;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getWaresName() {
        return waresName;
    }

    public void setWaresName(String waresName) {
        this.waresName = waresName;
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

    public String getYmdPayment() {
        return ymdPayment;
    }

    public void setYmdPayment(String ymdPayment) {
        this.ymdPayment = ymdPayment;
    }

    public String getOrderAmount() {
        return orderAmount;
    }

    public void setOrderAmount(String orderAmount) {
        this.orderAmount = orderAmount;
    }

    public String getOrderAmountExt() {
        return orderAmountExt;
    }

    public void setOrderAmountExt(String orderAmountExt) {
        this.orderAmountExt = orderAmountExt;
    }

    public String getOrderAmountSum() {
        return orderAmountSum;
    }

    public void setOrderAmountSum(String orderAmountSum) {
        this.orderAmountSum = orderAmountSum;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getInterest() {
        return interest;
    }

    public void setInterest(String interest) {
        this.interest = interest;
    }

    public String getAddInterest() {
        return addInterest;
    }

    public void setAddInterest(String addInterest) {
        this.addInterest = addInterest;
    }

    public String getPenaltyInteret() {
        return penaltyInteret;
    }

    public void setPenaltyInteret(String penaltyInteret) {
        this.penaltyInteret = penaltyInteret;
    }

    public String getDeadLine() {
        return deadLine;
    }

    public void setDeadLine(String deadLine) {
        this.deadLine = deadLine;
    }
}
