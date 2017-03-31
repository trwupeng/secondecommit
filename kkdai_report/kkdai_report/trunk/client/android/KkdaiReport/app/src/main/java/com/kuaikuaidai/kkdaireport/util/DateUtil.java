package com.kuaikuaidai.kkdaireport.util;


import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class DateUtil {

    public final static SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");

    public static String getBoforeDay(int days) {
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(new Date());
        calendar.add(Calendar.DAY_OF_MONTH, -days);
        return format.format(calendar.getTime());
    }

    public static Calendar getBoforeTime(int days) {
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(new Date());
        calendar.add(Calendar.DAY_OF_MONTH, -days);
        return calendar;
    }

    public static String getCurrent() {
        return format.format(new Date());
    }

    public static Calendar getCurrentCalendar() {
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(new Date());
        return calendar;
    }

    public static long getTime(String date) {
        long time = 0;
        try {
            time = (format.parse(date)).getTime();
        } catch (Exception e) {
            return 0;
        }
        return time;
    }

    public static String getLastSunday() {
        Calendar cal = Calendar.getInstance();
        cal.set(Calendar.DAY_OF_WEEK, Calendar.SUNDAY);
        return format.format(cal.getTime());
    }

    public static String getLastMonday() {
        Calendar cal = Calendar.getInstance();
        cal.add(Calendar.WEEK_OF_MONTH, -1);
        cal.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY);
        return format.format(cal.getTime());
    }

    public static Calendar getLastSundayCalendar() {
        Calendar cal = Calendar.getInstance();
        cal.set(Calendar.DAY_OF_WEEK, Calendar.SUNDAY);
        return cal;
    }

    public static Calendar getLastMondayCalendar() {
        Calendar cal = Calendar.getInstance();
        cal.add(Calendar.WEEK_OF_MONTH, -1);
        cal.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY);
        return cal;
    }

}
