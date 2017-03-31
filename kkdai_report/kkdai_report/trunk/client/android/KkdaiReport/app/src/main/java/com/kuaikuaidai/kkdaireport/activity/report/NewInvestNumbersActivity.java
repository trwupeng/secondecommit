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
import com.kuaikuaidai.kkdaireport.bean.NewInvestItem;
import com.kuaikuaidai.kkdaireport.bean.NewInvestNumber;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.NewInvestNumbersParse;
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
 * 新增理财人数
 */
public class NewInvestNumbersActivity extends BaseActivity {


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
    @BindView(R.id.ccv_new_invest_numbers1)
    ColumnChartView ccvNewInvestNumbers1;
    @BindView(R.id.ccv_new_invest_numbers2)
    ColumnChartView ccvNewInvestNumbers2;
    @BindView(R.id.tv_lable1)
    TextView tvLable1;
    @BindView(R.id.tv_lable2)
    TextView tvLable2;
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
    private List<NewInvestNumber> newInvestNumberList;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_invest_numbers);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvNewInvestNumbers1.setZoomEnabled(false);
        ccvNewInvestNumbers2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvNewInvestNumbers1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewInvestNumbers1.setLayoutParams(params1);
        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvNewInvestNumbers2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewInvestNumbers2.setLayoutParams(params2);

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
        CommSender.newInvestNumbers(start, end, contractId, this, mContext);
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

        tvDataLabel1.setText(R.string.invest_num_current);
        tvDataLabel2.setText(R.string.invest_num_1_5);
        tvDataLabel3.setText(R.string.invest_num_6_30);
        tvDataLabel4.setText(R.string.invest_num_31);
        newInvestNumberList = NewInvestNumbersParse.getInstance().getNewInvestNumberList();
        if (newInvestNumberList != null && newInvestNumberList.size() != 0) {
            int size = newInvestNumberList.size();
            if (size == 1) {
                tvLable2.setVisibility(View.GONE);
                ccvNewInvestNumbers2.setVisibility(View.GONE);
            } else {
                tvLable2.setVisibility(View.VISIBLE);
                ccvNewInvestNumbers2.setVisibility(View.VISIBLE);
            }
            for (int i = 0; i < ((size >= 2) ? 2 : size); i++) {
                generateData(newInvestNumberList, i);
            }
            NewInvestNumbersParse.getInstance().setNewInvestNumberList(null);
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

    private void generateData(List<NewInvestNumber> list, int index) {
        NewInvestNumber newInvestNumber = list.get(index);
        int numColumns = 3;//总3列
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            NewInvestItem newInvestItem = null;
            String label = null;
            switch (i) {
                case 0:
                    newInvestItem = newInvestNumber.getDqb();
                    label = getString(R.string.dingqibao);
                    break;
                case 1:
                    newInvestItem = newInvestNumber.getFbb();
                    label = getString(R.string.fangbaobao);
                    break;
                case 2:
                    newInvestItem = newInvestNumber.getJyb();
                    label = getString(R.string.jingyingbao);
                    break;
            }
            values.add(new SubcolumnValue(newInvestItem == null ? 0 : newInvestItem.getCountReg0Day(), ColorUtil.color1));
            values.add(new SubcolumnValue(newInvestItem == null ? 0 : newInvestItem.getCountReg1To5(), ColorUtil.color2));
            values.add(new SubcolumnValue(newInvestItem == null ? 0 : newInvestItem.getCountReg6To30(), ColorUtil.color3));
            values.add(new SubcolumnValue(newInvestItem == null ? 0 : newInvestItem.getCountReg31Plus(), ColorUtil.color4));
            axisXValues.add(new AxisValue(i).setLabel(label));
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setStacked(true);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(10).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(20));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(5).setName(getString(R.string.new_invest_numbers) + "(首次投资)").setFormatter(new SimpleAxisValueFormatter(0)));
        String se=null;
        switch (newInvestNumber.getStartToEnd()){
            case "1":
                se=TitleParse.getInstance().getDate1();
                break;
            case "2":
                se=TitleParse.getInstance().getDate2();
                break;
        }
        switch (index) {
            case 0:
                tvLable1.setText(se);
                ccvNewInvestNumbers1.setColumnChartData(data);
                break;
            case 1:
                tvLable2.setText(se);
                ccvNewInvestNumbers2.setColumnChartData(data);
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
