package com.kuaikuaidai.kkdaireport.activity.finance;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
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
import com.kuaikuaidai.kkdaireport.bean.LoanDetail;
import com.kuaikuaidai.kkdaireport.bean.Pager;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.LoanDetailParse;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 标的放款明细
 */

public class LoanDetailsActivity extends BaseFinanceActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


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

    private QuickAdapter<LoanDetail> adapter;
    private List<LoanDetail> list;

    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;
    private int pageId = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_loan_detail);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        tvTitle.setText(R.string.loan_detail);

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


        list = new ArrayList<LoanDetail>();
        adapter = new QuickAdapter<LoanDetail>(mContext, R.layout.item_loan_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, LoanDetail item) {
                helper.setText(R.id.tv_borrower_nickname, item.getCustomer_name());
                helper.setText(R.id.tv_borrower_realname, item.getCustomer_realname());
                helper.setText(R.id.tv_subject_name, item.getBid_title());
                helper.setText(R.id.tv_loan_money, item.getAmount());
                helper.setText(R.id.tv_loantime, item.getCreate_time());
            }
        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
        lvList.setAdapter(adapter);
        lvList.setOnRefreshListener(this);

        getData();
    }

    private void getData() {
        CommSender.loanDetail(startData, endData, String.valueOf(pageId), this, mContext);
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
        lvList.onRefreshComplete();
        list = LoanDetailParse.getInstance().getList();
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
            LoanDetailParse.getInstance().setList(null);
        } else {
            adapter.clear();
        }
        adapter.notifyDataSetChanged();
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
        LoanDetailParse.getInstance().setList(null);
    }


}
