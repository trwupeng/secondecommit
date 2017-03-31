package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/8.
 */

public class RtiNum implements Serializable {
    private int registerCount_real_;
    private int realnameCount_real_;
    private int bindcardCount_real_;
    private int newRechargeCount_real_;
    private int newBuyCount_real_;

    public RtiNum() {
    }

    public RtiNum(int registerCount_real_, int realnameCount_real_, int bindcardCount_real_, int newRechargeCount_real_, int newBuyCount_real_) {
        this.registerCount_real_ = registerCount_real_;
        this.realnameCount_real_ = realnameCount_real_;
        this.bindcardCount_real_ = bindcardCount_real_;
        this.newRechargeCount_real_ = newRechargeCount_real_;
        this.newBuyCount_real_ = newBuyCount_real_;
    }

    public int getRegisterCount_real_() {
        return registerCount_real_;
    }

    public void setRegisterCount_real_(int registerCount_real_) {
        this.registerCount_real_ = registerCount_real_;
    }

    public int getRealnameCount_real_() {
        return realnameCount_real_;
    }

    public void setRealnameCount_real_(int realnameCount_real_) {
        this.realnameCount_real_ = realnameCount_real_;
    }

    public int getBindcardCount_real_() {
        return bindcardCount_real_;
    }

    public void setBindcardCount_real_(int bindcardCount_real_) {
        this.bindcardCount_real_ = bindcardCount_real_;
    }

    public int getNewRechargeCount_real_() {
        return newRechargeCount_real_;
    }

    public void setNewRechargeCount_real_(int newRechargeCount_real_) {
        this.newRechargeCount_real_ = newRechargeCount_real_;
    }

    public int getNewBuyCount_real_() {
        return newBuyCount_real_;
    }

    public void setNewBuyCount_real_(int newBuyCount_real_) {
        this.newBuyCount_real_ = newBuyCount_real_;
    }
}
