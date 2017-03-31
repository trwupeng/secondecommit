package com.kuaikuaidai.kkdaireport.util;

import android.app.Activity;
import android.content.Context;
import android.util.DisplayMetrics;
import android.view.WindowManager;

public class DensityUtil {

	/**
	 * 根据手机的分辨率从 dp 的单位 转成为 px(像素)
	 */
	public static int dip2px(Context context, float dpValue) {
		final float scale = context.getResources().getDisplayMetrics().density;
		return (int) (dpValue * scale + 0.5f);
	}

	/**
	 * 根据手机的分辨率从 px(像素) 的单位 转成为 dp
	 */
	public static int px2dip(Context context, float pxValue) {
		final float scale = context.getResources().getDisplayMetrics().density;
		return (int) (pxValue / scale + 0.5f);
	}
	
	/**
     * 获得状态栏的高度
     * 
     * @param context
     * @return
     */
    public static int getStatusHeight(Context context) {
        int statusHeight = -1;
        try {
            Class clazz = Class.forName("com.android.internal.R$dimen");
            Object object = clazz.newInstance();
            int height = Integer.parseInt(clazz.getField("status_bar_height")
                    .get(object).toString());
            statusHeight = context.getResources().getDimensionPixelSize(height);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return statusHeight;
    }
    
    /**
     * 获取当前手机的dpi
     * @param activity
     * @return
     */
    public static int getDpi(Activity activity){
    	DisplayMetrics dm = new DisplayMetrics();
    	activity.getWindowManager().getDefaultDisplay().getMetrics(dm);
    	return dm.densityDpi;
    }
    
    /**
     * 获取屏幕高度
     * @param context
     * @return
     */
    public static int getWindowHeight(Activity context){
    	DisplayMetrics dm = new DisplayMetrics();
    	context.getWindowManager().getDefaultDisplay().getMetrics(dm);
		if(dm.heightPixels!=0){
			return dm.heightPixels;
		}else{
			WindowManager wm=(WindowManager)context.getSystemService(Context.WINDOW_SERVICE);
			return wm.getDefaultDisplay().getHeight();
		}
    }
    /**
     * 获取屏幕宽度
     * @param context
     * @return
     */
    public static int getWindowWidth(Activity context){
    	DisplayMetrics dm = new DisplayMetrics();
    	context.getWindowManager().getDefaultDisplay().getMetrics(dm);
    	if(dm.widthPixels!=0){
    		return dm.widthPixels;
    	}else{
    		WindowManager wm=(WindowManager)context.getSystemService(Context.WINDOW_SERVICE);
    		return wm.getDefaultDisplay().getWidth();
    	}
    }
    
    /**
     * 获取屏幕高度-状态栏高度
     * @param context
     * @return
     */
    public static int getWindowExceptStatusHeight(Activity context){
    	DisplayMetrics dm = new DisplayMetrics();
    	context.getWindowManager().getDefaultDisplay().getMetrics(dm);
		return dm.heightPixels-getStatusHeight(context);
    }
}
