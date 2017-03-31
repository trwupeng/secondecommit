package com.kuaikuaidai.kkdaireport.parse;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONArray;
import com.alibaba.fastjson.JSONObject;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by zhong.jiye on 2016/9/28.
 */

public class ContractIdsParse {
    private final static String TAG = "ContractIdsParse";

    private static ContractIdsParse contractIdsParse;

    private ContractIdsParse() {

    }

    public static ContractIdsParse getInstance() {
        if (contractIdsParse == null) {
            if (contractIdsParse == null) {
                contractIdsParse = new ContractIdsParse();
            }
        }
        return contractIdsParse;
    }

    private List<ContractIds> contractIds;

    public List<ContractIds> getContractIds() {
        return contractIds;
    }

    public void setContractIds(List<ContractIds> contractIds) {
        this.contractIds = contractIds;
    }

    public void parseContractIds(Object data) {
        try {
            if (data != null) {
                JSONObject object = JSON.parseObject(data.toString());
                if (object != null && object.size() != 0) {
                    if (contractIds == null) {
                        contractIds = new ArrayList<ContractIds>();
                    }
                    for (String key : object.keySet()) {
                        contractIds.add(new ContractIds(key, object.getString(key)));
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseChannel(Object data) {
        try {
            if (data != null) {
                JSONArray array = JSON.parseArray(data.toString());
                if (array != null && array.size() != 0) {
                    if (contractIds == null) {
                        contractIds = new ArrayList<ContractIds>();
                    }
                    for (int i = 0; i < array.size(); i++) {
                        contractIds.add(new ContractIds(null, array.get(i).toString()));
                    }
                }
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
