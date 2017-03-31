package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.UserFinancail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 用户理财明细
 */
public class UserFinancialParse {

    private final static String TAG = "UserFinancialParse";

    private static UserFinancialParse parse;

    private UserFinancialParse() {

    }

    public static UserFinancialParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new UserFinancialParse();
            }
        }
        return parse;
    }

    private List<UserFinancail> list;

    public List<UserFinancail> getList() {
        return list;
    }

    public void setList(List<UserFinancail> list) {
        this.list = list;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<UserFinancail>();
                }
                list = JSON.parseArray(data.toString(), UserFinancail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }


}
