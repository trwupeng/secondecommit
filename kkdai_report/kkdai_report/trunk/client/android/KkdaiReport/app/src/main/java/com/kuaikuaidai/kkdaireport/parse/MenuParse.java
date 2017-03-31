package com.kuaikuaidai.kkdaireport.parse;


import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.bean.Menu;
import com.kuaikuaidai.kkdaireport.util.Logger;

/**
 * 菜单解析
 */
public class MenuParse {

    private final static String TAG = "MenuParse";

    private static MenuParse menuParse;

    private MenuParse() {

    }

    public static MenuParse getInstance() {
        if (menuParse == null) {
            if (menuParse == null) {
                menuParse = new MenuParse();
            }
        }
        return menuParse;
    }

    private Menu menu;

    public Menu getMenu() {
        return menu;
    }

    public void setMenu(Menu menu) {
        this.menu = menu;
    }

    /**
     * 解析menus字段
     *
     * @param data
     */
    public void parseMenu(Object data) {
        try {
            if (data != null) {
                menu = JSON.parseObject(data.toString(), Menu.class);
            }
        } catch (Exception e) {
            Logger.e(TAG, "parse has error:" + e.getMessage());
        }
    }

}
