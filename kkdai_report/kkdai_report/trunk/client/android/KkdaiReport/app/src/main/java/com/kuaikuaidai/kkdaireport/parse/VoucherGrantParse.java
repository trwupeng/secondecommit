package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.VoucherGrant;
import com.kuaikuaidai.kkdaireport.bean.VoucherGrantDetail;
import com.kuaikuaidai.kkdaireport.bean.VoucherSum;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 优惠券发放解析
 */
public class VoucherGrantParse {

    private final static String TAG = "VoucherGrantParse";

    private static VoucherGrantParse parse;

    private VoucherGrantParse() {

    }

    public static VoucherGrantParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new VoucherGrantParse();
            }
        }
        return parse;
    }

    private VoucherSum voucherSum;
    private List<VoucherGrant> list;
    private List<VoucherGrantDetail> voucherGrantDetails;

    public VoucherSum getVoucherSum() {
        return voucherSum;
    }

    public void setVoucherSum(VoucherSum voucherSum) {
        this.voucherSum = voucherSum;
    }

    public List<VoucherGrant> getList() {
        return list;
    }

    public void setList(List<VoucherGrant> list) {
        this.list = list;
    }

    public List<VoucherGrantDetail> getVoucherGrantDetails() {
        return voucherGrantDetails;
    }

    public void setVoucherGrantDetails(List<VoucherGrantDetail> voucherGrantDetails) {
        this.voucherGrantDetails = voucherGrantDetails;
    }

    public VoucherSum getVoucherGrantInstance() {
        if (voucherSum == null) {
            voucherSum = new VoucherSum();
        }
        return voucherSum;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<VoucherGrant>();
                } else {
                    list.clear();
                }
                list = JSON.parseArray(data.toString(), VoucherGrant.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (voucherGrantDetails == null) {
                    voucherGrantDetails = new ArrayList<VoucherGrantDetail>();
                } else {
                    voucherGrantDetails.clear();
                }
                voucherGrantDetails = JSON.parseArray(data.toString(), VoucherGrantDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void clear() {
        voucherGrantDetails = null;
        voucherSum = null;
        list = null;
    }

}
