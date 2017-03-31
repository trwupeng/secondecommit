package com.kuaikuaidai.kkdaireport.base;

import android.app.Application;
import android.view.WindowManager;

import com.orhanobut.logger.Logger;

public class BaseApplication extends Application {
    private static BaseApplication mInstance;

    public static BaseApplication getInstance() {
        return mInstance;
    }

    @Override
    public void onCreate() {
        super.onCreate();
        mInstance = this;
        Logger.init().hideThreadInfo();
    }

    private WindowManager.LayoutParams windowParams = new WindowManager.LayoutParams();

    public WindowManager.LayoutParams getWindowParams() {
        return windowParams;
    }

}