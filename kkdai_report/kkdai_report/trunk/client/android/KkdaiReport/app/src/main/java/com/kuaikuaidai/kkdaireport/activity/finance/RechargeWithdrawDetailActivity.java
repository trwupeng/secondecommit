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
import com.kuaikuaidai.kkdaireport.bean.RwDetail;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.RechargeWithdrawParse;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 充值、提现详情
 */

public class RechargeWithdrawDetailActivity extends BaseActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;

    private QuickAdapter<RwDetail> adapter;
    private List<RwDetail> list;
    private int pageId = 1;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recharge_withdraw_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        key = getIntent().getStringExtra("ymd");
        tvTitle.setText(key + " " + getString(R.string.recharge_withdraw_detail));

        list = RechargeWithdrawParse.getInstance().getRwDetailList();

        adapter = new QuickAdapter<RwDetail>(mContext, R.layout.item_recharge_withdraw_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, RwDetail item) {
                helper.setText(R.id.tv_date, item.getYmd());
                helper.setText(R.id.tv_user_id, item.getUserId());
                helper.setText(R.id.tv_name, item.getRealname());
                helper.setText(R.id.tv_phone, item.getPhone());
                helper.setText(R.id.tv_operate_time, item.getOptime());
                helper.setText(R.id.tv_operate_type, item.getOpways());
                helper.setText(R.id.tv_money, item.getAmount());
            }
        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        if(getIntent().getBooleanExtra("refresh",false)){
            lvList.setMode(PullToRefreshBase.Mode.BOTH);
        }else{
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
        CommSender.rechargeWithdrawDetails(key, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        lvList.onRefreshComplete();
        list = RechargeWithdrawParse.getInstance().getRwDetailList();
        Pager pager = PagerParse.getInstance().getPager();
        if (pager != null) {
            pageId = pager.getPageId();
        }
        if (list != null && list.size() != 0) {
            lvList.onRefreshComplete();
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
            RechargeWithdrawParse.getInstance().setRwDetailList(null);
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
        RechargeWithdrawParse.getInstance().setRwDetailList(null);
    }
}
