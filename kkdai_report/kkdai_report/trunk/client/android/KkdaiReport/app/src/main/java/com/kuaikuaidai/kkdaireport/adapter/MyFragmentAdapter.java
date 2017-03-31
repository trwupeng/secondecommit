package com.kuaikuaidai.kkdaireport.adapter;

import android.content.Context;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.shizhefei.view.indicator.IndicatorViewPager;

import java.util.List;

public class MyFragmentAdapter extends IndicatorViewPager.IndicatorFragmentPagerAdapter {
    private LayoutInflater inflater;
    private String[] mTitles;
    private List<Fragment> mList;

    public MyFragmentAdapter(Context context, FragmentManager fragmentManager, String[] titles, List<Fragment> list) {
        super(fragmentManager);
        inflater = LayoutInflater.from(context.getApplicationContext());
        mTitles = titles;
        mList = list;
    }

    @Override
    public int getCount() {
        return mTitles.length;
    }

    @Override
    public View getViewForTab(int position, View convertView, ViewGroup container) {
        if (convertView == null) {
            convertView = inflater.inflate(R.layout.item_title, container, false);
        }
        TextView textView = (TextView) convertView;
        textView.setText(mTitles[position]);
        textView.setTextSize(14);
        return convertView;
    }

    @Override
    public Fragment getFragmentForPage(int position) {
        return mList.get(position);
    }
}
