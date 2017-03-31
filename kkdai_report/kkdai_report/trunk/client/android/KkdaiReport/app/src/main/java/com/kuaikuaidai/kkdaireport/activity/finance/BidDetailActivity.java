package com.kuaikuaidai.kkdaireport.activity.finance;

import android.os.Bundle;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.handmark.pulltorefresh.library.PullToRefreshBase;
import com.handmark.pulltorefresh.library.PullToRefreshListView;
import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.bean.BidDetail;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.BidParse;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 投标详情
 */

public class BidDetailActivity extends BaseActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;

    private QuickAdapter<BidDetail> adapter;
    private List<BidDetail> list;
    private int pageId = 1;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bid_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        key = getIntent().getStringExtra("ymd");
        int type = getIntent().getIntExtra("type", -1);
        switch (type) {
            case 1:
                tvTitle.setText(getString(R.string.bid_detail));
                break;
            case 2:
                tvTitle.setText(getString(R.string.fail_bid_detail));
                break;
        }
        list = BidParse.getInstance().getBidDetailList();

        adapter = new QuickAdapter<BidDetail>(mContext, R.layout.item_bid_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, BidDetail item) {
                helper.setText(R.id.tv_serial_number, item.getIndex_num());
                helper.setText(R.id.tv_order_number, item.getOrdersId());
                helper.setText(R.id.tv_user_id, item.getUserId());
                helper.setText(R.id.tv_name, item.getRealname());
                helper.setText(R.id.tv_phone, item.getPhone());
                helper.setText(R.id.tv_operate_time, item.getYmdhis());
                helper.setText(R.id.tv_invest_real_money, item.getAmount());
                helper.setText(R.id.tv_invest_vocher_money, item.getAmountExt());
                helper.setText(R.id.tv_popularizing_channel, item.getContractId());
                helper.setText(R.id.tv_invest_channel, item.getClientType());
                helper.setText(R.id.tv_invest_status, item.getOrderStatus());
                helper.setText(R.id.tv_subject_name, item.getWaresName());
                helper.setText(R.id.tv_subject_type, item.getShelfId());
                helper.setText(R.id.tv_subject_sum_amount, item.getWaresAmount());
                helper.setText(R.id.tv_subject_deadline, item.getDeadLine());
                helper.setText(R.id.tv_subject_rate, item.getYieldStatic());
                helper.setText(R.id.tv_subject_status, item.getStatusCode());
            }
        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        if (getIntent().getBooleanExtra("refresh", false)) {
            lvList.setMode(PullToRefreshBase.Mode.BOTH);
        } else {
            lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
        }
        lvList.setAdapter(adapter);
        lvList.setOnRefreshListener(this);
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

    private void getData() {
        CommSender.bidDetails(key, key, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        lvList.onRefreshComplete();
        list = BidParse.getInstance().getBidDetailList();
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
            BidParse.getInstance().setBidDetailList(null);
        } else {
            adapter.clear();
        }
        adapter.notifyDataSetChanged();
    }

    @OnClick(R.id.ll_back)
    public void onClick() {
        finish();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        BidParse.getInstance().setBidDetailList(null);
    }
}
