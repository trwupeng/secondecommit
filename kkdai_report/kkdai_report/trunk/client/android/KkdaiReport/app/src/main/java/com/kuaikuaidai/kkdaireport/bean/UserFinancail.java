package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/13.
 */

public class UserFinancail implements Serializable {
    private String zhaiyao;
    private String jxamount;
    private String dqamount;
    private String licaidate;

    public UserFinancail() {
    }

    public String getZhaiyao() {
        return zhaiyao;
    }

    public void setZhaiyao(String zhaiyao) {
        this.zhaiyao = zhaiyao;
    }

    public String getJxamount() {
        return jxamount;
    }

    public void setJxamount(String jxamount) {
        this.jxamount = jxamount;
    }

    public String getDqamount() {
        return dqamount;
    }

    public void setDqamount(String dqamount) {
        this.dqamount = dqamount;
    }

    public String getLicaidate() {
        return licaidate;
    }

    public void setLicaidate(String licaidate) {
        this.licaidate = licaidate;
    }
}
