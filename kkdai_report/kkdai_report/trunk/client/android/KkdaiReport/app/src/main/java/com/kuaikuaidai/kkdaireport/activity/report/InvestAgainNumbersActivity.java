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
import com.kuaikuaidai.kkdaireport.bean.InvestAgainNumbers;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MomPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.InvestAgainNumbersParse;
import com.kuaikuaidai.kkdaireport.util.ColorUtil;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

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
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 复投人数
 */
public class InvestAgainNumbersActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_invest_again_numbers)
    ColumnChartView ccvInvestAgainNumbers;
    @BindView(R.id.tv_channel)
    TextView tvChannel;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.tv_mom)
    TextView tvMom;


    private String endData, channel;
    private DatePickerDialog dialog;
    private int endYear, endMonth, endDay;
    private ChannelPop channelPop;
    private MomPop momPop;
    private String mom = null;

    private List<String> momList = null;
    private List<ContractIds> contractIdsList;
    private List<InvestAgainNumbers> investAgainNumbersList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_invest_again_numbers);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvInvestAgainNumbers.setZoomEnabled(false);

        mom = "自然周";
        tvMom.setText(mom);
        momList = new ArrayList<>();
        momList.add("自然周");
        momList.add("自然月");

        momPop = new MomPop((Activity) mContext, momList);
        momPop.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                momPop.dismiss();
                mom = momList.get(position);
                tvMom.setText(mom);
            }
        });

        Calendar endCalendar = DateUtil.getLastSundayCalendar();
        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);
        endData = DateUtil.getLastSunday();
        tvEnd.setText(endData);

        tvChannel.setText(R.string.all);
        getData();
    }

    private void getData() {
        CommSender.investAgainNumbers(endData, mom, channel, this, mContext);
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
        investAgainNumbersList = InvestAgainNumbersParse.getInstance().getInvestAgainNumbersList();
        if (investAgainNumbersList != null && investAgainNumbersList.size() != 0) {
            tvDataLabel1.setText(investAgainNumbersList.get(0).getStartToEnd());
            tvDataLabel2.setText(investAgainNumbersList.get(1).getStartToEnd());
            generateData(investAgainNumbersList);
            InvestAgainNumbersParse.getInstance().setInvestAgainNumbersList(null);
        }
    }

    @OnClick({R.id.tv_end, R.id.bt_query, R.id.tv_channel, R.id.bt_back, R.id.tv_mom})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay);
                break;
            case R.id.bt_query:
                getData();
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
            case R.id.tv_mom:
                if (momPop != null) {
                    momPop.showAsDropDown(tvMom);
                    momPop.backgroundAlpha((Activity) mContext, 0.5f);
                }
                break;
        }
    }

    private void showDialog(int mYear, int mMonth, int mDay) {
        dialog = new MyDateDialog(mContext, mYear, mMonth, mDay) {
            @Override
            public void DateChanged(int year, int month, int day, String date) {
                endDay = day;
                endMonth = month;
                endYear = year;
                endData = date;
                tvEnd.setText(endData);
            }
        };
        dialog.show();
    }

    private void generateData(List<InvestAgainNumbers> list) {
        int numColumns = 5;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getN1(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN1(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_once_num)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(0).getN2(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN2(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_twice_num)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(list.get(0).getN3(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN3(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_three_num)));
                    break;
                case 3:
                    values.add(new SubcolumnValue(list.get(0).getN4(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN4(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_four_num)));
                    break;
                case 4:
                    values.add(new SubcolumnValue(list.get(0).getN5(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN5(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_five_num)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(4));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.invest_again_numbers)).setFormatter(new SimpleAxisValueFormatter(0)));
        ccvInvestAgainNumbers.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        InvestAgainNumbersParse.getInstance().setInvestAgainNumbersList(null);
    }
}
