package com.kuaikuaidai.kkdaireport.activity;


import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.Window;
import android.widget.ImageView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BasePureActivity;
import com.kuaikuaidai.kkdaireport.bean.Menu;
import com.kuaikuaidai.kkdaireport.config.Configure;
import com.kuaikuaidai.kkdaireport.parse.MenuParse;
import com.kuaikuaidai.kkdaireport.util.SpUtil;

import butterknife.BindView;
import butterknife.ButterKnife;

/**
 * 启动页
 */
public class SplashActivity extends BasePureActivity {

    @BindView(R.id.iv_splash)
    ImageView ivSplash;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_splash);
        ButterKnife.bind(this);
    }

    @Override
    protected void onResume() {
        super.onResume();
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                boolean flag = false;
                Menu menu = (Menu) SpUtil.getObject(mContext, "menu", Menu.class);
                if (menu != null && !menu.getChildren().isEmpty()) {
                    flag = true;
                    MenuParse.getInstance().setMenu(menu);
                }
                if (flag) {
                    startActivity(new Intent(mContext, MenuActivity.class));
                } else {
                    startActivity(new Intent(mContext, LoginActivity.class));
                }
                finish();
            }
        }, Configure.STAY_TIME);
    }
}
