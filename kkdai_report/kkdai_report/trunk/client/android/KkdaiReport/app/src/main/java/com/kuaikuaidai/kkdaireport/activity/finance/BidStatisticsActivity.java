package com.kuaikuaidai.kkdaireport.activity.finance;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.handmark.pulltorefresh.library.PullToRefreshBase;
import com.handmark.pulltorefresh.library.PullToRefreshListView;
import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseFinanceActivity;
import com.kuaikuaidai.kkdaireport.bean.Bid;
import com.kuaikuaidai.kkdaireport.bean.BidSum;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.BidParse;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 投标统计
 */

public class BidStatisticsActivity extends BaseFinanceActivity implements AdapterView.OnItemClickListener, PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;
    @BindView(R.id.tv_sum)
    TextView tvSum;

    private QuickAdapter<Bid> adapter;
    private List<Bid> list;
    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;
    private int pageId = 1;
    private String key = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bid_statistics);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        tvTitle.setText(R.string.bid_statistics);

        Calendar startCalendar = DateUtil.getBoforeTime(30);
        Calendar endCalendar = DateUtil.getBoforeTime(1);

        startYear = startCalendar.get(Calendar.YEAR);
        startMonth = startCalendar.get(Calendar.MONTH);
        startDay = startCalendar.get(Calendar.DAY_OF_MONTH);

        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);

        startData = DateUtil.getBoforeDay(30);
        endData = DateUtil.getBoforeDay(1);

        tvStart.setText(startData);
        tvEnd.setText(endData);


        list = new ArrayList<Bid>();
        adapter = new QuickAdapter<Bid>(mContext, R.layout.item_bid, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, Bid item) {
                helper.setText(R.id.tv_date, item.getYmd());
                helper.setText(R.id.tv_normal_invest_success_money, item.getAmount_succ_normal());
                helper.setText(R.id.tv_normal_invest_success_count, item.getCount_succ_normal());
                helper.setText(R.id.tv_normal_invest_fail_money, item.getAmount_fail_normal());
                helper.setText(R.id.tv_normal_invest_fail_count, item.getCount_fail_normal());
                helper.setText(R.id.tv_vip_invest_success_money, item.getAmount_succ_super());
                helper.setText(R.id.tv_vip_invest_success_count, item.getCount_succ_super());
                helper.setText(R.id.tv_vip_invest_fail_money, item.getAmount_fail_super());
                helper.setText(R.id.tv_vip_invest_fail_count, item.getCount_fail_super());
                helper.setText(R.id.tv_success_money, item.getSumSuccAmount());
                helper.setText(R.id.tv_success_count, item.getSumSuccCount());
                helper.setText(R.id.tv_fail_money, item.getSumFailedAmount());
                helper.setText(R.id.tv_fail_count, item.getSumFailedCount());
            }

        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
        lvList.setAdapter(adapter);
        lvList.setOnItemClickListener(this);
        lvList.setOnRefreshListener(this);

        getData();
    }

    private void getData() {
        CommSender.bidStatistics(startData, endData, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onPullDownToRefresh(PullToRefreshBase<ListView> refreshView) {
        pageId = 1;
        getData();
    }

    @Override
    public void onPullUpToRefresh(PullToRefreshBase<ListView> refreshView) {
        pageId += 1;
        getData();
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        key = adapter.getItem(position - 1).getYmd();
        CommSender.bidDetails(key, key, "1", this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        switch (api) {
            case CommUrlConstant.BID_STATIATICS:
                BidSum bidSum = BidParse.getInstance().getBidSum();
                if(bidSum!=null){
                    if (!TextUtils.isEmpty(bidSum.toString())) {
                        tvSum.setText(bidSum.toString());
                        tvSum.setVisibility(View.VISIBLE);
                    } else {
                        tvSum.setText("");
                        tvSum.setVisibility(View.GONE);
                    }
                }
                lvList.onRefreshComplete();
                list = BidParse.getInstance().getList();
                Pager pager = PagerParse.getInstance().getPager();
                if (pager != null) {
                    pageId = pager.getPageId();
                }
                if (list != null && list.size() != 0) {
                    if (pageId < pager.getPageCount()) {
                        lvList.setMode(PullToRefreshBase.Mode.BOTH);
                    } else {
                        lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
                    }
                    if (pageId == 1) {
                        adapter.replaceAll(list);
                    } else {
                        adapter.addAll(list);
                    }
                    BidParse.getInstance().setList(null);
                } else {
                    adapter.clear();
                }
                adapter.notifyDataSetChanged();
                break;
            case CommUrlConstant.BID_DETAILS:
                if (BidParse.getInstance().getBidDetailList() != null && BidParse.getInstance().getBidDetailList().size() != 0) {
                    Intent intent = new Intent(mContext, BidDetailActivity.class);
                    intent.putExtra("type", 1);
                    intent.putExtra("ymd", key);
                    Pager pager2 = PagerParse.getInstance().getPager();
                    if (pager2 != null) {
                        if (pager2.getPageId() < pager2.getPageCount()) {
                            intent.putExtra("refresh",true);
                        } else {
                            intent.putExtra("refresh",false);
                        }
                    }else{
                        intent.putExtra("refresh",false);
                    }
                    startActivity(intent);
                } else {
                    showToastShort(R.string.empty_data);
                }
                break;
        }

    }

    @OnClick({R.id.tv_start, R.id.tv_end, R.id.bt_query, R.id.ll_back})
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.tv_start:
                showDialog(startYear, startMonth, startDay, DialogType.START);
                break;
            case R.id.tv_end:
                showDialog(endYear, endMonth, endDay, DialogType.END);
                break;
            case R.id.bt_query:
                pageId = 1;
                getData();
                break;
            case R.id.ll_back:
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
        BidParse.getInstance().clear();
    }


}
