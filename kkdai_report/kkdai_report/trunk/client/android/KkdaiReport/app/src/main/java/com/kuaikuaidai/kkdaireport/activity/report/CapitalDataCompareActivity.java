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
import com.kuaikuaidai.kkdaireport.bean.CapitalDataCompare;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.CapitalDataCompareParse;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
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
 * 资金数据对比
 */
public class CapitalDataCompareActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_capital_data_compare)
    ColumnChartView ccvCapitalDataCompare;
    @BindView(R.id.tv_channel)
    TextView tvChannel;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;


    private List<ContractIds> contractIdsList;
    private List<CapitalDataCompare> capitalDataCompareList;
    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_capital_data_compare);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvCapitalDataCompare.setZoomEnabled(false);
        tvChannel.setText(R.string.all);

        Calendar startCalendar =DateUtil.getLastMondayCalendar();
        Calendar endCalendar = DateUtil.getLastSundayCalendar();

        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);

        startYear = startCalendar.get(Calendar.YEAR);
        startMonth = startCalendar.get(Calendar.MONTH);
        startDay = startCalendar.get(Calendar.DAY_OF_MONTH);

        startData = DateUtil.getLastMonday();
        endData = DateUtil.getLastSunday();

        tvStart.setText(startData);
        tvEnd.setText(endData);

        getData(startData, endData, channel);
    }

    private void getData(final String start, String end, final String contractId) {
        CommSender.capitalDataCompare(start, end, contractId, this, mContext);
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
                        channel = contractIds.getName();
                    }
                });
            }
        }

        capitalDataCompareList = CapitalDataCompareParse.getInstance().getCapitalDataCompareList();
        if (capitalDataCompareList != null && capitalDataCompareList.size() != 0) {
            tvDataLabel1.setText(R.string.recharge);
            tvDataLabel2.setText(R.string.withdraw);
            generateData(capitalDataCompareList);
            CapitalDataCompareParse.getInstance().setCapitalDataCompareList(null);
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

    private void generateData(List<CapitalDataCompare> list) {
        int numColumns = 2;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getRechargeAmount() / 100, ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(0).getWithdrawAmount() / 100, ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(list.get(0).getStartToEnd()));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(1).getRechargeAmount() / 100, ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getWithdrawAmount() / 100, ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(list.get(1).getStartToEnd()));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            column.setFormatter(new SimpleColumnChartValueFormatter(2));
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(12));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.capital_state)));
        ccvCapitalDataCompare.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        CapitalDataCompareParse.getInstance().setCapitalDataCompareList(null);
    }
}
