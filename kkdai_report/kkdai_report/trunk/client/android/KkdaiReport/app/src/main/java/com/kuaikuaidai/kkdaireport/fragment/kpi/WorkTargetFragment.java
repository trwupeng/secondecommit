package com.kuaikuaidai.kkdaireport.fragment.kpi;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.adapter.MyFragmentAdapter;
import com.kuaikuaidai.kkdaireport.comm.CallbackInterface;
import com.shizhefei.view.indicator.FixedIndicatorView;
import com.shizhefei.view.indicator.IndicatorViewPager;
import com.shizhefei.view.indicator.slidebar.ColorBar;
import com.shizhefei.view.indicator.transition.OnTransitionTextListener;
import com.shizhefei.view.viewpager.SViewPager;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

import static com.kuaikuaidai.kkdaireport.util.ColorUtil.black;
import static com.kuaikuaidai.kkdaireport.util.ColorUtil.red;
import static com.kuaikuaidai.kkdaireport.util.ColorUtil.redMain;

/**
 * 目标管理-工作目标
 */
public class WorkTargetFragment extends Fragment implements CallbackInterface {


    @BindView(R.id.cus_indicator)
    FixedIndicatorView cusIndicator;
    @BindView(R.id.cus_viewpager)
    SViewPager cusViewpager;
    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;

    private String[] mTitles = new String[4];
    private List<Fragment> mList;
    private IndicatorViewPager mIndicatorViewPager = null;
    private Fragment mQuarterFragment, mMonthFragment, mWeekFragment, mDayFragment;

    public WorkTargetFragment() {
    }

    public static WorkTargetFragment newInstance() {
        WorkTargetFragment fragment = new WorkTargetFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_work_target, container, false);
        ButterKnife.bind(this, view);
        initViewPager();
        return view;
    }


    private void initViewPager() {
        tvTitle.setText(R.string.work_target);
        mTitles[0] = getString(R.string.quarter_target);
        mTitles[1] = getString(R.string.month_target);
        mTitles[2] = getString(R.string.week_target);
        mTitles[3] = getString(R.string.day_target);

        mQuarterFragment = TargetQuarterFragment.newInstance();
        mMonthFragment = TargetMonthFragment.newInstance();
        mWeekFragment = TargetWeekFragment.newInstance();
        mDayFragment = TargetDayFragment.newInstance();

        mList = new ArrayList<>();
        mList.add(mQuarterFragment);
        mList.add(mMonthFragment);
        mList.add(mWeekFragment);
        mList.add(mDayFragment);

        cusViewpager.setCanScroll(true);
        cusViewpager.setOffscreenPageLimit(4);
        cusIndicator.setScrollBar(new ColorBar(getActivity().getApplicationContext(), redMain, 4));
        cusIndicator.setOnTransitionListener(new OnTransitionTextListener().setColor(red, black).setSize(15, 15));

        mIndicatorViewPager = new IndicatorViewPager(cusIndicator, cusViewpager);
        mIndicatorViewPager.setPageMargin(0);

        mIndicatorViewPager.setAdapter(new MyFragmentAdapter(getActivity(), getChildFragmentManager(), mTitles, mList));
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {

    }

    @OnClick()
    public void onClick() {
        getActivity().finish();
    }

}
