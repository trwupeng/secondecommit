package com.kuaikuaidai.kkdaireport.activity.finance;

import android.app.DatePickerDialog;
import android.os.Bundle;
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
import com.kuaikuaidai.kkdaireport.bean.ProductType;
import com.kuaikuaidai.kkdaireport.bean.RefundBorrower;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
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
 * 还款统计-借款人
 */

public class RefundBorrowerStatisticsActivity extends BaseFinanceActivity implements PullToRefreshBase.OnRefreshListener2<ListView> {


    @BindView(R.id.ll_back)
    LinearLayout llBack;
    @BindView(R.id.tv_title)
    TextView tvTitle;

    @BindView(R.id.textView5)
    TextView textView5;
    @BindView(R.id.tv_type)
    TextView tvType;
    @BindView(R.id.et_borrower_name)
    EditText etBorrowerName;
    @BindView(R.id.tv_start)
    TextView tvStart;
    @BindView(R.id.tv_end)
    TextView tvEnd;
    @BindView(R.id.bt_query)
    TextView btQuery;
    @BindView(R.id.lv_list)
    PullToRefreshListView lvList;
    @BindView(R.id.et_subject_name)
    EditText etSubjectName;

    private QuickAdapter<RefundBorrower> adapter;
    private List<RefundBorrower> list;

    private DatePickerDialog dialog;
    private int startYear, startMonth, startDay, endYear, endMonth, endDay;
    private String startData, endData;
    private int pageId = 1;
    private ProductTypePop productTypePop;
    private String productType = null;
    private String borrowerName = null;
    private String subjectName = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_refund_borrower);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        tvTitle.setText(R.string.refund_borrower_statistics);

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

        tvType.setText("所有类型");
        productType = "ALLSHELFID";


        list = new ArrayList<RefundBorrower>();
        adapter = new QuickAdapter<RefundBorrower>(mContext, R.layout.item_refund_borrower, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, RefundBorrower item) {
                helper.setText(R.id.tv_actual_refund_date, item.getYmdPayment());
                helper.setText(R.id.tv_subject_id, item.getWaresId());
                helper.setText(R.id.tv_borrower_nickname, item.getNickname());
                helper.setText(R.id.tv_borrower_name, item.getRealname());
                helper.setText(R.id.tv_subject_name, item.getWaresName());
                helper.setText(R.id.tv_subject_type, item.getShelfId());
                helper.setText(R.id.tv_refund_stage_num, item.getBillNum());
                helper.setText(R.id.tv_contact_refund_date, item.getYmdShouldPay());
                helper.setText(R.id.tv_principal, item.getPrincipal());
                helper.setText(R.id.tv_interest, item.getInterest());
                helper.setText(R.id.tv_management_fee, item.getServiceCharge());
                helper.setText(R.id.tv_punish_interest, item.getPenaltyInteret());
                helper.setText(R.id.tv_overdue_management_fee, item.getOverheadCharges());
                helper.setText(R.id.tv_total, item.getSumAmount());
                helper.setText(R.id.tv_actual_refund_money, item.getPaymentMoney());
                helper.setText(R.id.tv_whether_pay_off, item.getFinish());
            }
        };
        lvList.setEmptyView(getLayoutInflater().inflate(R.layout.view_empty, null));
        lvList.setMode(PullToRefreshBase.Mode.PULL_FROM_START);
        lvList.setAdapter(adapter);
        lvList.setOnRefreshListener(this);

        getData();
    }

    private void getData() {
        CommSender.repaymentBorrwoerStatistics(productType, borrowerName, subjectName, startData, endData, String.valueOf(pageId), this, mContext);
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
        list = RefundParse.getInstance().getBorrowerList();
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
            RefundParse.getInstance().setBorrowerList(null);
        } else {
            adapter.clear();
        }
        adapter.notifyDataSetChanged();
    }

    @OnClick({R.id.tv_start, R.id.tv_end, R.id.bt_query, R.id.ll_back, R.id.tv_type})
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
                borrowerName = etBorrowerName.getText().toString().trim();
                subjectName = etSubjectName.getText().toString().trim();
                getData();
                break;
            case R.id.ll_back:
                finish();
                break;
            case R.id.tv_type:
                getPopShow();
                break;
        }
    }

    private void getPopShow() {
        if (productTypePop == null) {
            productTypePop = new ProductTypePop(mContext) {
                @Override
                public void OnItemClick(ProductType product) {
                    productType = product.getShefId();
                    tvType.setText(product.getShefName());
                }
            };
        }
        productTypePop.backgroundAlpha(mContext, 0.5f);
        productTypePop.showAsDropDown(tvType);
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
        RefundParse.getInstance().setBorrowerList(null);
    }

}
