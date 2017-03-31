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
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestMoney;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestMoneyParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestMoneyParse;
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
 * 新老用户理财金额
 */
public class NewOldInvestMoneyActivity extends BaseActivity {

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
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.tv_data_label3)
    TextView tvDataLabel3;
    @BindView(R.id.tv_lable1)
    TextView tvLable1;
    @BindView(R.id.tv_lable2)
    TextView tvLable2;
    @BindView(R.id.ccv_new_old_invest_money1)
    ColumnChartView ccvNewOldInvestMoney1;
    @BindView(R.id.ccv_new_old_invest_money2)
    ColumnChartView ccvNewOldInvestMoney2;

    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;

    private List<ContractIds> contractIdsList;
    private List<NewOldInvestMoney> newOldInvestMoneyList1, newOldInvestMoneyList2;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_old_invest_money);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvNewOldInvestMoney1.setZoomEnabled(false);
        ccvNewOldInvestMoney2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvNewOldInvestMoney1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestMoney1.setLayoutParams(params1);
        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvNewOldInvestMoney2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestMoney2.setLayoutParams(params2);

        tvChannel.setText(R.string.all);

        Calendar startCalendar = DateUtil.getLastMondayCalendar();
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
        CommSender.newOldInvestMoney(start, end, contractId, this, mContext);
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

        tvDataLabel1.setText(R.string.no_invest_money_1);
        tvDataLabel2.setText(R.string.no_invest_money_2_5);
        tvDataLabel3.setText(R.string.no_invest_money_5);
        newOldInvestMoneyList1 = NewOldInvestMoneyParse.getInstance().getNewOldInvestMoneyList1();
        newOldInvestMoneyList2 = NewOldInvestMoneyParse.getInstance().getNewOldInvestMoneyList2();
        int size = 0;
        if (newOldInvestMoneyList1 != null) {
            size += newOldInvestMoneyList1.size();
        }
        if (newOldInvestMoneyList2 != null) {
            size += newOldInvestMoneyList2.size();
        }
        if (size != 0) {
            if (newOldInvestMoneyList1 != null && newOldInvestMoneyList1.size() != 0) {
                generateData(newOldInvestMoneyList1, 1);
                NewOldInvestMoneyParse.getInstance().setNewOldInvestMoneyList1(null);
            }
            if (newOldInvestMoneyList2 != null && newOldInvestMoneyList2.size() != 0) {
                generateData(newOldInvestMoneyList2, 2);
                NewOldInvestMoneyParse.getInstance().setNewOldInvestMoneyList2(null);
            }
        } else {
            showToastShort(R.string.empty_data);
            ccvNewOldInvestMoney1.setColumnChartData(null);
            ccvNewOldInvestMoney2.setColumnChartData(null);
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

    private void generateData(List<NewOldInvestMoney> list, int index) {
        int numColumns = list.size();//总3列
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        NewOldInvestMoney newOldInvestMoney = null;
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            newOldInvestMoney = list.get(i);
            String label = getString(R.string.unknown);
            switch (newOldInvestMoney.getShelfId()) {
                case 0:
                    label = getString(R.string.tiantianzhuan);
                    break;
                case 1:
                    label = getString(R.string.dingqibao);
                    break;
                case 2:
                    label = getString(R.string.fangbaobao);
                    break;
                case 5:
                    label = getString(R.string.jingyingbao);
                    break;
                default:
                    label = String.valueOf(newOldInvestMoney.getShelfId());
                    break;
            }
            values.add(new SubcolumnValue(newOldInvestMoney.getAmount1Buy(), ColorUtil.color1));
            values.add(new SubcolumnValue(newOldInvestMoney.getAmount5Buy(), ColorUtil.color2));
            values.add(new SubcolumnValue(newOldInvestMoney.getAmount6PlusBuy(), ColorUtil.color3));
            axisXValues.add(new AxisValue(i).setLabel(label));
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setStacked(true);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(10).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(10));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.new_old_invest_money)));
        switch (index) {
            case 1:
                tvLable1.setText(NewInvestMoneyParse.getInstance().getRs1TitleText());
                ccvNewOldInvestMoney1.setColumnChartData(data);
                break;
            case 2:
                tvLable2.setText(NewInvestMoneyParse.getInstance().getRs2TitleText());
                ccvNewOldInvestMoney2.setColumnChartData(data);
                break;
        }

    }


    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
    }
}

