package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.VoucherUseDetail;
import com.kuaikuaidai.kkdaireport.bean.VoucherUse;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 优惠券使用解析
 */
public class VoucherUseParse {

    private final static String TAG = "VoucherUseParse";

    private static VoucherUseParse parse;

    private VoucherUseParse() {

    }

    public static VoucherUseParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new VoucherUseParse();
            }
        }
        return parse;
    }

    private List<VoucherUse> list;
    private List<VoucherUseDetail> voucherUseDetails;

    public List<VoucherUse> getList() {
        return list;
    }

    public void setList(List<VoucherUse> list) {
        this.list = list;
    }

    public List<VoucherUseDetail> getVoucherUseDetails() {
        return voucherUseDetails;
    }

    public void setVoucherUseDetails(List<VoucherUseDetail> voucherUseDetails) {
        this.voucherUseDetails = voucherUseDetails;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<VoucherUse>();
                } else {
                    list.clear();
                }
                list = JSON.parseArray(data.toString(), VoucherUse.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (voucherUseDetails == null) {
                    voucherUseDetails = new ArrayList<VoucherUseDetail>();
                } else {
                    voucherUseDetails.clear();
                }
                voucherUseDetails = JSON.parseArray(data.toString(), VoucherUseDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void clear() {
        voucherUseDetails = null;
        list = null;
        VoucherGrantParse.getInstance().setVoucherSum(null);
    }

}
