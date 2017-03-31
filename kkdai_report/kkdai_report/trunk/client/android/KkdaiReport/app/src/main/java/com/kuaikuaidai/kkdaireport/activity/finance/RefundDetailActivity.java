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
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.bean.RefundDetail;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.RefundParse;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * Created by zhong.jiye on 2016/10/17.
 * 还款详情
 */

public class RefundDetailActivity extends BaseActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;

    private QuickAdapter<RefundDetail> adapter;
    private List<RefundDetail> list;
    private int pageId = 1;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_refund_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        key = getIntent().getStringExtra("key");
        tvTitle.setText(getString(R.string.refund_detail));

        list = RefundParse.getInstance().getRefundDetailList();

        adapter = new QuickAdapter<RefundDetail>(mContext, R.layout.item_refund_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, RefundDetail item) {
                helper.setText(R.id.tv_order_number, item.getOrdersId());
                helper.setText(R.id.tv_user_id, item.getUserId());
                helper.setText(R.id.tv_name, item.getRealname());
                helper.setText(R.id.tv_phone, item.getPhone());
                helper.setText(R.id.tv_subject_name, item.getWaresName());
                helper.setText(R.id.tv_subject_deadline, item.getDeadLine());
                helper.setText(R.id.tv_refund_stage_num, item.getBillNum());
                helper.setText(R.id.tv_contact_refund_date, item.getYmdShouldPay());
                helper.setText(R.id.tv_actual_refund_date, item.getYmdPayment());
                helper.setText(R.id.tv_actual_invest_money, item.getOrderAmount());
                helper.setText(R.id.tv_invest_vocher_money, item.getOrderAmountExt());
                helper.setText(R.id.tv_invest_money, item.getOrderAmountSum());
                helper.setText(R.id.tv_refund_money, item.getAmount());
                helper.setText(R.id.tv_interest, item.getInterest());
                helper.setText(R.id.tv_award_interest, item.getAddInterest());
                helper.setText(R.id.tv_punish_interest, item.getPenaltyInteret());
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
        CommSender.refundDetail(key, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        lvList.onRefreshComplete();
        list = RefundParse.getInstance().getRefundDetailList();
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
            RefundParse.getInstance().setRefundDetailList(null);
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
        RefundParse.getInstance().setRefundDetailList(null);
    }
}
