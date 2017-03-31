package com.kuaikuaidai.kkdaireport.activity.finance;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
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
import com.kuaikuaidai.kkdaireport.bean.UserFinancail;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.cusview.MyDateDialog;
import com.kuaikuaidai.kkdaireport.parse.PagerParse;
import com.kuaikuaidai.kkdaireport.parse.UserFinancialParse;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 用户理财明细
 */

public class UserFinancialActivity extends BaseFinanceActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.et_key)
    EditText etKey;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;


    private QuickAdapter<UserFinancail> adapter;
    private List<UserFinancail> list;


    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;

    private String key = null;
    private int pageId = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_financial);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        tvTitle.setText(R.string.user_financial_details);

        Calendar startCalendar = DateUtil.getBoforeTime(6);
        Calendar endCalendar = Calendar.getInstance();

        startYear = startCalendar.get(Calendar.YEAR);
        startMonth = startCalendar.get(Calendar.MONTH);
        startDay = startCalendar.get(Calendar.DAY_OF_MONTH);

        endYear = endCalendar.get(Calendar.YEAR);
        endMonth = endCalendar.get(Calendar.MONTH);
        endDay = endCalendar.get(Calendar.DAY_OF_MONTH);

        startData = DateUtil.getBoforeDay(6);
        endData = DateUtil.getCurrent();

        tvStart.setText(startData);
        tvEnd.setText(endData);

        list = new ArrayList<UserFinancail>();
        adapter = new QuickAdapter<UserFinancail>(mContext, R.layout.item_user_financial_detail, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, UserFinancail item) {
                helper.setText(R.id.tv_summary, item.getZhaiyao());
                helper.setText(R.id.tv_trade_money, item.getJxamount());
                helper.setText(R.id.tv_current_balance, item.getDqamount());
                helper.setText(R.id.tv_financial_date, item.getLicaidate());
            }
        };
        lvList.setOnRefreshListener(this);
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        lvList.setAdapter(adapter);
    }

    private void getData() {
        if (!TextUtils.isEmpty(key)) {
            CommSender.userFinancialDetails(startData, endData, key, String.valueOf(pageId), this, mContext);
        } else {
            lvList.onRefreshComplete();
            showToastShort(R.string.username_phone_empty);
        }
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
            case CommUrlConstant.USER_FINANCIAL_DETAILS:
                lvList.onRefreshComplete();
                list = UserFinancialParse.getInstance().getList();
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
                    UserFinancialParse.getInstance().setList(null);
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
                key = etKey.getText().toString().trim();
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
        UserFinancialParse.getInstance().setList(null);
    }


}
