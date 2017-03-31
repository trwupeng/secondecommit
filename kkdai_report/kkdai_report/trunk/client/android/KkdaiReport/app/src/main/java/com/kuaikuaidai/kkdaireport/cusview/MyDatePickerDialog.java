package com.kuaikuaidai.kkdaireport.cusview;

import android.app.DatePickerDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.view.View;
import android.view.ViewGroup;
import android.widget.DatePicker;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.config.Configure;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.Date;

public abstract class MyDatePickerDialog extends DatePickerDialog {

    public enum Type {
        YEAR, MONTH, DAY
    }

    private DatePicker mPicker;

    private Type type;

    public final static int defaultData = 1;


    public MyDatePickerDialog(Context context, int year) {
        super(context, R.style.AppTheme_Dialog, null, year, defaultData, defaultData);
        type = Type.YEAR;
        init(year, -1, -1);
    }

    public MyDatePickerDialog(Context context, int year, int month) {
        super(context, R.style.AppTheme_Dialog, null, year, month, defaultData);
        type = Type.MONTH;
        init(year, month, -1);
    }

    public MyDatePickerDialog(Context context, int year, int month, int day) {
        super(context, R.style.AppTheme_Dialog, null, year, month, day);
        type = Type.DAY;
        init(year, month, day);
    }

    public abstract void DateChanged(int year, int month, int day);

    private void init(int year, int month, int day) {
        mPicker = getDatePicker();
        mPicker.setMaxDate(new Date().getTime());
        mPicker.setMinDate(DateUtil.getTime(Configure.MIN_DATE));
        if (type == Type.YEAR || type == Type.MONTH) {
            hideChildren();
            setTitle(year, month + 1);
        }
    }

    @Override
    public void onDateChanged(DatePicker view, int year, int month, int day) {
        super.onDateChanged(view, year, month, day);
        if (type == Type.YEAR || type == Type.MONTH) {
            setTitle(year, month + 1);
        }
    }

    @Override
    public void onClick(DialogInterface dialog, int which) {
        super.onClick(dialog, which);
        if (which == DialogInterface.BUTTON_POSITIVE) {
            DateChanged(mPicker.getYear(), mPicker.getMonth(), mPicker.getDayOfMonth());
        }
    }

    private void setTitle(int year, int month) {
        switch (type) {
            case MONTH:
                setTitle(year + "年" + month + "月");
                break;
            case YEAR:
                setTitle(year + "年");
                break;
        }
    }

    private void hideChildren() {
        ViewGroup group = ((ViewGroup) ((ViewGroup) mPicker.getChildAt(0)).getChildAt(0));
        switch (type) {
            case MONTH:
                group.getChildAt(2).setVisibility(View.GONE);
                break;
            case YEAR:
                group.getChildAt(1).setVisibility(View.GONE);
                group.getChildAt(2).setVisibility(View.GONE);
                break;
        }
    }

}
