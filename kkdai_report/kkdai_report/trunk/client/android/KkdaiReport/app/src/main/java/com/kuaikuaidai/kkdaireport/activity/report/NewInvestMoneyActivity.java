package com.kuaikuaidai.kkdaireport.activity.report;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.NewInvestMoney;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestMoneyParse;
import com.kuaikuaidai.kkdaireport.util.ColorUtil;
import com.kuaikuaidai.kkdaireport.util.DateUtil;
import com.kuaikuaidai.kkdaireport.util.DensityUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import lecho.lib.hellocharts.model.Axis;
import lecho.lib.hellocharts.model.AxisValue;
import lecho.lib.hellocharts.model.Column;
import lecho.lib.hellocharts.model.ColumnChartData;
import lecho.lib.hellocharts.model.SubcolumnValue;
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 新增理财金额
 */
public class NewInvestMoneyActivity extends BaseActivity {


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
    @BindView(R.id.ccv_new_invest_money1)
    ColumnChartView ccvNewInvestMoney1;
    @BindView(R.id.ccv_new_invest_money2)
    ColumnChartView ccvNewInvestMoney2;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.tv_data_label3)
    TextView tvDataLabel3;
    @BindView(R.id.tv_data_label4)
    TextView tvDataLabel4;


    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;

    private List<ContractIds> contractIdsList;
    private List<NewInvestMoney> newInvestMoneyList1;
    private List<NewInvestMoney> newInvestMoneyList2;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_invest_money);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvNewInvestMoney1.setZoomEnabled(false);
        ccvNewInvestMoney2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvNewInvestMoney1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewInvestMoney1.setLayoutParams(params1);

        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvNewInvestMoney2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewInvestMoney2.setLayoutParams(params2);

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
        CommSender.newInvestMoney(start, end, contractId, this, mContext);
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

        tvDataLabel1.setText(R.string.invest_money_current);
        tvDataLabel2.setText(R.string.invest_money_1_5);
        tvDataLabel3.setText(R.string.invest_money_6_30);
        tvDataLabel4.setText(R.string.invest_money_31);

        newInvestMoneyList1 = NewInvestMoneyParse.getInstance().getNewInvestMoneyList1();
        newInvestMoneyList2 = NewInvestMoneyParse.getInstance().getNewInvestMoneyList2();

        if (newInvestMoneyList1 != null && newInvestMoneyList1.size() != 0) {
            tvLable1.setVisibility(View.VISIBLE);
            ccvNewInvestMoney1.setVisibility(View.VISIBLE);
            generateData(newInvestMoneyList1, 0);
            NewInvestMoneyParse.getInstance().setNewInvestMoneyList1(null);
        } else {
            tvLable1.setVisibility(View.GONE);
            ccvNewInvestMoney1.setVisibility(View.GONE);
        }
        if (newInvestMoneyList2 != null && newInvestMoneyList2.size() != 0) {
            tvLable2.setVisibility(View.VISIBLE);
            ccvNewInvestMoney2.setVisibility(View.VISIBLE);
            generateData(newInvestMoneyList2, 1);
            NewInvestMoneyParse.getInstance().setNewInvestMoneyList2(null);
        } else {
            tvLable2.setVisibility(View.GONE);
            ccvNewInvestMoney2.setVisibility(View.GONE);
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

    private void generateData(List<NewInvestMoney> list, int index) {
        int numColumns = list.size();//总列数
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            values.add(new SubcolumnValue(list.get(i).getAmountReg0Day(), ColorUtil.color1));
            values.add(new SubcolumnValue(list.get(i).getAmountReg1To5(), ColorUtil.color2));
            values.add(new SubcolumnValue(list.get(i).getAmountReg6To30(), ColorUtil.color3));
            values.add(new SubcolumnValue(list.get(i).getAmountReg31Plus(), ColorUtil.color4));
            String lable = null;
            switch (list.get(i).getShelfId()) {
                case 0:
                    lable = getString(R.string.tiantianzhuan);
                    break;
                case 1:
                    lable = getString(R.string.dingqibao);
                    break;
                case 2:
                    lable = getString(R.string.fangbaobao);
                    break;
                case 5:
                    lable = getString(R.string.jingyingbao);
                    break;
            }
            axisXValues.add(new AxisValue(i).setLabel(lable));
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setStacked(true);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(10).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(20));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.new_invest_money) + "(首次投资)"));
        switch (index) {
            case 0:
                if (!TextUtils.isEmpty(NewInvestMoneyParse.getInstance().getRs1TitleText())) {
                    tvLable1.setText(NewInvestMoneyParse.getInstance().getRs1TitleText());
                }
                ccvNewInvestMoney1.setColumnChartData(data);
                break;
            case 1:
                if (!TextUtils.isEmpty(NewInvestMoneyParse.getInstance().getRs2TitleText())) {
                    tvLable2.setText(NewInvestMoneyParse.getInstance().getRs2TitleText());
                }
                ccvNewInvestMoney2.setColumnChartData(data);
                break;
        }

    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        NewInvestMoneyParse.getInstance().setNewInvestMoneyList1(null);
        NewInvestMoneyParse.getInstance().setNewInvestMoneyList2(null);
    }
}
