package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/14.
 */

public class FailBid implements Serializable {
    private String ymdStartReal;
    private String waresId;
    private String waresName;
    private String shelfId;
    private String deadLine;
    private String yieldStatic;
    private String amount;
    private String realRaise;
    private String statusCode;

    public FailBid() {
    }

    public String getYmdStartReal() {
        return ymdStartReal;
    }

    public void setYmdStartReal(String ymdStartReal) {
        this.ymdStartReal = ymdStartReal;
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

    public String getDeadLine() {
        return deadLine;
    }

    public void setDeadLine(String deadLine) {
        this.deadLine = deadLine;
    }

    public String getYieldStatic() {
        return yieldStatic;
    }

    public void setYieldStatic(String yieldStatic) {
        this.yieldStatic = yieldStatic;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getRealRaise() {
        return realRaise;
    }

    public void setRealRaise(String realRaise) {
        this.realRaise = realRaise;
    }

    public String getStatusCode() {
        return statusCode;
    }

    public void setStatusCode(String statusCode) {
        this.statusCode = statusCode;
    }
}
