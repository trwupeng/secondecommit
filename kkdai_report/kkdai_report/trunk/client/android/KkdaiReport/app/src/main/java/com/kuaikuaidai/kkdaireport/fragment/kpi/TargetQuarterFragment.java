package com.kuaikuaidai.kkdaireport.fragment.kpi;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.cusview.MyDatePickerDialog;
import com.kuaikuaidai.kkdaireport.cusview.QuarterPop;

import java.util.ArrayList;
import java.util.Calendar;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 目标管理-工作目标-季度目标
 */
public class TargetQuarterFragment extends Fragment implements AdapterView.OnItemClickListener {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_year)
    TextView tvYear;
    @BindView(R.id.tv_quarter)
    TextView tvQuarter;

    private MyDatePickerDialog mYearDateDialog;
    private QuarterPop mQuarterPop;
    private Calendar mCalendar;
    private String mQuarter;
    private int mYear;
    private ArrayList<String> mQuarterList;


    public TargetQuarterFragment() {
    }


    public static TargetQuarterFragment newInstance() {
        TargetQuarterFragment fragment = new TargetQuarterFragment();
        return fragment;
    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_target_quarter, container, false);
        ButterKnife.bind(this, view);
        init();
        return view;
    }

    private void init() {
        mCalendar = Calendar.getInstance();
        initQuarterDatas();
        setQuarter(0);
        mYear = mCalendar.get(Calendar.YEAR);
        tvYear.setText(String.valueOf(mYear));
    }

    private void initQuarterDatas() {
        mQuarterList = new ArrayList<>();
        mQuarterList.add(getString(R.string.q1th));
        mQuarterList.add(getString(R.string.q2th));
        mQuarterList.add(getString(R.string.q3th));
        mQuarterList.add(getString(R.string.q4th));
    }

    private void showDialog() {
        if (mYearDateDialog == null) {
            mYearDateDialog = new MyDatePickerDialog(getActivity(), mCalendar.get(Calendar.YEAR)) {
                @Override
                public void DateChanged(int year, int month, int day) {
                    mYear = year;
                    tvYear.setText(String.valueOf(mYear));
                }
            };
        } else {
            mYearDateDialog.updateDate(mYear, MyDatePickerDialog.defaultData, MyDatePickerDialog.defaultData);
        }
        if (!mYearDateDialog.isShowing()) {
            mYearDateDialog.show();
        }
    }


    private void showPop() {
        if (mQuarterPop == null) {
            mQuarterPop = new QuarterPop(getActivity(), mQuarterList);
            mQuarterPop.setOnItemClickListener(this);
        }
        if (!mQuarterPop.isShowing()) {
            mQuarterPop.showAsDropDown(tvQuarter);
        }
    }

    @OnClick({R.id.bt_query, R.id.tv_year, R.id.tv_quarter})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.bt_query:
                break;
            case R.id.tv_year:
                showDialog();
                break;
            case R.id.tv_quarter:
                showPop();
                break;
        }
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        setQuarter(position);
        mQuarterPop.dismiss();
    }


    private void setQuarter(int position) {
        mQuarter = mQuarterList.get(position);
        tvQuarter.setText(mQuarter);
    }

}
