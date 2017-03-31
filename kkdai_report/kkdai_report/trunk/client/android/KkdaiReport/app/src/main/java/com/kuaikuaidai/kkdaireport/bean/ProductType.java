package com.kuaikuaidai.kkdaireport.bean;

/**
 * Created by zhong.jiye on 2016/10/17.
 */

public class ProductType {
    private String shefId;
    private String shefName;

    public ProductType() {
    }

    public ProductType(String shefId, String shefName) {
        this.shefId = shefId;
        this.shefName = shefName;
    }

    public String getShefId() {
        return shefId;
    }

    public void setShefId(String shefId) {
        this.shefId = shefId;
    }

    public String getShefName() {
        return shefName;
    }

    public void setShefName(String shefName) {
        this.shefName = shefName;
    }
}
