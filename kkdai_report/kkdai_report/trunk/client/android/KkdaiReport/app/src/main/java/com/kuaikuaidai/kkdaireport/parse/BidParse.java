package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.Bid;
import com.kuaikuaidai.kkdaireport.bean.BidDetail;
import com.kuaikuaidai.kkdaireport.bean.BidSum;
import com.kuaikuaidai.kkdaireport.util.Logger;

import java.util.ArrayList;
import java.util.List;

/**
 * 投标统计解析
 */
public class BidParse {

    private final static String TAG = "BidParse";

    private static BidParse parse;

    private BidParse() {

    }

    public static BidParse getInstance() {
        if (parse == null) {
            if (parse == null) {
                parse = new BidParse();
            }
        }
        return parse;
    }

    private BidSum bidSum;
    private List<Bid> list;
    private List<BidDetail> bidDetailList;

    public List<Bid> getList() {
        return list;
    }

    public void setList(List<Bid> list) {
        this.list = list;
    }

    public List<BidDetail> getBidDetailList() {
        return bidDetailList;
    }

    public void setBidDetailList(List<BidDetail> bidDetailList) {
        this.bidDetailList = bidDetailList;
    }

    public BidSum getBidSum() {
        return bidSum;
    }

    public void setBidSum(BidSum bidSum) {
        this.bidSum = bidSum;
    }

    public BidSum getBidSumInstance() {
        if (bidSum == null) {
            bidSum = new BidSum();
        }
        return bidSum;
    }

    public void parseRecord(Object data) {
        try {
            if (data != null) {
                if (list == null) {
                    list = new ArrayList<Bid>();
                } else {
                    list.clear();
                }
                list = JSON.parseArray(data.toString(), Bid.class);
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

    public void clear() {
        list = null;
        bidDetailList = null;
        bidSum = null;
    }

}
