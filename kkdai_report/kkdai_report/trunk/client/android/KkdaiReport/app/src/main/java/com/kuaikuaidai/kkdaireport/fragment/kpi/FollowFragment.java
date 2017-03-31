package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.kuaikuaidai.kkdaireport.R;

/**
 * 目标管理-消息中心-我的跟踪
 */
public class FollowFragment extends Fragment {


    public FollowFragment() {
    }


    public static FollowFragment newInstance() {
        FollowFragment fragment = new FollowFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_follow, container, false);
        return view;
    }

}
