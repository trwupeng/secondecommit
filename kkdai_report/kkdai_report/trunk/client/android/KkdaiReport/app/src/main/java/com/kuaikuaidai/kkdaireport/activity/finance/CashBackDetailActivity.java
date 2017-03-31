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
import com.kuaikuaidai.kkdaireport.bean.CashBackDetail;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.CashBackParse;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 好友返现详情
 */

public class CashBackDetailActivity extends BaseActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;

    private QuickAdapter<CashBackDetail> adapter;
    private List<CashBackDetail> list;
    private int pageId = 1;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cash_back_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        key = getIntent().getStringExtra("ymd");

        tvTitle.setText(key + " " + getString(R.string.cash_back_detail));

        list = CashBackParse.getInstance().getCashBackDetailList();

        adapter = new QuickAdapter<CashBackDetail>(mContext, R.layout.item_cash_back_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, CashBackDetail item) {
                helper.setText(R.id.tv_customer_id, item.getCustomer_id());
                helper.setText(R.id.tv_name, item.getCustomer_realname());
                helper.setText(R.id.tv_phone, item.getCustomer_cellphone());
                helper.setText(R.id.tv_cash_back_time, item.getCreate_time());
                helper.setText(R.id.tv_cash_back_money, item.getAmount());
            }
        };
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
        CommSender.cashBackDetail(key, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        lvList.onRefreshComplete();
        list = CashBackParse.getInstance().getCashBackDetailList();
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
            CashBackParse.getInstance().setCashBackDetailList(null);
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
        CashBackParse.getInstance().setCashBackDetailList(null);
    }


}
