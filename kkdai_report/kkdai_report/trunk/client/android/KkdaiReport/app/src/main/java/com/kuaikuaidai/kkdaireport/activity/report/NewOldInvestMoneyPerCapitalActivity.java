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
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestPerCapital;
import com.kuaikuaidai.kkdaireport.bean.NewOldInvestPerCapitalItem;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.NewOldInvestPerCapitalParse;
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
import lecho.lib.hellocharts.model.Axis;
import lecho.lib.hellocharts.model.AxisValue;
import lecho.lib.hellocharts.model.Column;
import lecho.lib.hellocharts.model.ColumnChartData;
import lecho.lib.hellocharts.model.SubcolumnValue;
import lecho.lib.hellocharts.view.ColumnChartView;

/**
 * 新老用户理财金额人均
 */
public class NewOldInvestMoneyPerCapitalActivity extends BaseActivity {


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
    @BindView(R.id.ccv_new_old_invest_money_per_capital1)
    ColumnChartView ccvNewOldInvestMoneyPerCapital1;
    @BindView(R.id.ccv_new_old_invest_money_per_capital2)
    ColumnChartView ccvNewOldInvestMoneyPerCapital2;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.tv_data_label3)
    TextView tvDataLabel3;


    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;

    private List<ContractIds> contractIdsList;
    private List<NewOldInvestPerCapital> newOldInvestPerCapitalList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_old_invest_money_per_capital);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvNewOldInvestMoneyPerCapital1.setZoomEnabled(false);
        ccvNewOldInvestMoneyPerCapital2.setZoomEnabled(false);

        LinearLayout.LayoutParams params1 = (LinearLayout.LayoutParams) ccvNewOldInvestMoneyPerCapital1.getLayoutParams();
        params1.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestMoneyPerCapital1.setLayoutParams(params1);
        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) ccvNewOldInvestMoneyPerCapital2.getLayoutParams();
        params2.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvNewOldInvestMoneyPerCapital2.setLayoutParams(params2);

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
        CommSender.newOldInvestMoneyPerCapita(start, end, contractId, this, mContext);
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

        tvDataLabel1.setText(R.string.no_invest_per_1);
        tvDataLabel2.setText(R.string.no_invest_per_2_5);
        tvDataLabel3.setText(R.string.no_invest_per_5);

        newOldInvestPerCapitalList = NewOldInvestPerCapitalParse.getInstance().getNewOldInvestPerCapitalList();
        if (newOldInvestPerCapitalList != null && newOldInvestPerCapitalList.size() != 0) {
            int size = newOldInvestPerCapitalList.size();
            if (size == 1) {
                tvLable2.setVisibility(View.GONE);
                ccvNewOldInvestMoneyPerCapital2.setVisibility(View.GONE);
            } else {
                tvLable2.setVisibility(View.VISIBLE);
                ccvNewOldInvestMoneyPerCapital2.setVisibility(View.VISIBLE);
            }
            for (int i = 0; i < ((size >= 2) ? 2 : size); i++) {
                generateData(newOldInvestPerCapitalList, i);
            }
            NewOldInvestPerCapitalParse.getInstance().setNewOldInvestPerCapitalList(null);
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

    private void generateData(List<NewOldInvestPerCapital> list, int index) {
        NewOldInvestPerCapital newOldInvestPerCapital = list.get(index);
        int numColumns = 3;//总列数
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            NewOldInvestPerCapitalItem newOldInvestPerCapitalItem = null;
            String lable = null;
            switch (i) {
                case 0:
                    newOldInvestPerCapitalItem = newOldInvestPerCapital.getDqb();
                    lable = getString(R.string.dingqibao);
                    break;
                case 1:
                    newOldInvestPerCapitalItem = newOldInvestPerCapital.getFbb();
                    lable = getString(R.string.fangbaobao);
                    break;
                case 2:
                    newOldInvestPerCapitalItem = newOldInvestPerCapital.getJyb();
                    lable = getString(R.string.jingyingbao);
                    break;
            }
            values.add(new SubcolumnValue(newOldInvestPerCapitalItem.getAvgAmount1Buy(), ColorUtil.color1));
            values.add(new SubcolumnValue(newOldInvestPerCapitalItem.getAvgAmount5Buy(), ColorUtil.color2));
            values.add(new SubcolumnValue(newOldInvestPerCapitalItem.getAvgAmount6PlusBuy(), ColorUtil.color3));
            axisXValues.add(new AxisValue(i).setLabel(lable));
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(10).setHasTiltedLabels(false).setTextColor(Color.BLACK).setMaxLabelChars(20));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.new_old_invest_money_per_capita)));
        String se=null;
        switch (newOldInvestPerCapital.getStartToEnd()){
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
                ccvNewOldInvestMoneyPerCapital1.setColumnChartData(data);
                break;
            case 1:
                tvLable2.setText(se);
                ccvNewOldInvestMoneyPerCapital2.setColumnChartData(data);
                break;
        }

    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        NewOldInvestPerCapitalParse.getInstance().setNewOldInvestPerCapitalList(null);
    }
}
