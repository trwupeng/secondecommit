package com.kuaikuaidai.kkdaireport.cusview;

import android.app.DatePickerDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.widget.DatePicker;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.config.Configure;
import com.kuaikuaidai.kkdaireport.util.DateUtil;

import java.util.Calendar;
import java.util.Date;

/**
 * Created by zhong.jiye on 2016/10/10.
 */


public abstract class MyDateDialog extends DatePickerDialog {
    private DatePicker picker;

    public abstract void DateChanged(int year, int month, int day, String date);

    public MyDateDialog(Context context, int year, int month, int day) {
        super(context, R.style.AppTheme_Dialog, null, year, month, day);
        picker = getDatePicker();
        picker.setMaxDate(new Date().getTime());
        picker.setMinDate(DateUtil.getTime(Configure.MIN_DATE));
    }

    @Override
    public void onClick(DialogInterface dialog, int which) {
        super.onClick(dialog, which);
        switch (which) {
            case DialogInterface.BUTTON_POSITIVE:
                Calendar calendar = Calendar.getInstance();
                calendar.set(picker.getYear(), picker.getMonth(), picker.getDayOfMonth());
                String date = DateUtil.format.format(calendar.getTime());
                DateChanged(picker.getYear(), picker.getMonth(), picker.getDayOfMonth(), date);
                break;
        }
    }


}
