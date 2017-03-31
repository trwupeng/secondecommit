package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.app.DatePickerDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.cusview.MyDatePickerDialog;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.Calendar;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 目标管理-工作目标-周目标
 */
public class TargetWeekFragment extends Fragment {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;

    private DatePickerDialog mDialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;

    public TargetWeekFragment() {
    }


    public static TargetWeekFragment newInstance() {
        TargetWeekFragment fragment = new TargetWeekFragment();
        return fragment;
    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_target_week, container, false);
        ButterKnife.bind(this, view);
        init();
        return view;
    }

    private void init() {
        init(DateUtil.getLastMondayCalendar(), DialogType.START);
        init(DateUtil.getLastSundayCalendar(), DialogType.END);
    }

    private void init(Calendar calendar, DialogType type) {
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        String date = DateUtil.format.format(calendar.getTime());
        switch (type) {
            case START:
                startYear = year;
                startMonth = month;
                startDay = day;
                startData = date;
                tvStart.setText(startData);
                break;
            case END:
                endYear = year;
                endMonth = month;
                endDay = day;
                endData = date;
                tvEnd.setText(endData);
                break;
        }
    }

    private void showDialog(int year, int month, int day, final DialogType type) {
        mDialog = new MyDatePickerDialog(getActivity(), year, month, day) {
            @Override
            public void DateChanged(int year, int month, int day) {
                Calendar calendar = Calendar.getInstance();
                calendar.set(year, month, day);
                init(calendar, type);
            }
        };
        mDialog.show();
    }

    @OnClick({R.id.bt_query, R.id.tv_start, R.id.tv_end})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.bt_query:
                break;
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay, DialogType.START);
                break;
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay, DialogType.END);
                break;
        }
    }
}
