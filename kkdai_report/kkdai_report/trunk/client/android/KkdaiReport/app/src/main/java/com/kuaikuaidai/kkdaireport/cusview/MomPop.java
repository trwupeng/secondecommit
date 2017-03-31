package com.kuaikuaidai.kkdaireport.cusview;

import android.app.Activity;
import android.graphics.drawable.ColorDrawable;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.PopupWindow;

import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;

import java.util.List;

/**
 * Created by zhong.jiye on 2016/9/27.
 */

public class MomPop extends PopupWindow {

    private AdapterView.OnItemClickListener onItemClickListener;
    private ListView listView;

    public MomPop(final Activity context,List<String> momList) {
        super(context.getLayoutInflater().inflate(R.layout.view_mom_pop, null),
                ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT, true);
        setFocusable(true);
        View root = getContentView();
        listView = (ListView) root.findViewById(R.id.lv_pop_mom);
        QuickAdapter<String> adapter = new QuickAdapter<String>(context, R.layout.item_mom, momList) {
            @Override
            protected void convert(BaseAdapterHelper helper, String item) {
                helper.setText(R.id.tv_mom, item);
            }
        };
        listView.setAdapter(adapter);
        ColorDrawable dw = new ColorDrawable(0x00000000);
        this.setBackgroundDrawable(dw);
        setOnDismissListener(new OnDismissListener() {
            @Override
            public void onDismiss() {
                backgroundAlpha(context, 1f);
            }
        });
    }

    public void setOnItemClickListener(AdapterView.OnItemClickListener onItemClickListener) {
        this.onItemClickListener = onItemClickListener;
        if (listView != null && this.onItemClickListener != null) {
            listView.setOnItemClickListener(this.onItemClickListener);
        }
    }

    /**
     * 设置添加屏幕的背景透明度
     * @param bgAlpha
     */
    public void backgroundAlpha(Activity context, float bgAlpha)
    {
        WindowManager.LayoutParams lp = context.getWindow().getAttributes();
        lp.alpha = bgAlpha;
        context.getWindow().addFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);
        context.getWindow().setAttributes(lp);
    }
}
