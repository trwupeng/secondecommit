package com.kuaikuaidai.kkdaireport.cusview;

import android.app.Activity;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.PopupWindow;

import com.joanzapata.android.BaseAdapterHelper;
import com.joanzapata.android.QuickAdapter;
import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.bean.ProductType;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by zhong.jiye on 2016/9/27.
 */

public abstract class ProductTypePop extends PopupWindow {

    private ListView listView;

    public ProductTypePop(final Context context) {
        super((LayoutInflater.from(context)).inflate(R.layout.view_product_type_pop, null),
                ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT, true);
        setFocusable(true);
        View root = getContentView();
        listView = (ListView) root.findViewById(R.id.lv_product_type);
        List<ProductType> list = new ArrayList<ProductType>();
        list.add(new ProductType("ALLSHELFID", "所有类型"));
        list.add(new ProductType("1", "定期宝"));
        list.add(new ProductType("2", "房宝宝"));
        list.add(new ProductType("3", "3"));
        list.add(new ProductType("4", "4"));
        list.add(new ProductType("5", "精英宝"));
        final QuickAdapter<ProductType> adapter = new QuickAdapter<ProductType>(context, R.layout.item_product_type, list) {
            @Override
            protected void convert(BaseAdapterHelper helper, ProductType item) {
                helper.setText(R.id.tv_product_type, item.getShefName());
            }
        };
        listView.setAdapter(adapter);
        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                OnItemClick(adapter.getItem(position));
                dismiss();
            }
        });
        ColorDrawable dw = new ColorDrawable(0x00000000);
        setBackgroundDrawable(dw);
        setOnDismissListener(new OnDismissListener() {
            @Override
            public void onDismiss() {
                backgroundAlpha(context, 1f);
            }
        });
    }

    public abstract void OnItemClick(ProductType productType);


    /**
     * 设置添加屏幕的背景透明度
     *
     * @param bgAlpha
     */
    public void backgroundAlpha(Context context, float bgAlpha) {
        Window window = ((Activity) context).getWindow();
        WindowManager.LayoutParams lp = window.getAttributes();
        lp.alpha = bgAlpha;
        window.addFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);
        window.setAttributes(lp);
    }
}
