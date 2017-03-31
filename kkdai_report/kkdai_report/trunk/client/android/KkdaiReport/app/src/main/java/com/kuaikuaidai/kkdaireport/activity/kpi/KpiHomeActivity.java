package com.kuaikuaidai.kkdaireport.activity.kpi;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.widget.DrawerLayout;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;

import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseFragmentActivity;
import com.kuaikuaidai.kkdaireport.bean.Menu;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.fragment.kpi.EcStatisticsFragment;
import com.kuaikuaidai.kkdaireport.fragment.kpi.NewsCenterFragment;
import com.kuaikuaidai.kkdaireport.fragment.kpi.WorkLogFragment;
import com.kuaikuaidai.kkdaireport.fragment.kpi.WorkTargetFragment;

import java.util.ArrayList;

import butterknife.BindView;
import butterknife.ButterKnife;

/**
 * 工作管理
 */
public class KpiHomeActivity extends BaseFragmentActivity implements AdapterView.OnItemClickListener {

    public static final String ARG_PARAM1 = "menu";
    public static final String ARG_PARAM2 = "url";
    public static final String TARGET_TAG = "WorkTargetFragment", LOG_TAG = "WorkLogFragment", NEWS_TAG = "NewsCenterFragment", EC_TAG = "EcStatisticsFragment";

    @BindView(R.id.lv_list)
    ListView lvList;
    @BindView(R.id.drawer_layout)
    DrawerLayout drawerLayout;

    private FragmentManager mFragmentManager;
    private FragmentTransaction mTransaction;
    private Fragment mWorkTargetFragment, mWorkLogFragment, mNewsCenterFragment, mEcStatisticsFragment;


    private QuickAdapter<Menu> mAdapter;
    private ArrayList<Menu> mMenuList = null;
    private String mUrl = null;


    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_kpi_home);
        ButterKnife.bind(this);
        initData();
        initMenu();
        show();
    }

    private void initData() {
        mMenuList = getIntent().getParcelableArrayListExtra(ARG_PARAM1);
        mUrl = getIntent().getStringExtra(ARG_PARAM2);
    }

    private void initMenu() {
        mFragmentManager = getSupportFragmentManager();
        mAdapter = new QuickAdapter<Menu>(mContext, R.layout.item_left, mMenuList) {
            @Override
            protected void convert(BaseAdapterHelper helper, Menu item) {
                helper.setText(R.id.tv_title, item.getCapt());
            }
        };
        lvList.setAdapter(mAdapter);
        lvList.setOnItemClickListener(this);
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        if (!mMenuList.isEmpty() && mMenuList.get(position) != null && !TextUtils.isEmpty(mMenuList.get(position).getUrl())) {
            mUrl = mMenuList.get(position).getUrl().replace("?", "").trim().substring(1);
            show();
        }
    }

    private void show() {
        mTransaction = mFragmentManager.beginTransaction();

        mWorkTargetFragment = mFragmentManager.findFragmentByTag(TARGET_TAG);
        mWorkLogFragment = mFragmentManager.findFragmentByTag(LOG_TAG);
        mNewsCenterFragment = mFragmentManager.findFragmentByTag(NEWS_TAG);
        mEcStatisticsFragment = mFragmentManager.findFragmentByTag(EC_TAG);

        if (mWorkTargetFragment != null) {
            mTransaction.hide(mWorkTargetFragment);
        }
        if (mWorkLogFragment != null) {
            mTransaction.hide(mWorkLogFragment);
        }
        if (mNewsCenterFragment != null) {
            mTransaction.hide(mNewsCenterFragment);
        }
        if (mEcStatisticsFragment != null) {
            mTransaction.hide(mEcStatisticsFragment);
        }

        switch (mUrl) {
            case CommUrlConstant.WORK_TARGET://工作目标
                if (mWorkTargetFragment == null) {
                    mWorkTargetFragment = WorkTargetFragment.newInstance();
                    mTransaction.add(R.id.main_fragment, mWorkTargetFragment, TARGET_TAG);
                } else {
                    mTransaction.show(mWorkTargetFragment);
                }
                break;
            case CommUrlConstant.WORK_LOG://工作日志
                if (mWorkLogFragment == null) {
                    mWorkLogFragment = WorkLogFragment.newInstance();
                    mTransaction.add(R.id.main_fragment, mWorkLogFragment, LOG_TAG);
                } else {
                    mTransaction.show(mWorkLogFragment);
                }
                break;
            case CommUrlConstant.NEWS_CENTER://消息中心
                if (mNewsCenterFragment == null) {
                    mNewsCenterFragment = NewsCenterFragment.newInstance();
                    mTransaction.add(R.id.main_fragment, mNewsCenterFragment, NEWS_TAG);
                } else {
                    mTransaction.show(mNewsCenterFragment);
                }
                break;
            case CommUrlConstant.EC_STATIATICS:
                if (mEcStatisticsFragment == null) {
                    mEcStatisticsFragment = EcStatisticsFragment.newInstance();
                    mTransaction.add(R.id.main_fragment, mEcStatisticsFragment, EC_TAG);
                } else {
                    mTransaction.show(mEcStatisticsFragment);
                }
                break;
        }
        mTransaction.commit();
        drawerLayout.closeDrawers();
    }

}
