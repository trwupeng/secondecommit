package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/12.
 */

public class CashBackDetail implements Serializable {


    private String customer_id;
    private String customer_realname;
    private String customer_cellphone;
    private String create_time;
    private String amount;

    public CashBackDetail() {
    }

    public CashBackDetail(String customer_id, String customer_realname, String customer_cellphone, String create_time, String amount) {
        this.customer_id = customer_id;
        this.customer_realname = customer_realname;
        this.customer_cellphone = customer_cellphone;
        this.create_time = create_time;
        this.amount = amount;
    }

    public String getCustomer_id() {
        return customer_id;
    }

    public void setCustomer_id(String customer_id) {
        this.customer_id = customer_id;
    }

    public String getCustomer_realname() {
        return customer_realname;
    }

    public void setCustomer_realname(String customer_realname) {
        this.customer_realname = customer_realname;
    }

    public String getCustomer_cellphone() {
        return customer_cellphone;
    }

    public void setCustomer_cellphone(String customer_cellphone) {
        this.customer_cellphone = customer_cellphone;
    }

    public String getCreate_time() {
        return create_time;
    }

    public void setCreate_time(String create_time) {
        this.create_time = create_time;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }
}
