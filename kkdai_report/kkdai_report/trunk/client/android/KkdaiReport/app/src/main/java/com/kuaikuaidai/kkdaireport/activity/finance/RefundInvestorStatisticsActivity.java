package com.kuaikuaidai.kkdaireport.activity.finance;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.handmark.pulltorefresh.library.PullToRefreshBase;
import com.handmark.pulltorefresh.library.PullToRefreshListView;
import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseFinanceActivity;
import com.kuaikuaidai.kkdaireport.bean.DialogType;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.bean.RefundBorrower;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.cusview.ProductTypePop;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.RefundParse;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 还款统计-投资人
 */

public class RefundInvestorStatisticsActivity extends BaseFinanceActivity implements PullToRefreshBase.OnRefreshListener2<ListView>, AdapterView.OnItemClickListener {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.et_subject_id)
    EditText etSubjectId;
    @BindView(R.id.et_subject_name)
    EditText etSubjectName;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;
    private QuickAdapter<RefundBorrower> adapter;
    private List<RefundBorrower> list;

    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;
    private int pageId = 1;
    private ProductTypePop productTypePop;
    private String subjectName = null;
    private String subjectId = null;
    private String key = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_refund_investor);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        tvTitle.setText(R.string.refund_investor_statistics);

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


        list = new ArrayList<RefundBorrower>();
        adapter = new QuickAdapter<RefundBorrower>(mContext, R.layout.item_refund_investor, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, RefundBorrower item) {
                helper.setText(R.id.tv_actual_refund_date, item.getYmdPayment());
                helper.setText(R.id.tv_subject_id, item.getWaresId());
                helper.setText(R.id.tv_subject_name, item.getWaresName());
                helper.setText(R.id.tv_subject_type, item.getShelfId());
                helper.setText(R.id.tv_refund_stage_num, item.getBillNum());
                helper.setText(R.id.tv_principal, item.getPrincipal());
                helper.setText(R.id.tv_interest, item.getInterest());
                helper.setText(R.id.tv_service_fee, item.getServiceCharge());
                helper.setText(R.id.tv_punish_interest, item.getPenaltyInteret());
                helper.setText(R.id.tv_overdue_management_fee, item.getOverheadCharges());
                helper.setText(R.id.tv_actual_refund_money_tatal, item.getPaymentMoney());
            }
        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
        lvList.setAdapter(adapter);
        lvList.setOnRefreshListener(this);
        lvList.setOnItemClickListener(this);

        getData();
    }

    private void getData() {
        CommSender.repaymentInvestorStatistics(subjectId, subjectName, startData, endData, String.valueOf(pageId), this, mContext);
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        key = adapter.getItem(position - 1).getPKEY__();
        CommSender.refundDetail(key, "1", this, mContext);
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
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        switch (api) {
            case CommUrlConstant.REFUND_DETAILS:
                if (RefundParse.getInstance().getRefundDetailList() != null && RefundParse.getInstance().getRefundDetailList().size() != 0) {
                    Intent intent = new Intent(mContext, RefundDetailActivity.class);
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
                    intent.putExtra("key", key);
                    startActivity(intent);
                } else {
                    showToastShort(R.string.empty_data);
                }
                break;
            case CommUrlConstant.REFUND_INVESTOR_STATIATICS:
                lvList.onRefreshComplete();
                list = RefundParse.getInstance().getInvestorList();
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
                    RefundParse.getInstance().setInvestorList(null);
                } else {
                    adapter.clear();
                }
                adapter.notifyDataSetChanged();
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
                subjectId = etSubjectId.getText().toString().trim();
                subjectName = etSubjectName.getText().toString().trim();
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
        RefundParse.getInstance().setInvestorList(null);
    }


}
