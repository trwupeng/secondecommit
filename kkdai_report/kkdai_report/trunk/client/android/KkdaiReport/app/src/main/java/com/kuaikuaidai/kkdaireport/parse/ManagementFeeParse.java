package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.ManagementDetail;
import com.kuaikuaidai.kkdaireport.bean.ManagementFee;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 管理费解析
 */
public class ManagementFeeParse {

    private final static String TAG = "ManagementFeeParse";

    private static ManagementFeeParse parse;

    private ManagementFeeParse() {

    }

    public static ManagementFeeParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new ManagementFeeParse();
            }
        }
        return parse;
    }

    private List<ManagementFee> managementFeeList;
    private List<ManagementDetail> managementDetailList;

    public List<ManagementFee> getManagementFeeList() {
        return managementFeeList;
    }

    public void setManagementFeeList(List<ManagementFee> managementFeeList) {
        this.managementFeeList = managementFeeList;
    }

    public List<ManagementDetail> getManagementDetailList() {
        return managementDetailList;
    }

    public void setManagementDetailList(List<ManagementDetail> managementDetailList) {
        this.managementDetailList = managementDetailList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (managementFeeList == null) {
                    managementFeeList = new ArrayList<ManagementFee>();
                } else {
                    managementFeeList.clear();
                }
                managementFeeList = JSON.parseArray(data.toString(), ManagementFee.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (managementDetailList == null) {
                    managementDetailList = new ArrayList<ManagementDetail>();
                } else {
                    managementDetailList.clear();
                }
                managementDetailList = JSON.parseArray(data.toString(), ManagementDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
