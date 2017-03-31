package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/11.
 */

public class SeviceFeeDetail implements Serializable {

    private String customer_realname;
    private String create_time;
    private String bid_title;
    private String bid_amount;
    private String bid_interest;
    private String bid_period;
    private String bid_credit_level;
    private String bid_serviceFee;

    public SeviceFeeDetail() {
    }

    public SeviceFeeDetail(String customer_realname, String create_time, String bid_title, String bid_amount, String bid_interest, String bid_period, String bid_credit_level, String bid_serviceFee) {
        this.customer_realname = customer_realname;
        this.create_time = create_time;
        this.bid_title = bid_title;
        this.bid_amount = bid_amount;
        this.bid_interest = bid_interest;
        this.bid_period = bid_period;
        this.bid_credit_level = bid_credit_level;
        this.bid_serviceFee = bid_serviceFee;
    }

    public String getCustomer_realname() {
        return customer_realname;
    }

    public void setCustomer_realname(String customer_realname) {
        this.customer_realname = customer_realname;
    }

    public String getCreate_time() {
        return create_time;
    }

    public void setCreate_time(String create_time) {
        this.create_time = create_time;
    }

    public String getBid_title() {
        return bid_title;
    }

    public void setBid_title(String bid_title) {
        this.bid_title = bid_title;
    }

    public String getBid_amount() {
        return bid_amount;
    }

    public void setBid_amount(String bid_amount) {
        this.bid_amount = bid_amount;
    }

    public String getBid_interest() {
        return bid_interest;
    }

    public void setBid_interest(String bid_interest) {
        this.bid_interest = bid_interest;
    }

    public String getBid_period() {
        return bid_period;
    }

    public void setBid_period(String bid_period) {
        this.bid_period = bid_period;
    }

    public String getBid_credit_level() {
        return bid_credit_level;
    }

    public void setBid_credit_level(String bid_credit_level) {
        this.bid_credit_level = bid_credit_level;
    }

    public String getBid_serviceFee() {
        return bid_serviceFee;
    }

    public void setBid_serviceFee(String bid_serviceFee) {
        this.bid_serviceFee = bid_serviceFee;
    }
}
