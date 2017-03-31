package com.kuaikuaidai.kkdaireport.base;


import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;

import com.kuaikuaidai.kkdaireport.comm.CallbackInterface;
import com.kuaikuaidai.kkdaireport.cusview.SuperCustomToast;
import com.kuaikuaidai.kkdaireport.util.AppManager;


public abstract class BaseFragmentActivity extends FragmentActivity implements CallbackInterface {

    protected Context mContext = null;
    protected SuperCustomToast mToast;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        mContext = this;
        mToast = SuperCustomToast.getInstance(mContext);
        AppManager.getAppManager().addActivity(this);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        AppManager.getAppManager().finishActivity(mContext);
    }

    protected void showToastShort(String content) {
        mToast.show(content, 1000);
    }

    protected void showToastShort(int resId) {
        mToast.show(getString(resId), 1000);
    }

    protected void showToastLong(String content) {
        mToast.show(content, 3000);
    }

    protected void showToastLong(int resId) {
        mToast.show(getString(resId), 3000);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {

    }
}
