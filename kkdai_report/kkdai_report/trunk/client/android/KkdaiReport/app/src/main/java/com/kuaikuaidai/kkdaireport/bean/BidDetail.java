package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/14.
 */

public class BidDetail implements Serializable {
    private String index_num;
    private String ordersId;
    private String userId;
    private String realname;
    private String phone;
    private String ymdhis;
    private String amount;
    private String amountExt;
    private String contractId;
    private String clientType;
    private String orderStatus;
    private String waresName;
    private String shelfId;
    private String waresAmount;
    private String deadLine;
    private String yieldStatic;
    private String statusCode;

    public BidDetail() {
    }

    public String getIndex_num() {
        return index_num;
    }

    public void setIndex_num(String index_num) {
        this.index_num = index_num;
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

    public String getYmdhis() {
        return ymdhis;
    }

    public void setYmdhis(String ymdhis) {
        this.ymdhis = ymdhis;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getAmountExt() {
        return amountExt;
    }

    public void setAmountExt(String amountExt) {
        this.amountExt = amountExt;
    }

    public String getContractId() {
        return contractId;
    }

    public void setContractId(String contractId) {
        this.contractId = contractId;
    }

    public String getClientType() {
        return clientType;
    }

    public void setClientType(String clientType) {
        this.clientType = clientType;
    }

    public String getOrderStatus() {
        return orderStatus;
    }

    public void setOrderStatus(String orderStatus) {
        this.orderStatus = orderStatus;
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

    public String getWaresAmount() {
        return waresAmount;
    }

    public void setWaresAmount(String waresAmount) {
        this.waresAmount = waresAmount;
    }

    public String getYieldStatic() {
        return yieldStatic;
    }

    public void setYieldStatic(String yieldStatic) {
        this.yieldStatic = yieldStatic;
    }

    public String getStatusCode() {
        return statusCode;
    }

    public void setStatusCode(String statusCode) {
        this.statusCode = statusCode;
    }

    public String getDeadLine() {
        return deadLine;
    }

    public void setDeadLine(String deadLine) {
        this.deadLine = deadLine;
    }
}
