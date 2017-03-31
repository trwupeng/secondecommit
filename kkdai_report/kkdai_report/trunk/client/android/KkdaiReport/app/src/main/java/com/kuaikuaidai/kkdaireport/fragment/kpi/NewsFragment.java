package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.kuaikuaidai.kkdaireport.R;

/**
 * 目标管理-消息中心-消息
 */
public class NewsFragment extends Fragment {


    public NewsFragment() {
    }


    public static NewsFragment newInstance() {
        NewsFragment fragment = new NewsFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_news, container, false);
        return view;
    }

}
