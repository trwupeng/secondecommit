package com.kuaikuaidai.kkdaireport.activity;


import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Parcelable;
import android.view.KeyEvent;
import android.view.View;
import android.widget.ExpandableListView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.activity.kpi.KpiHomeActivity;
import com.kuaikuaidai.kkdaireport.adapter.MenuAdapter;
import com.kuaikuaidai.kkdaireport.base.BasePureActivity;
import com.kuaikuaidai.kkdaireport.bean.Menu;
import com.kuaikuaidai.kkdaireport.comm.CommUrlConstant;
import com.kuaikuaidai.kkdaireport.parse.MenuParse;
import com.kuaikuaidai.kkdaireport.util.AppManager;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;

/**
 * 菜单
 */
public class MenuActivity extends BasePureActivity implements ExpandableListView.OnChildClickListener {


    @BindView(R.id.elv_menu)
    ExpandableListView elvMenu;

    private Menu menu;

    private MenuAdapter menuAdapter;

    private int backCount = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_menu);
        ButterKnife.bind(this);
        init();
    }

    private void init() {
        menu = MenuParse.getInstance().getMenu();
        List<Menu> children = new ArrayList<Menu>();
        for (Menu temp : menu.getChildren()) {
            if (getString(R.string.visual_reports).equals(temp.getCapt())
                    || getString(R.string.financial_statistics).equals(temp.getCapt())
                    || getString(R.string.kpi).equals(temp.getCapt())
                    ) {
                children.add(temp);
            }
        }
        menu.setChildren(children);//隐藏除可视化报表外其他的子项
        menuAdapter = new MenuAdapter(mContext, menu);
        elvMenu.setAdapter(menuAdapter);
        elvMenu.setOnChildClickListener(this);
        for (int i = 0; i < menu.getChildren().size(); i++) {
            if (
//                    getString(R.string.visual_reports).equals(menu.getChildren().get(i).getCapt()) ||
//                            getString(R.string.financial_statistics).equals(menu.getChildren().get(i).getCapt()) ||
                    getString(R.string.kpi).equals(menu.getChildren().get(i).getCapt())
                    ) {
                elvMenu.expandGroup(i);
            }
        }
    }

    @Override
    public boolean onChildClick(ExpandableListView parent, View v, int groupPosition, int childPosition, long id) {
        Menu rootMenu = menu.getChildren().get(groupPosition);
        List<Menu> menuList = rootMenu.getChildren();
        Menu childMenu = menuList.get(childPosition);
        String url = childMenu.getUrl().replace("?", "").trim().substring(1);
        Class<?> cls = CommUrlConstant.matchMap.get(url);
        if (cls != null) {
            Intent intent = new Intent(mContext, cls);
            intent.putParcelableArrayListExtra(KpiHomeActivity.ARG_PARAM1, (ArrayList<? extends Parcelable>) menuList);
            intent.putExtra(KpiHomeActivity.ARG_PARAM2, url);
            startActivity(intent);
        }
        return false;
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            if (backCount == 0) {
                showToastShort(R.string.click_again_exit);
                backCount++;
            } else {
                AppManager.getAppManager().finishAllActivity();
            }
            new Handler().postDelayed(new Runnable() {
                @Override
                public void run() {
                    backCount = 0;
                }
            }, 2000);
        }
        return false;
    }
}
