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
import com.kuaikuaidai.kkdaireport.bean.InvestAgainRatio;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MomPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.InvestAgainRatioParse;
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
 * 复投率
 */
public class InvestAgainRatioActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_invest_again_ratio)
    ColumnChartView ccvInvestAgainRatio;
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

    private List<String> momList=null;
    private List<ContractIds> contractIdsList;
    private List<InvestAgainRatio> investAgainRatioList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_invest_again_ratio);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvInvestAgainRatio.setZoomEnabled(false);

        tvChannel.setText(R.string.all);

        mom = "自然周";
        tvMom.setText(mom);
        momList=new ArrayList<>();
        momList.add("自然周");
        momList.add("自然月");

        momPop = new MomPop((Activity) mContext,momList);
        momPop.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                momPop.dismiss();
                mom=momList.get(position);
                tvMom.setText(mom);
            }
        });

        Calendar endCalendar = DateUtil.getLastSundayCalendar();

        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);
        endData = DateUtil.getLastSunday();

        tvEnd.setText(endData);

        getData();
    }

    private void getData() {
        CommSender.investAgainRatio(endData, mom, channel, this, mContext);
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

        investAgainRatioList = InvestAgainRatioParse.getInstance().getInvestAgainRatioList();
        if (investAgainRatioList != null && investAgainRatioList.size() >= 2) {
            tvDataLabel1.setText(investAgainRatioList.get(0).getStartToEnd());
            tvDataLabel2.setText(investAgainRatioList.get(1).getStartToEnd());
            generateData(investAgainRatioList);
            InvestAgainRatioParse.getInstance().setInvestAgainRatioList(null);
        }
    }

    @OnClick({R.id.tv_end, R.id.bt_query, R.id.tv_channel, R.id.bt_back,R.id.tv_mom})
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

    private void generateData(List<InvestAgainRatio> list) {
        int numColumns = 4;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getN1(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN1(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_twice_ratio)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(0).getN2(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN2(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_three_ratio)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(list.get(0).getN3(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN3(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_four_ratio)));
                    break;
                case 3:
                    values.add(new SubcolumnValue(list.get(0).getN4(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getN4(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_five_ratio)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setFormatter(new SimpleColumnChartValueFormatter(2));
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(3));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setName(getString(R.string.invest_again_ratio) + "(%)").setMaxLabelChars(3));
        ccvInvestAgainRatio.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        InvestAgainRatioParse.getInstance().setInvestAgainRatioList(null);
    }
}
