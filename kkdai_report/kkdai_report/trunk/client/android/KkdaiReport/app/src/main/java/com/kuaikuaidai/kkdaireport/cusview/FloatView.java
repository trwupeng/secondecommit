package com.kuaikuaidai.kkdaireport.cusview;

import android.content.Context;
import android.graphics.Rect;
import android.util.Log;
import android.view.MotionEvent;
import android.view.WindowManager;
import android.widget.ImageView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseApplication;


public class FloatView extends ImageView {

    private float mTouchX;
    private float mTouchY;
    private float x;
    private float y;
    private float mStartX;
    private float mStartY;
    private OnClickListener mClickListener;

    private WindowManager windowManager = (WindowManager) getContext()
            .getApplicationContext().getSystemService(Context.WINDOW_SERVICE);
    private WindowManager.LayoutParams windowManagerParams = ((BaseApplication) getContext().getApplicationContext()).getWindowParams();

    public FloatView(Context context) {
        super(context);
        setImageResource(R.drawable.login_log_icon);
    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        //获取到状态栏的高度
        Rect frame = new Rect();
        getWindowVisibleDisplayFrame(frame);
        int statusBarHeight = frame.top - 48;
        System.out.println("statusBarHeight:" + statusBarHeight);
        // 获取相对屏幕的坐标，即以屏幕左上角为原点
        x = event.getRawX();
        y = event.getRawY() - statusBarHeight; // statusBarHeight是系统状态栏的高度
        Log.i("tag", "currX" + x + "====currY" + y);
        switch (event.getAction()) {
            case MotionEvent.ACTION_DOWN: // 捕获手指触摸按下动作
                // 获取相对View的坐标，即以此View左上角为原点
                mTouchX = event.getX();
                mTouchY = event.getY();
                mStartX = x;
                mStartY = y;
                Log.i("tag", "startX" + mTouchX + "====startY"
                        + mTouchY);
                break;

            case MotionEvent.ACTION_MOVE: // 捕获手指触摸移动动作
                updateViewPosition();
                break;

            case MotionEvent.ACTION_UP: // 捕获手指触摸离开动作
                updateViewPosition();
                mTouchX = mTouchY = 0;
                if ((x - mStartX) < 5 && (y - mStartY) < 5) {
                    if (mClickListener != null) {
                        mClickListener.onClick(this);
                    }
                }
                break;
        }
        return true;
    }

    @Override
    public void setOnClickListener(OnClickListener l) {
        this.mClickListener = l;
    }

    private void updateViewPosition() {
        // 更新浮动窗口位置参数
        windowManagerParams.x = (int) (x - mTouchX);
        windowManagerParams.y = (int) (y - mTouchY);
        windowManager.updateViewLayout(this, windowManagerParams); // 刷新显示
    }


//    private WindowManager windowManager = null;
//    private WindowManager.LayoutParams windowManagerParams = null;
//    private FloatView floatView = null;
//    floatView = new FloatView(getApplicationContext());
//    floatView.setOnClickListener(new View.OnClickListener() {
//        @Override
//        public void onClick(View v) {
//
//        }
//    });
//
//    windowManager = (WindowManager) getApplicationContext().getSystemService(Context.WINDOW_SERVICE);
//    windowManagerParams = ((BaseApplication) getApplication()).getWindowParams();
//    windowManagerParams.type = WindowManager.LayoutParams.TYPE_PHONE;
//    windowManagerParams.format = PixelFormat.RGBA_8888;
//    windowManagerParams.flags = WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL
//    | WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE;
//    windowManagerParams.gravity = Gravity.LEFT | Gravity.TOP;
//    windowManagerParams.x = 0;
//    windowManagerParams.y = 0;
//    windowManagerParams.width = WindowManager.LayoutParams.WRAP_CONTENT;
//    windowManagerParams.height = WindowManager.LayoutParams.WRAP_CONTENT;
//    windowManager.addView(floatView, windowManagerParams);


}

