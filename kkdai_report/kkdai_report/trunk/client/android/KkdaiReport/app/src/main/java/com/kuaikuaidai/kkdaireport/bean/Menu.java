package com.kuaikuaidai.kkdaireport.bean;

import android.os.Parcel;
import android.os.Parcelable;

import java.io.Serializable;
import java.util.List;


public class Menu implements Serializable, Parcelable {

    private String capt;
    private String url;
    private String options;
    private List<Menu> children;


    public String getCapt() {
        return capt;
    }

    public void setCapt(String capt) {
        this.capt = capt;
    }

    public List<Menu> getChildren() {
        return children;
    }

    public void setChildren(List<Menu> children) {
        this.children = children;
    }

    public String getOptions() {
        return options;
    }

    public void setOptions(String options) {
        this.options = options;
    }

    public String getUrl() {
        return url;
    }

    public void setUrl(String url) {
        this.url = url;
    }

    public Menu(String capt, String url, String options, List<Menu> children) {
        this.capt = capt;
        this.url = url;
        this.options = options;
        this.children = children;
    }

    public Menu() {
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeString(this.capt);
        dest.writeString(this.url);
        dest.writeString(this.options);
        dest.writeTypedList(this.children);
    }

    protected Menu(Parcel in) {
        this.capt = in.readString();
        this.url = in.readString();
        this.options = in.readString();
        this.children = in.createTypedArrayList(Menu.CREATOR);
    }

    public static final Creator<Menu> CREATOR = new Creator<Menu>() {
        @Override
        public Menu createFromParcel(Parcel source) {
            return new Menu(source);
        }

        @Override
        public Menu[] newArray(int size) {
            return new Menu[size];
        }
    };
}
