package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.cusview.MyDatePickerDialog;

import java.util.Calendar;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 目标管理-工作目标-月目标
 */
public class TargetMonthFragment extends Fragment {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_year_month)
    TextView tvYearMonth;

    private MyDatePickerDialog mYearDateDialog;
    private Calendar mCalendar;
    private int mYear, mMonth;

    public static TargetMonthFragment newInstance() {
        TargetMonthFragment fragment = new TargetMonthFragment();
        return fragment;
    }

    public TargetMonthFragment() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_target_month, container, false);
        ButterKnife.bind(this, view);
        init();
        return view;
    }

    private void init() {
        mCalendar = Calendar.getInstance();
        mYear = mCalendar.get(Calendar.YEAR);
        mMonth = mCalendar.get(Calendar.MONTH) + 1;
        tvYearMonth.setText(mYear + "年" + mMonth + "月");
    }


    private void showDialog() {
        if (mYearDateDialog == null) {
            mYearDateDialog = new MyDatePickerDialog(getActivity(), mCalendar.get(Calendar.YEAR), mCalendar.get(Calendar.MONTH)) {
                @Override
                public void DateChanged(int year, int month, int day) {
                    mYear = year;
                    mMonth = month + 1;
                    tvYearMonth.setText(mYear + "年" + mMonth + "月");
                }
            };
        } else {
            mYearDateDialog.updateDate(mYear, mMonth - 1, MyDatePickerDialog.defaultData);
        }
        if (!mYearDateDialog.isShowing()) {
            mYearDateDialog.show();
        }
    }


    @OnClick({R.id.bt_query, R.id.tv_year_month})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.bt_query:
                break;
            case R.id.tv_year_month:
                showDialog();
                break;
        }
    }


}
