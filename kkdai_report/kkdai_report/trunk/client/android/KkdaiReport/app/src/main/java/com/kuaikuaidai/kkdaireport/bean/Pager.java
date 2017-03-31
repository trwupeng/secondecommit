package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/10/13.
 */

public class Pager implements Serializable {
    private int pageId;
    private int total;
    private int pageSize;
    private int pageCount;

    public Pager() {
    }

    public Pager(int pageId, int total, int pageSize, int pageCount) {
        this.pageId = pageId;
        this.total = total;
        this.pageSize = pageSize;
        this.pageCount = pageCount;
    }

    public int getPageId() {
        return pageId;
    }

    public void setPageId(int pageId) {
        this.pageId = pageId;
    }

    public int getTotal() {
        return total;
    }

    public void setTotal(int total) {
        this.total = total;
    }

    public int getPageSize() {
        return pageSize;
    }

    public void setPageSize(int pageSize) {
        this.pageSize = pageSize;
    }

    public int getPageCount() {
        return pageCount;
    }

    public void setPageCount(int pageCount) {
        this.pageCount = pageCount;
    }
}
