package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/19.
 */

public class VoucherSum implements Serializable {
    private String sumDixian;
    private String sumTixian;
    private String sumJiaXi;
    private String sumFanxian;

    public String getSumDixian() {
        return sumDixian;
    }

    public void setSumDixian(String sumDixian) {
        this.sumDixian = sumDixian;
    }

    public String getSumTixian() {
        return sumTixian;
    }

    public void setSumTixian(String sumTixian) {
        this.sumTixian = sumTixian;
    }

    public String getSumJiaXi() {
        return sumJiaXi;
    }

    public void setSumJiaXi(String sumJiaXi) {
        this.sumJiaXi = sumJiaXi;
    }

    public String getSumFanxian() {
        return sumFanxian;
    }

    public void setSumFanxian(String sumFanxian) {
        this.sumFanxian = sumFanxian;
    }

    public VoucherSum() {
    }


    public String toString(int type) {
        String content = null;
        switch (type) {
            case 1:
                content = "发放";
                break;
            case 2:
                content = "使用";
                break;
        }
        return "抵现券" + content + sumDixian + "元, 提现券" + content + sumTixian + "元,  加息券" + content + sumJiaXi + "个, 返现券" + content + sumFanxian + "元";
    }
}
