package com.kuaikuaidai.kkdaireport.cusview;

import android.app.Activity;
import android.view.View;
import android.view.ViewGroup;
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

public class QuarterPop extends PopupWindow {

    private ListView listView;

    public QuarterPop(final Activity context, List<String> list) {
        super(context.getLayoutInflater().inflate(R.layout.view_pop_quarter, null),
                ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT, true);
        setFocusable(true);
        View root = getContentView();
        listView = (ListView) root.findViewById(R.id.lv_pop_quarter);
        QuickAdapter<String> adapter = new QuickAdapter<String>(context, R.layout.item_quarter, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, String item) {
                helper.setText(R.id.tv_quarter, item);
            }
        };
        listView.setAdapter(adapter);

    }

    public void setOnItemClickListener(AdapterView.OnItemClickListener onItemClickListener) {
        if (onItemClickListener != null) {
            listView.setOnItemClickListener(onItemClickListener);
        }
    }
}
