package com.kuaikuaidai.kkdaireport.bean;

import java.io.Serializable;

/**
 * Created by zhong.jiye on 2016/9/26.
 */

public class AppTraffic implements Serializable {
    private int new_user;
    private int active_user;
    private int launches_user;
    private String startToEnd;

    public int getNew_user() {
        return new_user;
    }

    public void setNew_user(int new_user) {
        this.new_user = new_user;
    }

    public int getActive_user() {
        return active_user;
    }

    public void setActive_user(int active_user) {
        this.active_user = active_user;
    }

    public int getLaunches_user() {
        return launches_user;
    }

    public void setLaunches_user(int launches_user) {
        this.launches_user = launches_user;
    }

    public String getStartToEnd() {
        return startToEnd;
    }

    public void setStartToEnd(String startToEnd) {
        this.startToEnd = startToEnd;
    }

    public AppTraffic(String startToEnd) {
        this.startToEnd = startToEnd;
    }

    public AppTraffic(int new_user, int active_user, int launches_user) {
        this.new_user = new_user;
        this.active_user = active_user;
        this.launches_user = launches_user;
    }

    public AppTraffic(int new_user, int active_user, int launches_user, String startToEnd) {
        this.new_user = new_user;
        this.active_user = active_user;
        this.launches_user = launches_user;
        this.startToEnd = startToEnd;
    }

    public AppTraffic() {
    }
}
