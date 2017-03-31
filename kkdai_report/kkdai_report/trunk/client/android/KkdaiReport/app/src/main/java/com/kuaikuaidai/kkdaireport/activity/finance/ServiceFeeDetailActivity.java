package com.kuaikuaidai.kkdaireport.activity.finance;

import android.os.Bundle;
import android.text.TextUtils;
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
import com.kuaikuaidai.kkdaireport.bean.SeviceFeeDetail;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.ServiceFeeParse;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 服务费详情
 */

public class ServiceFeeDetailActivity extends BaseActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;

    private QuickAdapter<SeviceFeeDetail> adapter;
    private List<SeviceFeeDetail> list;
    private int pageId = 1;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_service_fee_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        key = getIntent().getStringExtra("ymd");
        tvTitle.setText(key + " " + getString(R.string.service_fee_detail));

        list = ServiceFeeParse.getInstance().getSeviceFeeDetailList();

        adapter = new QuickAdapter<SeviceFeeDetail>(mContext, R.layout.item_service_fee_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, SeviceFeeDetail item) {
                helper.setText(R.id.tv_borrower_name, TextUtils.isEmpty(item.getCustomer_realname()) ? "" : item.getCustomer_realname());
                helper.setText(R.id.tv_charge_time, TextUtils.isEmpty(item.getCreate_time()) ? "" : item.getCreate_time());
                helper.setText(R.id.tv_subject_name, TextUtils.isEmpty(item.getBid_title()) ? "" : item.getBid_title());
                helper.setText(R.id.tv_subject_deadline, TextUtils.isEmpty(item.getBid_period()) ? "" : item.getBid_period());
                helper.setText(R.id.tv_subject_money, TextUtils.isEmpty(item.getBid_amount()) ? "" : item.getBid_amount());
                helper.setText(R.id.tv_subject_rate, TextUtils.isEmpty(item.getBid_interest()) ? "" : item.getBid_interest());
                helper.setText(R.id.tv_credit_rating, TextUtils.isEmpty(item.getBid_credit_level()) ? "" : item.getBid_credit_level());
                helper.setText(R.id.tv_service_fee, TextUtils.isEmpty(item.getBid_serviceFee()) ? "" : item.getBid_serviceFee());
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
        CommSender.serviceFeeDetailStatistics(key, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        lvList.onRefreshComplete();
        list = ServiceFeeParse.getInstance().getSeviceFeeDetailList();
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
            ServiceFeeParse.getInstance().setSeviceFeeDetailList(null);
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
        ServiceFeeParse.getInstance().setSeviceFeeDetailList(null);
    }
}
