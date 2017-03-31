package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.BidDetail;
import com.kuaikuaidai.kkdaireport.bean.FailBid;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 流标统计解析
 */
public class FailBidParse {

    private final static String TAG = "FailBidParse";

    private static FailBidParse parse;

    private FailBidParse() {

    }

    public static FailBidParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new FailBidParse();
            }
        }
        return parse;
    }

    private List<FailBid> list;
    private List<BidDetail> bidDetailList;

    public List<FailBid> getList() {
        return list;
    }

    public void setList(List<FailBid> list) {
        this.list = list;
    }

    public List<BidDetail> getBidDetailList() {
        return bidDetailList;
    }

    public void setBidDetailList(List<BidDetail> bidDetailList) {
        this.bidDetailList = bidDetailList;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<FailBid>();
                } else {
                    list.clear();
                }
                list = JSON.parseArray(data.toString(), FailBid.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

    public void parseDetail(Object data) {
        try {
            if (data != null) {
                if (bidDetailList == null) {
                    bidDetailList = new ArrayList<BidDetail>();
                } else {
                    bidDetailList.clear();
                }
                bidDetailList = JSON.parseArray(data.toString(), BidDetail.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
