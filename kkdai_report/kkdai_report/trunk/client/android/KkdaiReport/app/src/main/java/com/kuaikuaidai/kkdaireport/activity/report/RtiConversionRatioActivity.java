package com.kuaikuaidai.kkdaireport.activity.report;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.RtiConversionRatio;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.InvestAgainNumbersParse;
import com.kuaikuaidai.kkdaireport.parse.RtiConversionRatioParse;
import com.kuaikuaidai.kkdaireport.util.ColorUtil;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import lecho.lib.hellocharts.formatter.SimpleColumnChartValueFormatter;
import lecho.lib.hellocharts.model.Axis;
import lecho.lib.hellocharts.model.AxisValue;
import lecho.lib.hellocharts.model.Column;
import lecho.lib.hellocharts.model.ColumnChartData;
import lecho.lib.hellocharts.model.SubcolumnValue;
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 注册至理财转化率
 */
public class RtiConversionRatioActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_rti_conversion_ratio)
    ColumnChartView ccvRtiConversionRatio;
    @BindView(R.id.tv_channel)
    TextView tvChannel;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;

    private ChannelPop channelPop;
    private String startData, endData, channel;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay;
    private int endYear, endMonth, endDay;

    private List<ContractIds> contractIdsList;
    private List<RtiConversionRatio> rtiConversionRatioList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_regist_invest_conversion_ratio);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvRtiConversionRatio.setZoomEnabled(false);

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

        tvChannel.setText(R.string.all);

        getData(startData, endData, channel);
    }

    private void getData(final String start, String end, final String contractId) {
        CommSender.registToInvestConversionRatio(start, end, contractId, this, mContext);
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

        rtiConversionRatioList = RtiConversionRatioParse.getInstance().getRtiConversionRatioList();
        if (rtiConversionRatioList != null && rtiConversionRatioList.size() != 0) {
            tvDataLabel1.setText(rtiConversionRatioList.get(0).getStartToEnd());
            tvDataLabel2.setText(rtiConversionRatioList.get(1).getStartToEnd());
            generateData(rtiConversionRatioList);
            InvestAgainNumbersParse.getInstance().setInvestAgainNumbersList(null);
        } else {
            ccvRtiConversionRatio.setColumnChartData(null);
            showToastShort(R.string.empty_data);
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

    private void generateData(List<RtiConversionRatio> list) {
        int numColumns = 4;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getRealnameCount(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getRealnameCount(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.regist_authentication)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(0).getBindcardCount(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getBindcardCount(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.regist_bingcard)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(list.get(0).getNewRechargeCount(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getNewRechargeCount(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.regist_recharge)));
                    break;
                case 3:
                    values.add(new SubcolumnValue(list.get(0).getNewBuyCount(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getNewBuyCount(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.regist_invest)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setFormatter(new SimpleColumnChartValueFormatter(2));
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(11));
        data.setAxisYLeft(new Axis().setTextSize(12).setTextColor(Color.BLACK).setName(getString(R.string.conversion_ratio)).setHasLines(true).setMaxLabelChars(3));
        ccvRtiConversionRatio.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        RtiConversionRatioParse.getInstance().setRtiConversionRatioList(null);
        ContractIdsParse.getInstance().setContractIds(null);
    }
}
