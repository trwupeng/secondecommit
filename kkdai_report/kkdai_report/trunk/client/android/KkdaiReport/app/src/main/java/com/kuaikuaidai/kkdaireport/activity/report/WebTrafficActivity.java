package com.kuaikuaidai.kkdaireport.activity.report;


import android.app.DatePickerDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.WebTraffic;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.WebTrafficParse;
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
 * 网页流量
 */
public class WebTrafficActivity extends BaseActivity {

    @BindView(R.id.bt_back)
    TextView btBack;
    @BindView(R.id.ccv_web_traffic)
    ColumnChartView ccvWebTraffic;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.tv_data_label1)
    TextView tvDataLabel1;
    @BindView(R.id.tv_data_label2)
    TextView tvDataLabel2;

    private List<WebTraffic> list;
    private String startData, endData;

    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_web_traffic);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        ccvWebTraffic.setZoomEnabled(false);

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

        getData(startData, endData);


    }

    private void getData(String start, String end) {
        CommSender.webTraffic(start, end, this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        list = WebTrafficParse.getInstance().getWebTrafficList();
        if (list != null && list.size() >= 2) {
            tvDataLabel1.setText(list.get(0).getStartToEnd());
            tvDataLabel2.setText(list.get(1).getStartToEnd());
            generateData(list);
            WebTrafficParse.getInstance().setWebTrafficList(null);
        }
    }

    private void generateData(List<WebTraffic> list) {
        int numColumns = 3;
        List<Column> columns = new ArrayList<Column>();
        List<SubcolumnValue> values;
        List<AxisValue> axisXValues = new ArrayList<AxisValue>();
        for (int i = 0; i < numColumns; ++i) {
            values = new ArrayList<SubcolumnValue>();
            switch (i) {
                case 0://浏览量
                    values.add(new SubcolumnValue(list.get(0).getPv_count(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getPv_count(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(WebTrafficParse.getInstance().getPvCountName()));
                    break;
                case 1://访客数
                    values.add(new SubcolumnValue(list.get(0).getVisitor_count(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getVisitor_count(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(WebTrafficParse.getInstance().getVisitorName()));
                    break;
                case 2://IP数
                    values.add(new SubcolumnValue(list.get(0).getIp_count(), ColorUtil.color1));
                    values.add(new SubcolumnValue(list.get(1).getIp_count(), ColorUtil.color2));
                    axisXValues.add(new AxisValue(i).setLabel(WebTrafficParse.getInstance().getIpContName()));
                    break;
            }
            Column column = new Column(values);
            column.setHasLabels(true);
            column.setHasLabelsOnlyForSelected(false);
            columns.add(column);
        }
        ColumnChartData data = new ColumnChartData(columns);
        data.setAxisXBottom(new Axis(axisXValues).setTextSize(12).setTextColor(Color.BLACK).setMaxLabelChars(10));
        data.setAxisYLeft(new Axis().setHasLines(true).setTextColor(Color.BLACK).setMaxLabelChars(8).setName(getString(R.string.web_traffic)));
        ccvWebTraffic.setColumnChartData(data);
    }


    @OnClick({R.id.tv_start, R.id.tv_end, R.id.bt_query, R.id.bt_back})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay, DialogType.START);
                break;
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay, DialogType.END);
                break;
            case R.id.bt_query:
                getData(startData, endData);
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

    @Override
    protected void onDestroy() {
        super.onDestroy();
        WebTrafficParse.getInstance().clear();
    }
}
