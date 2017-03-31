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
import com.kuaikuaidai.kkdaireport.bean.RemainData;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MomPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
import com.kuaikuaidai.kkdaireport.parse.RemainDataParse;
import com.kuaikuaidai.kkdaireport.util.ColorUtil;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

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
 * 留存数据
 */
public class RemainDataActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_remain_data)
    ColumnChartView ccvRemainData;
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
    private List<RemainData> remainDataList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_remain_data);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvRemainData.setZoomEnabled(false);

        tvChannel.setText(R.string.all);

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

        getData();
    }

    private void getData() {
        CommSender.remainData(endData, mom, channel, this, mContext);
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

        remainDataList = RemainDataParse.getInstance().getRemainDataList();
        if (remainDataList != null && remainDataList.size() != 0) {
            tvDataLabel1.setText(remainDataList.get(0).getStartToEnd());
            tvDataLabel2.setText(remainDataList.get(1).getStartToEnd());
            generateData(remainDataList);
            RemainDataParse.getInstance().setRemainDataList(null);
        }
    }

    @OnClick({R.id.tv_end, R.id.bt_query, R.id.tv_channel, R.id.bt_back,R.id.tv_mom})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay);
                break;
            case R.id.tv_mom:
                if (momPop != null) {
                    momPop.showAsDropDown(tvMom);
                    momPop.backgroundAlpha((Activity) mContext, 0.5f);
                }
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

    private void generateData(List<RemainData> list) {
        int numColumns = 3;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getNotLicaiHasBalance(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getNotLicaiHasBalance(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_no_money_yes)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(0).getLicaiNoBalance(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getLicaiNoBalance(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_yes_money_no)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(list.get(0).getLicaiHasBalance(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getLicaiHasBalance(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.invest_yes_money_yes)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(8));
        data.setAxisYLeft(new Axis().setName(getString(R.string.remain_state)).setTextColor(Color.BLACK).setHasLines(true).setMaxLabelChars(8));
        ccvRemainData.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        RemainDataParse.getInstance().setRemainDataList(null);
    }
}
