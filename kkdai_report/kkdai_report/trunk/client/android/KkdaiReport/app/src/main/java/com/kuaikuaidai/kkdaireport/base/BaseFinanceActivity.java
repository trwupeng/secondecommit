package com.kuaikuaidai.kkdaireport.base;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Parcelable;
import android.support.v4.widget.DrawerLayout;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;

import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.activity.kpi.KpiHomeActivity;
import com.kuaikuaidai.kkdaireport.bean.Menu;
import com.kuaikuaidai.kkdaireport.comm.CallbackInterface;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.cusview.SuperCustomToast;
import com.kuaikuaidai.kkdaireport.util.AppManager;

import java.util.ArrayList;

import static com.kuaikuaidai.kkdaireport.activity.kpi.KpiHomeActivity.ARG_PARAM1;
import static com.kuaikuaidai.kkdaireport.activity.kpi.KpiHomeActivity.ARG_PARAM2;


public abstract class BaseFinanceActivity extends Activity implements CallbackInterface {

    protected Context mContext = null;
    protected SuperCustomToast mToast;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        mContext = this;
        mToast = SuperCustomToast.getInstance(mContext);
        AppManager.getAppManager().addActivity(this);
    }

    private QuickAdapter<Menu> mMenuAdapter;
    private ArrayList<Menu> mMenuList = null;
    private String mCurrentUrl = null;
    private boolean init = false;

    @Override
    protected void onResume() {
        super.onResume();
        if (!init) {
            mMenuList = getIntent().getParcelableArrayListExtra(ARG_PARAM1);
            mCurrentUrl = getIntent().getStringExtra(ARG_PARAM2);
            mMenuAdapter = new QuickAdapter<Menu>(mContext, R.layout.item_left, mMenuList) {
                @Override
                protected void convert(BaseAdapterHelper helper, Menu item) {
                    helper.setText(R.id.tv_title, item.getCapt());
                }
            };
            ListView listView = (ListView) findViewById(R.id.menu_list);
            listView.setAdapter(mMenuAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                    if (!mMenuList.isEmpty() && mMenuList.get(position) != null) {
                        if (!TextUtils.isEmpty(mMenuList.get(position).getUrl())) {
                            String url = mMenuList.get(position).getUrl().replace("?", "").trim().substring(1);
                            if (!TextUtils.isEmpty(url)) {
                                if (url.equals(mCurrentUrl)) {
                                    ((DrawerLayout) findViewById(R.id.drawer_layout)).closeDrawers();
                                } else {
                                    Class<?> cls = CommUrlConstant.matchMap.get(url);
                                    if (cls != null) {
                                        Intent intent = new Intent(mContext, cls);
                                        intent.putParcelableArrayListExtra(KpiHomeActivity.ARG_PARAM1, (ArrayList<? extends Parcelable>) mMenuList);
                                        startActivity(intent);
                                        finish();
                                    }
                                }
                            }
                        }
                    }
                }
            });
            init = true;
        }
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
    public abstract void onCallback(long code, String msg, Exception e, String api, String useData);
}
