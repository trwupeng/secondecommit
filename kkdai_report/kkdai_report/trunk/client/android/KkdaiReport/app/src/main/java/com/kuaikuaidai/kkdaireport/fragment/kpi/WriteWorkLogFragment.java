package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.app.DatePickerDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.Calendar;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 目标管理-工作日志-书写日志
 */
public class WriteWorkLogFragment extends Fragment {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_start)
    TextView tvStart;

    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay;
    private String startData;

    public WriteWorkLogFragment() {
    }


    public static WriteWorkLogFragment newInstance() {
        WriteWorkLogFragment fragment = new WriteWorkLogFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_write_log, container, false);
        ButterKnife.bind(this, view);
        init(DateUtil.getCurrentCalendar());
        return view;
    }

    private void init(Calendar calendar) {
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        String date = DateUtil.format.format(calendar.getTime());
        startYear = year;
        startMonth = month;
        startDay = day;
        startData = date;
        tvStart.setText(startData);
    }

    private void showDialog(int mYear, int mMonth, int mDay) {
        if (dialog == null) {
            dialog = new MyDateDialog(getActivity(), mYear, mMonth, mDay) {
                @Override
                public void DateChanged(int year, int month, int day, String date) {
                    startDay = day;
                    startMonth = month;
                    startYear = year;
                    startData = date;
                    tvStart.setText(startData);
                }
            };
        } else {
            dialog.updateDate(startYear, startMonth, startDay);
        }
        if (!dialog.isShowing()) {
            dialog.show();
        }
    }

    @OnClick({R.id.bt_query, R.id.tv_start})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.bt_query:
                break;
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay);
                break;
        }
    }
}
