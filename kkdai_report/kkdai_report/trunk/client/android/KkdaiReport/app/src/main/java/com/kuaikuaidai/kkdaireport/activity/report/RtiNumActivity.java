package com.kuaikuaidai.kkdaireport.activity.report;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.RtiNum;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.RtiNumParse;
import com.kuaikuaidai.kkdaireport.parse.TitleParse;
import com.kuaikuaidai.kkdaireport.util.ColorUtil;
import com.kuaikuaidai.kkdaireport.util.DateUtil;
import com.kuaikuaidai.kkdaireport.util.DensityUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import lecho.lib.hellocharts.formatter.SimpleAxisValueFormatter;
import lecho.lib.hellocharts.model.Axis;
import lecho.lib.hellocharts.model.AxisValue;
import lecho.lib.hellocharts.model.Column;
import lecho.lib.hellocharts.model.ColumnChartData;
import lecho.lib.hellocharts.model.SubcolumnValue;
import lecho.lib.hellocharts.util.ChartUtils;
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 注册至理财人数
 * Created by zhong.jiye on 2016/10/8.
 */

public class RtiNumActivity extends BaseActivity {
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.tv_channel)
    TextView tvChannel;
    @BindView(R.id.tv_lable1)
    TextView tvLable1;
    @BindView(R.id.tv_lable2)
    TextView tvLable2;
    @BindView(R.id.ccv_regist_invest_num1)
    ColumnChartView ccvRegistInvestNum1;
    @BindView(R.id.ccv_regist_invest_num2)
    ColumnChartView ccvRegistInvestNum2;


    private List<ContractIds> contractIdsList;
    private List<RtiNum> rtiNumList;

    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_regist_invest_numbers);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvRegistInvestNum1.setZoomEnabled(false);
        ccvRegistInvestNum2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvRegistInvestNum1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvRegistInvestNum1.setLayoutParams(params1);

        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvRegistInvestNum2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvRegistInvestNum2.setLayoutParams(params2);

        tvChannel.setText(R.string.all);

        Calendar startCalendar = DateUtil.getBoforeTime(7);
        Calendar endCalendar = DateUtil.getBoforeTime(1);

        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);

        startYear = startCalendar.get(Calendar.YEAR);
        startMonth = startCalendar.get(Calendar.MONTH);
        startDay = startCalendar.get(Calendar.DAY_OF_MONTH);

        startData = DateUtil.getBoforeDay(7);
        endData = DateUtil.getBoforeDay(1);

        tvStart.setText(startData);
        tvEnd.setText(endData);
        getData(startData, endData, channel);
    }

    private void getData(final String start, String end, final String contractId) {
        CommSender.registToInvestNumbers(start, end, contractId, this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        if (contractIdsList == null) {
            contractIdsList = ContractIdsParse.getInstance().getContractIds();
            if (channelPop == null && contractIdsList != null && contractIdsList.size() != 0) {
                channelPop = new ChannelPop((Activity) mContext, contractIdsList);
                channelPop.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                    @Override
                    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                        channelPop.dismiss();
                        ContractIds contractIds = contractIdsList.get(position);
                        tvChannel.setText(contractIds.getName());
                        channel = contractIds.getId();
                    }
                });
            }
        }

        rtiNumList = RtiNumParse.getInstance().getRtiNumList();
        if (rtiNumList != null && rtiNumList.size() != 0) {
            int size = rtiNumList.size();
            if (size == 1) {
                tvLable2.setVisibility(View.GONE);
                ccvRegistInvestNum1.setVisibility(View.GONE);
            } else {
                tvLable2.setVisibility(View.VISIBLE);
                ccvRegistInvestNum2.setVisibility(View.VISIBLE);
            }
            for (int i = 0; i < ((size >= 2) ? 2 : size); i++) {
                generateData(rtiNumList, i);
            }
            RtiNumParse.getInstance().setRtiNumList(null);
        }
    }

    @OnClick({R.id.tv_start, R.id.tv_end, R.id.bt_query, R.id.tv_channel, R.id.bt_back})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay, DialogType.START);
                break;
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay, DialogType.END);
                break;
            case R.id.bt_query:
                getData(startData, endData, channel);
                break;
            case R.id.tv_channel:
                if (channelPop != null) {
                    channelPop.showAsDropDown(tvChannel);
                    channelPop.backgroundAlpha((Activity) mContext, 0.5f);
                }
                break;
            case R.id.bt_back:
                finish();
                break;
        }
    }

    private void showDialog(int mYear, int mMonth, int mDay, final DialogType type) {
        dialog = new MyDateDialog(mContext, mYear, mMonth, mDay) {
            @Override
            public void DateChanged(int year, int month, int day, String date) {
                switch (type) {
                    case START:
                        startDay = day;
                        startMonth = month;
                        startYear = year;
                        startData = date;
                        tvStart.setText(startData);
                        break;
                    case END:
                        endDay = day;
                        endMonth = month;
                        endYear = year;
                        endData = date;
                        tvEnd.setText(endData);
                        break;
                }
            }
        };
        dialog.show();
    }

    private void generateData(List<RtiNum> list, int index) {
        int numColumns = 5;//总3列
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        RtiNum rtiNum = list.get(index);
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(rtiNum.getRegisterCount_real_(), getColors(i)));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.regist)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(rtiNum.getRealnameCount_real_(), getColors(i)));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.authentication)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(rtiNum.getBindcardCount_real_(), getColors(i)));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.bingcard)));
                    break;
                case 3:
                    values.add(new SubcolumnValue(rtiNum.getNewRechargeCount_real_(), getColors(i)));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.recharge)));
                    break;
                case 4:
                    values.add(new SubcolumnValue(rtiNum.getNewBuyCount_real_(), getColors(i)));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setStacked(true);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(2));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setTextSize(12).setMaxLabelChars(5).setName(getString(R.string.regist_to_invest_numbers)).setFormatter(new SimpleAxisValueFormatter(0)));
        switch (index) {
            case 0:
                tvLable1.setText(TitleParse.getInstance().getDate3());
                ccvRegistInvestNum1.setColumnChartData(data);
                break;
            case 1:
                tvLable2.setText(TitleParse.getInstance().getDate4());
                ccvRegistInvestNum2.setColumnChartData(data);
                break;
        }

    }

    private int getColors(int index) {
        int color = ChartUtils.pickColor();
        switch (index) {
            case 0:
                color = ColorUtil.color1;
                break;
            case 1:
                color = ColorUtil.color2;
                break;
            case 2:
                color = ColorUtil.color3;
                break;
            case 3:
                color = ColorUtil.color4;
                break;
            case 4:
                color = ColorUtil.color5;
                break;
        }
        return color;
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
    }
}

