package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/14.
 */

public class LoanDetail implements Serializable {
    private String customer_name;
    private String customer_realname;
    private String bid_title;
    private String amount;
    private String create_time;

    public LoanDetail() {
    }

    public String getCustomer_name() {
        return customer_name;
    }

    public void setCustomer_name(String customer_name) {
        this.customer_name = customer_name;
    }

    public String getCustomer_realname() {
        return customer_realname;
    }

    public void setCustomer_realname(String customer_realname) {
        this.customer_realname = customer_realname;
    }

    public String getBid_title() {
        return bid_title;
    }

    public void setBid_title(String bid_title) {
        this.bid_title = bid_title;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getCreate_time() {
        return create_time;
    }

    public void setCreate_time(String create_time) {
        this.create_time = create_time;
    }
}
