package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.util.Logger;

public class PagerParse {

    private final static String TAG = "PagerParse";

    private static PagerParse parse;

    private PagerParse() {

    }

    public static PagerParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new PagerParse();
            }
        }
        return parse;
    }

    private Pager pager;

    public Pager getPager() {
        return pager;
    }

    public void setPager(Pager pager) {
        this.pager = pager;
    }

    public void parsePager(Object data) {
        try {
            if (data != null) {
                pager = JSON.parseObject(data.toString(), Pager.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }
}
