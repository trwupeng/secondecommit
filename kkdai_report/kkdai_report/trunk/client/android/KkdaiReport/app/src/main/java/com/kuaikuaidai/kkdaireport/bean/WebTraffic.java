package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * 网页流量
 */
public class WebTraffic implements Serializable {


    private int pv_count;
    private int visitor_count;
    private int ip_count;
    private String startToEnd;


    public WebTraffic() {
    }

    public int getPv_count() {
        return pv_count;
    }

    public void setPv_count(int pv_count) {
        this.pv_count = pv_count;
    }

    public int getVisitor_count() {
        return visitor_count;
    }

    public void setVisitor_count(int visitor_count) {
        this.visitor_count = visitor_count;
    }

    public int getIp_count() {
        return ip_count;
    }

    public void setIp_count(int ip_count) {
        this.ip_count = ip_count;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }
}