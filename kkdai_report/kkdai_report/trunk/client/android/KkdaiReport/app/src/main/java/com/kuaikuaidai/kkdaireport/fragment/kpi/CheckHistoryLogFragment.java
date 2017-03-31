package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.kuaikuaidai.kkdaireport.R;

/**
 * 目标管理-工作日志-查看历史日志
 */
public class CheckHistoryLogFragment extends Fragment {


    public CheckHistoryLogFragment() {
    }


    public static CheckHistoryLogFragment newInstance() {
        CheckHistoryLogFragment fragment = new CheckHistoryLogFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_history_log, container, false);
        return view;
    }

}
