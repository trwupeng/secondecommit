package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.ServiceFee;
import com.kuaikuaidai.kkdaireport.bean.SeviceFeeDetail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 服务费解析
 */
public class ServiceFeeParse {

    private final static String TAG = "ServiceFeeParse";

    private static ServiceFeeParse parse;

    private ServiceFeeParse() {

    }

    public static ServiceFeeParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new ServiceFeeParse();
            }
        }
        return parse;
    }

    private List<ServiceFee> serviceFeeList;
    private List<SeviceFeeDetail> seviceFeeDetailList;

    public List<ServiceFee> getServiceFeeList() {
        return serviceFeeList;
    }

    public void setServiceFeeList(List<ServiceFee> serviceFeeList) {
        this.serviceFeeList = serviceFeeList;
    }

    public List<SeviceFeeDetail> getSeviceFeeDetailList() {
        return seviceFeeDetailList;
    }

    public void setSeviceFeeDetailList(List<SeviceFeeDetail> seviceFeeDetailList) {
        this.seviceFeeDetailList = seviceFeeDetailList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (serviceFeeList == null) {
                    serviceFeeList = new ArrayList<ServiceFee>();
                } else {
                    serviceFeeList.clear();
                }
                serviceFeeList = JSON.parseArray(data.toString(), ServiceFee.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (seviceFeeDetailList == null) {
                    seviceFeeDetailList = new ArrayList<SeviceFeeDetail>();
                } else {
                    seviceFeeDetailList.clear();
                }
                seviceFeeDetailList = JSON.parseArray(data.toString(), SeviceFeeDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void clear(){
        serviceFeeList=null;
        seviceFeeDetailList=null;
    }

}
