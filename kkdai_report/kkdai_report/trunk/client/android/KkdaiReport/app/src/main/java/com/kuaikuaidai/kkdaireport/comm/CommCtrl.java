package com.kuaikuaidai.kkdaireport.comm;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.os.Handler;
import android.os.Message;

import com.kuaikuaidai.kkdaireport.BuildConfig;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseApplication;
import com.kuaikuaidai.kkdaireport.config.Configure;
import com.kuaikuaidai.kkdaireport.cusview.CommDialogFactory;
import com.kuaikuaidai.kkdaireport.util.Logger;
import com.kuaikuaidai.kkdaireport.util.PhoneNetUtils;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;

class ResInfo {
    public long httpCode;
    public String data;
    public Exception e;
    public String cookie;
    public int type;
    public boolean needErr;
    public long sn;
    public CallbackInterface cb;
    Context act;
    public String api;
    public String userData;
}

public class CommCtrl {
    static private CommThread _uiThread = null;
    static private CommThread _workThread = null;
    static private CommThread _silenceThread = null;
    static private boolean _started = false;

    static private String _httpHead = null;
    static private String _apiUrl = null;
    static private String _domain = null;


    static private Handler _handler = null;

    static void start() {
        if (_started) {
            return;
        }

        _started = true;

        _handler = new Handler() {
            public void handleMessage(Message msg) {
                ResInfo info = (ResInfo) msg.obj;
                if (null != info.cookie) {
                    setCookie(info.cookie);
                }
                CommParser.parse(info.httpCode, info.data, info.e, info.type, info.needErr, info.cb, info.sn, info.act, info.api, info.userData);
                if (null != info.act) {
                    CommDialogFactory.dismissLoadingDialog();
                }
            }
        };

        init();

        _uiThread = CommThread.create(1, _handler);
        _workThread = CommThread.create(1, _handler);
        _silenceThread = CommThread.create(1, _handler);
    }

	static void init()
	{
		_httpHead = Configure.REQ_PROTOCOL;
        _domain = Configure.REALM_NAME;
		_apiUrl = BuildConfig.API_IP;
	}


    public static CommInfo send(String url, HashMap<String, String> param, CallbackInterface cb, int type, Context act, String loading, boolean needErr, HashMap<String, String> exParam, String api, String userData) {
        String tmpUrl = url;
        if (null != param) {
            Set<String> keys = param.keySet();
            Iterator<String> itr = keys.iterator();
            while (itr.hasNext()) {
                String key = itr.next();
                String value = param.get(key);
                if (null != value) {
                    tmpUrl = tmpUrl + "&" + key + "=" + value;
                }
            }
        }
        tmpUrl = tmpUrl + "&__VIEW__=json";
        return doSend(tmpUrl, cb, type, act, loading, needErr, exParam, api, userData);
    }

    public static String makeApi(String app) {
        return (_httpHead + _apiUrl + "/" + app+"?");
    }

    public static String getDomain() {
        return _domain;
    }

    static CommInfo doSend(String url, final CallbackInterface cb, final int type, final Context act, String loading, final boolean needErr, HashMap<String, String> param, String api, String userData) {
        start();

        if (null != act) {
            if (null == loading) {
                loading = act.getString(R.string.exex_data);
            }
            CommDialogFactory.showLoadingDialog(act, loading);
        }

        CommInfo info = new CommInfo();
        Logger.i("CommSender", url);
        info.setUrl(url);
        info.setApi(api);
        info.setCallback(new CallbackInterfaceInner() {

            @Override
            public void onCallback(long httpCode, String data, Exception e, long sn, String cookie, String api, String userData) {
                ResInfo res = new ResInfo();
                res.httpCode = httpCode;
                res.e = e;
                res.data = data;
                res.cookie = cookie;
                res.sn = sn;
                res.type = type;
                res.needErr = needErr;
                res.cb = cb;
                res.act = act;
                res.api = api;
                res.userData = userData;
                Message msg = new Message();
                msg.obj = res;
                _handler.sendMessage(msg);
            }

        });

        if (null != param) {
            String s = param.get("timeout");
            if (null != s) {
                info.setTimeout(Integer.parseInt(s));
            }

            s = param.get("retry");
            if (null != s) {
                info.setRetry(Integer.parseInt(s));
            }

            s = param.get("timer");
            if (null != s) {
                info.setTimeout(Integer.parseInt(s));
            }
        }

        info.setUserData(userData);

        info.setCookie(getCookie());

        info.setType(type);

        if (!PhoneNetUtils.checkNetwork()) {
            ResInfo res = new ResInfo();
            res.httpCode = CommConstant.CODE_NO_NETWORK;
            res.e = null;
            res.data = null;
            res.cookie = null;
            res.sn = 0;
            res.type = type;
            res.needErr = needErr;
            res.cb = cb;
            res.act = act;
            res.api = api;
            res.userData = userData;
            Message msg = new Message();
            msg.obj = res;
            _handler.sendMessage(msg);
            return info;
        }

        if (0 == info.getTimer()) {
            addComm(info);
        }

        return info;
    }

    public static void addComm(CommInfo info) {
        switch (info.getType()) {
            case CommConstant.CT_UI:
                _uiThread.addCommInfo(info);
                break;

            case CommConstant.CT_WORK:
                _workThread.addCommInfo(info);
                break;

            case CommConstant.CT_BG:
                _silenceThread.addCommInfo(info);
                break;

            default:
                _uiThread.addCommInfo(info);
                break;
        }
    }

    public static String getCookie() {
        String cookie = CommConstant.COOKIE_PREFIX + "null";
        try {
            Context ctx = BaseApplication.getInstance().getApplicationContext();
            SharedPreferences sp = ctx.getSharedPreferences(CommConstant.CONFIG_FILE, Activity.MODE_PRIVATE);
            cookie = sp.getString(CommConstant.COOKIE_KEY, "");
            if (null == cookie || cookie.isEmpty()) {
                cookie = CommConstant.COOKIE_PREFIX + "null";
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return cookie;
    }

    public static void setCookie(String cookie) {
        if (null == cookie || cookie.isEmpty()) {
            return;
        }
        try {
            Context ctx = BaseApplication.getInstance().getApplicationContext();
            SharedPreferences sp = ctx.getSharedPreferences(CommConstant.CONFIG_FILE, Activity.MODE_PRIVATE);
            String tmp = sp.getString(CommConstant.COOKIE_KEY, "");
            if (null == tmp || tmp.isEmpty()) {
                Editor edit = sp.edit();
                edit.putString(CommConstant.COOKIE_KEY, cookie);
                edit.commit();
            } else if (tmp.trim().equals("imei:null"))//如果当前的cookie是此种格式则需要重新保存cookie
            {
                Editor edit = sp.edit();
                edit.putString(CommConstant.COOKIE_KEY, cookie);
                edit.commit();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
