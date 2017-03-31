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
 * 目标管理-消息中心
 */
public class NewsCenterFragment extends Fragment {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.cus_indicator)
    FixedIndicatorView cusIndicator;
    @BindView(R.id.cus_viewpager)
    SViewPager cusViewpager;

    private String[] mTitles = new String[2];
    private IndicatorViewPager mIndicatorViewPager = null;
    private Fragment mNewsFragment, mFollowFragment;
    private List<Fragment> mList;

    public NewsCenterFragment() {
    }


    public static NewsCenterFragment newInstance() {
        NewsCenterFragment fragment = new NewsCenterFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_news_center, container, false);
        ButterKnife.bind(this, view);
        initViewPager();
        return view;
    }

    private void initViewPager() {
        tvTitle.setText(R.string.news_center);
        mTitles[0] = getString(R.string.news);
        mTitles[1] = getString(R.string.follow);
        mNewsFragment = NewsFragment.newInstance();
        mFollowFragment = FollowFragment.newInstance();
        mList = new ArrayList<>();
        mList.add(mNewsFragment);
        mList.add(mFollowFragment);
        cusViewpager.setCanScroll(true);
        cusViewpager.setOffscreenPageLimit(2);
        cusIndicator.setScrollBar(new ColorBar(getActivity().getApplicationContext(), redMain, 4));
        cusIndicator.setOnTransitionListener(new OnTransitionTextListener().setColor(red, black).setSize(15, 15));

        mIndicatorViewPager = new IndicatorViewPager(cusIndicator, cusViewpager);
        mIndicatorViewPager.setPageMargin(0);
        mIndicatorViewPager.setAdapter(new MyFragmentAdapter(getActivity(), getChildFragmentManager(), mTitles, mList));
    }

    @OnClick(R.id.ll_back)
    public void onClick() {
        getActivity().finish();
    }
}
