package com.kuaikuaidai.kkdaireport.util;

import java.util.Stack;

import android.app.Activity;
import android.content.Context;

/**
 * 应用程序Activity管理类：用于Activity管理和应用程序退出
 */
public class AppManager {

	private static Stack<Activity> activityStack;
	private static AppManager instance;

	private AppManager() {
	}

	/**
	 * 单一实例
	 */
	public static AppManager getAppManager() {
		if (instance == null) {
			if (activityStack != null) {
				activityStack.clear();
			}
			instance = new AppManager();
			if (AppManager.activityStack == null) {
				activityStack = new Stack<Activity>();
			}
		}
		return instance;
	}

	/**
	 * add Activity 添加Activity到栈
	 */
	public void addActivity(Activity activity) {
		if (activityStack == null) {
			activityStack = new Stack<Activity>();
		}
		activityStack.add(activity);
	}

	/**
	 * get current Activity 获取当前Activity（栈中最后一个压入的）
	 */
	public Activity currentActivity() {
		if (activityStack != null && activityStack.size() > 0) {
			return activityStack.lastElement();
		}
		return null;
	}

	/**
	 * 获取指定的Activity
	 * 
	 * @param name
	 *            activity类名 eg:com.xiaoxialicai.xxlc.TabHostAct
	 * @return
	 */
	public Activity getActivity(String name) {
		Activity activity = null;
		if (name != null && name.trim().length() != 0) {
			for (Activity temp : activityStack) {
				if (temp != null && name.equals(temp.getComponentName().getClassName())) {
					activity = temp;
				}
			}
		}
		return activity;
	}

	/**
	 * 结束当前Activity（栈中最后一个压入的）
	 */
	public void finishActivity() {
		Activity activity = activityStack.lastElement();
		finishActivity(activity);
	}

	/**
	 * 结束指定的Activity
	 */
	public void finishActivity(Context activity) {
		if (activity != null&&activity instanceof Activity) {
			activityStack.remove(activity);
			((Activity)activity).finish();
			activity = null;
		}
	}

	/**
	 * 结束除指定Activity外的所有activity
	 * 
	 * @param name
	 *            activity类名 eg:com.xiaoxialicai.xxlc.TabHostAct
	 */
	public void finishExceptActivity(String name) {
		if (name != null && name.trim().length() != 0) {
			String className;
			for (int i = 0, size = activityStack.size(); i < size; i++) {
				if (null != activityStack.get(i)) {
					className = activityStack.get(i).getComponentName().getClassName();
					if (!className.equals(name)) {
						activityStack.get(i).finish();
					}
				}
			}
		}
	}

	/**
	 * 结束除指定Activity外的所有activity
	 * 
	 * @param name1
	 *            activity类名
	 * @param name2
	 *            activity类名
	 */
	public void finishExceptActivity(String name1, String name2) {
		if (name1 != null && name1.trim().length() != 0 && name2 != null && name2.trim().length() != 0) {
			String className;
			for (int i = 0, size = activityStack.size(); i < size; i++) {
				if (null != activityStack.get(i)) {
					className = activityStack.get(i).getComponentName().getClassName();
					if ((!className.equals(name1)) && (!className.equals(name2))) {
						activityStack.get(i).finish();
					}
				}
			}
		}
	}

	/**
	 * 结束指定类名的Activity
	 */
	public void finishActivity(Class<?> cls) {
		for (Activity activity : activityStack) {
			if (activity.getClass().equals(cls)) {
				activity.finish();
			}
		}
	}

	/**
	 * 判断当前activity是否存在
	 * 
	 * @param cls
	 */
	public boolean isExisttActivity(Class<?> cls) {
		for (Activity activity : activityStack) {
			if (activity.getClass().equals(cls)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 当前栈顶的Class和传递进来的Class是否相等
	 * 
	 * @param cls
	 * @return
	 */
	public boolean isCurrentActivity(Class<?> cls) {
		if (activityStack != null && activityStack.size() != 0) {
			if (activityStack.lastElement().getClass().equals(cls)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 结束所有Activity
	 */
	public void finishAllActivity() {
		for (int i = 0, size = activityStack.size(); i < size; i++) {
			if (null != activityStack.get(i)) {
				activityStack.get(i).finish();
			}
		}
		activityStack.clear();
	}

	/**
	 * 获得栈中Activity的数量
	 * 
	 * @return
	 */
	public int getSize() {
		if (activityStack != null) {
			return activityStack.size();
		} else {
			return 0;
		}

	}

	/**
	 * 退出应用程序
	 */
	public void AppExit() {
		try {
			finishAllActivity();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	public void clearObj() {
		activityStack.clear();
		instance = null;
	}
}