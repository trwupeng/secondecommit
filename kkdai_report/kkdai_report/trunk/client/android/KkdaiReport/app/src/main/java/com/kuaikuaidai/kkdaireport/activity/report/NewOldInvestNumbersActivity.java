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
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestNumbers;
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestNumbersItem;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestNumbersParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestNumbersParse;
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
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 新老用户理财人数
 */
public class NewOldInvestNumbersActivity extends BaseActivity {
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
    @BindView(R.id.ccv_new_old_invest_numbers1)
    ColumnChartView ccvNewOldInvestNumbers1;
    @BindView(R.id.ccv_new_old_invest_numbers2)
    ColumnChartView ccvNewOldInvestNumbers2;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.tv_data_label3)
    TextView tvDataLabel3;

    private List<ContractIds> contractIdsList;
    private List<NewOldInvestNumbers> newOldInvestNumbersList;

    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_old_invest_numbers);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvNewOldInvestNumbers1.setZoomEnabled(false);
        ccvNewOldInvestNumbers2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvNewOldInvestNumbers1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestNumbers1.setLayoutParams(params1);
        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvNewOldInvestNumbers2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestNumbers2.setLayoutParams(params2);

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
        CommSender.newOldInvestNumbers(start, end, contractId, this, mContext);
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

        tvDataLabel1.setText(R.string.no_invest_num_1);
        tvDataLabel2.setText(R.string.no_invest_num_2_5);
        tvDataLabel3.setText(R.string.no_invest_num_5);
        newOldInvestNumbersList = NewOldInvestNumbersParse.getInstance().getNewOldInvestNumbersList();
        if (newOldInvestNumbersList != null && newOldInvestNumbersList.size() != 0) {
            int size = newOldInvestNumbersList.size();
            if (size == 1) {
                tvLable2.setVisibility(View.GONE);
                ccvNewOldInvestNumbers2.setVisibility(View.GONE);
            } else {
                tvLable2.setVisibility(View.VISIBLE);
                ccvNewOldInvestNumbers2.setVisibility(View.VISIBLE);
            }
            for (int i = 0; i < ((newOldInvestNumbersList.size() >= 2) ? 2 : newOldInvestNumbersList.size()); i++) {
                generateData(newOldInvestNumbersList, i);
            }
            NewOldInvestNumbersParse.getInstance().setNewOldInvestNumbersList(null);
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

    private void generateData(List<NewOldInvestNumbers> list, int index) {
        NewOldInvestNumbers newOldInvestNumbers = list.get(index);
        int numColumns = 3;//总3列
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            NewOldInvestNumbersItem newInvestItem = null;
            String label = null;
            switch (i) {
                case 0:
                    newInvestItem = newOldInvestNumbers.getDqb();
                    label = getString(R.string.dingqibao);
                    break;
                case 1:
                    newInvestItem = newOldInvestNumbers.getFbb();
                    label = getString(R.string.fangbaobao);
                    break;
                case 2:
                    newInvestItem = newOldInvestNumbers.getJyb();
                    label = getString(R.string.jingyingbao);
                    break;
            }
            values.add(new SubcolumnValue(newInvestItem.getCount1Buy(), ColorUtil.color1));
            values.add(new SubcolumnValue(newInvestItem.getCount5Buy(), ColorUtil.color2));
            values.add(new SubcolumnValue(newInvestItem.getCount6PlusBuy(), ColorUtil.color3));
            axisXValues.add(new AxisValue(i).setLabel(label));
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setStacked(true);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(10).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(20));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.new_old_invest_numbers)).setFormatter(new SimpleAxisValueFormatter(0)));
        String se=null;
        switch (newOldInvestNumbers.getStartToEnd()){
            case "1":
                se= TitleParse.getInstance().getDate1();
                break;
            case "2":
                se=TitleParse.getInstance().getDate2();
                break;
        }
        switch (index) {
            case 0:
                tvLable1.setText(se);
                ccvNewOldInvestNumbers1.setColumnChartData(data);
                break;
            case 1:
                tvLable2.setText(se);
                ccvNewOldInvestNumbers2.setColumnChartData(data);
                break;
        }

    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        NewInvestNumbersParse.getInstance().setNewInvestNumberList(null);
    }
}
