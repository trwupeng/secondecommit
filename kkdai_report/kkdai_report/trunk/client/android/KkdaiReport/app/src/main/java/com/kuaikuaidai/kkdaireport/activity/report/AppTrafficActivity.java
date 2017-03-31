package com.kuaikuaidai.kkdaireport.activity.report;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.AppTraffic;
import com.kuaikuaidai.kkdaireport.bean.Channel;
import com.kuaikuaidai.kkdaireport.bean.ContractIds;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.ChannelPop;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.cusview.MyListView;
import com.kuaikuaidai.kkdaireport.parse.AppTrafficParse;
import com.kuaikuaidai.kkdaireport.parse.ContractIdsParse;
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
 * app流量
 */

public class AppTrafficActivity extends BaseActivity {


    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.ccv_app_traffic)
    ColumnChartView ccvAppTraffic;
    @BindView(R.id.lv_max_channel)
    MyListView lvMaxChannel;
    @BindView(R.id.tv_channel)
    TextView tvChannel;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;
    @BindView(R.id.bt_back)
    TextView btBack;


    private QuickAdapter<Channel> channelAdapter;
    private List<Channel> maxChannelList;
    private List<ContractIds> contractIdsList;
    private List<AppTraffic> appTrafficList;

    private ChannelPop channelPop;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData, channel;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_app_traffic);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) ccvAppTraffic.getLayoutParams();
        params.height = DensityUtil.getWindowExceptStatusHeight((Activity) mContext) - DensityUtil.dip2px(mContext, 70);
        ccvAppTraffic.setLayoutParams(params);
        ccvAppTraffic.setZoomEnabled(false);

        maxChannelList = new ArrayList<Channel>();
        channelAdapter = new QuickAdapter<Channel>(mContext, R.layout.item_max_channel, maxChannelList) {
            @Override
            protected void convert(BaseAdapterHelper helper, Channel item) {
                helper.setText(R.id.tv_name, item.getChannelName());
                helper.setText(R.id.tv_data, item.getChannelData());
            }
        };
        lvMaxChannel.setAdapter(channelAdapter);

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
        CommSender.appTraffic(start, end, contractId, this, mContext);
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

        maxChannelList = AppTrafficParse.getInstance().getMaxChannelList();
        if (maxChannelList != null && maxChannelList.size() != 0) {
            maxChannelList.add(0, AppTrafficParse.getInstance().getChannelHeader());
            channelAdapter.replaceAll(maxChannelList);
            AppTrafficParse.getInstance().setMaxChannelList(null);
            channelAdapter.notifyDataSetChanged();
        }

        appTrafficList = AppTrafficParse.getInstance().getAppTrafficList();
        if (appTrafficList != null && appTrafficList.size() >= 2) {
            tvDataLabel1.setText(appTrafficList.get(0).getStartToEnd());
            tvDataLabel2.setText(appTrafficList.get(1).getStartToEnd());
            generateData(appTrafficList);
            AppTrafficParse.getInstance().setAppTrafficList(null);
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


    private void generateData(List<AppTraffic> list) {
        int numColumns = 3;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0:
                    values.add(new SubcolumnValue(list.get(0).getNew_user(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getNew_user(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.activation_user)));
                    break;
                case 1:
                    values.add(new SubcolumnValue(list.get(0).getActive_user(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getActive_user(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.active_user)));
                    break;
                case 2:
                    values.add(new SubcolumnValue(list.get(0).getLaunches_user(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getLaunches_user(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(getString(R.string.start_num)));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(4));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(5).setName(getString(R.string.app_traffic)));
        ccvAppTraffic.setColumnChartData(data);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        ContractIdsParse.getInstance().setContractIds(null);
        AppTrafficParse.getInstance().setAppTrafficList(null);
        AppTrafficParse.getInstance().setMaxChannelList(null);
        AppTrafficParse.getInstance().setChannelHeader(null);
    }


}
