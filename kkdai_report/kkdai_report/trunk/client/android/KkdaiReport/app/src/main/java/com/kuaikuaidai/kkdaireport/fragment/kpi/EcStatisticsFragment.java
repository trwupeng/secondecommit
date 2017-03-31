package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.app.DatePickerDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
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
 * 目标管理-EC统计
 */
public class EcStatisticsFragment extends Fragment {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.tv_user_id)
    TextView tvUserId;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_clear_query)
    TextView btClearQuery;
    @BindView(R.id.bt_group_check)
    TextView btGroupCheck;

    private DatePickerDialog mDialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;

    public EcStatisticsFragment() {
    }


    public static EcStatisticsFragment newInstance() {
        EcStatisticsFragment fragment = new EcStatisticsFragment();
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_ec_statistics, container, false);
        ButterKnife.bind(this, view);
        initDate();
        return view;
    }

    private void initDate() {
        tvTitle.setText(R.string.ec_statistics);
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

    @OnClick({R.id.ll_back, R.id.tv_start, R.id.tv_end, R.id.bt_query, R.id.bt_clear_query, R.id.bt_group_check})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.ll_back:
                getActivity().finish();
                break;
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay, DialogType.START);
                break;
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay, DialogType.END);
                break;
            case R.id.bt_query:
                break;
            case R.id.bt_clear_query:
                tvUserId.setText("");
                init(DateUtil.getLastMondayCalendar(), DialogType.START);
                init(DateUtil.getLastSundayCalendar(), DialogType.END);
                break;
            case R.id.bt_group_check:
                break;
        }
    }
}
