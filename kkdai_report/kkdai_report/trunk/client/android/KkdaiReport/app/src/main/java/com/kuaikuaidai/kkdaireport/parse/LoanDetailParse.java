package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.LoanDetail;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 放款明细
 */
public class LoanDetailParse {

    private final static String TAG = "LoanDetailParse";

    private static LoanDetailParse parse;

    private LoanDetailParse() {

    }

    public static LoanDetailParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new LoanDetailParse();
            }
        }
        return parse;
    }

    private List<LoanDetail> list;

    public List<LoanDetail> getList() {
        return list;
    }

    public void setList(List<LoanDetail> list) {
        this.list = list;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<LoanDetail>();
                }
                list = JSON.parseArray(data.toString(), LoanDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
