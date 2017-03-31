package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/19.
 */

public class BidSum implements Serializable {
    private String succ_super_amount;
    private String succ_super_count;
    private String succ_normal_amount;
    private String succ_normal_count;
    private String sumSuccAmount;
    private String sumSuccCount;

    public BidSum() {
    }

    public String getSucc_super_amount() {
        return succ_super_amount;
    }

    public void setSucc_super_amount(String succ_super_amount) {
        this.succ_super_amount = succ_super_amount;
    }

    public String getSucc_super_count() {
        return succ_super_count;
    }

    public void setSucc_super_count(String succ_super_count) {
        this.succ_super_count = succ_super_count;
    }

    public String getSucc_normal_amount() {
        return succ_normal_amount;
    }

    public void setSucc_normal_amount(String succ_normal_amount) {
        this.succ_normal_amount = succ_normal_amount;
    }

    public String getSucc_normal_count() {
        return succ_normal_count;
    }

    public void setSucc_normal_count(String succ_normal_count) {
        this.succ_normal_count = succ_normal_count;
    }

    public String getSumSuccAmount() {
        return sumSuccAmount;
    }

    public void setSumSuccAmount(String sumSuccAmount) {
        this.sumSuccAmount = sumSuccAmount;
    }

    public String getSumSuccCount() {
        return sumSuccCount;
    }

    public void setSumSuccCount(String sumSuccCount) {
        this.sumSuccCount = sumSuccCount;
    }

    @Override
    public String toString() {
        return "超级用户成功投资" + succ_super_amount + "元, 成功投资笔数" + succ_super_count
                + ",  非超级用户投资成功" + succ_normal_amount
                + "元, 成功投资笔数" + succ_normal_count
                + ".  总成功投资" + sumSuccAmount
                + "元, 总成功笔数" + sumSuccCount;
    }
}
