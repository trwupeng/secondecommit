package com.kuaikuaidai.kkdaireport.parse;


/**
 * 菜单解析
 */
public class TitleParse {

    private final static String TAG = "TitleParse";

    private static TitleParse titleParse;

    private TitleParse() {

    }

    public static TitleParse getInstance() {
        if (titleParse == null) {
            if (titleParse == null) {
                titleParse = new TitleParse();
            }
        }
        return titleParse;
    }

    private String date1;
    private String date2;
    private String date3;
    private String date4;

    public String getDate1() {
        return date1;
    }

    public void setDate1(String date1) {
        this.date1 = date1;
    }

    public String getDate2() {
        return date2;
    }

    public void setDate2(String date2) {
        this.date2 = date2;
    }

    public String getDate3() {
        return date3;
    }

    public void setDate3(String date3) {
        this.date3 = date3;
    }

    public String getDate4() {
        return date4;
    }

    public void setDate4(String date4) {
        this.date4 = date4;
    }
}
