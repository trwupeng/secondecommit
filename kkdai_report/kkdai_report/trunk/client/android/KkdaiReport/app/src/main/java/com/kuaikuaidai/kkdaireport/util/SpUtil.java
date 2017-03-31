package com.kuaikuaidai.kkdaireport.util;

import android.content.Context;
import android.content.SharedPreferences;
import com.alibaba.fastjson.JSON;
import com.kuaikuaidai.kkdaireport.base.BaseApplication;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.StreamCorruptedException;
import java.io.UnsupportedEncodingException;

public class SpUtil {

	public static final String CONFIG = "config";

	public static SharedPreferences getSp(Context ctx) {
		return ctx.getSharedPreferences(CONFIG, Context.MODE_PRIVATE);
	}

	public static void editConfig(Context context, String key, String value) {
		SharedPreferences.Editor editor = getSp(context).edit();
		editor.putString(key, value);
		editor.commit();
	}

	public static void editConfigForInt(Context context, String key, int value) {
		SharedPreferences.Editor editor = getSp(context).edit();
		editor.putInt(key, value);
		editor.commit();
	}

	public static void editConfigForBoolean(Context context, String key,boolean value) {
		SharedPreferences.Editor editor = getSp(context).edit();
		editor.putBoolean(key, value);
		editor.commit();
	}

	public static String readConfig(Context context, String key) {
		return getSp(context).getString(key, "");
	}

	public static int readConfigForInt(Context context, String key) {
		return getSp(context).getInt(key, -1);
	}

	public static boolean readConfigForBoolean(Context context, String key) {
		return getSp(context).getBoolean(key, false);
	}

	
	public static void saveObjectBase64(Context ctx,String key, Object obj) {

		try {

			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			ObjectOutputStream oos = new ObjectOutputStream(baos);
			oos.writeObject(obj);
			oos.close();
			saveBase64(ctx,key, baos.toByteArray());

		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void saveBase64(Context ctx,String key, byte[] buf) {
		// 将字节流编码成base64的字符窜
		String base64 = Base64.encode(buf);
		getSp(ctx).edit().putString(key, base64).commit();

	}
	/**
	 * 保存有一个class对象
	 * @param ctx
	 * @param key
	 * @param obj
	 */
	public static void saveObject(Context ctx,String key,Object obj){
		String jsonStr = JSON.toJSONString(obj);
		String encode = Base64.encode(jsonStr.getBytes());
		getSp(ctx).edit().putString(key, encode).commit();
	}
	
	/**
	 * 根据 key获取一个持久化对象 T (list/obj)
	 * 
	 * @param key
	 * @return
	 */
	public static Object readObjectBase64(Context ctx,String key) {

		String productBase64 = getSp(ctx).getString(key, null);
		if (productBase64 == null) {
			return null;
		}

		Object Object = null;
		// 读取字节
		byte[] base64 = Base64.decode(productBase64);

		// 封装到字节流
		ByteArrayInputStream bais = new ByteArrayInputStream(base64);
		try {
			// 再次封装
			ObjectInputStream bis = new ObjectInputStream(bais);
			try {
				// 读取对象
				Object = bis.readObject();
			} catch (ClassNotFoundException e) {
				e.printStackTrace();
			}
		} catch (StreamCorruptedException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}

		return Object;

	}
	
	/**
	 * 获取有一个class对象
	 * @param ctx
	 * @param key
	 * @param calzz
	 * @return
	 */
	public static Object getObject(Context ctx,String key ,Class<?> calzz){
		String str = getSp(ctx).getString(key, "");
		if (str.equals("")) {
			return null;
		}
		byte[] decodeBase64 = Base64.decode(str);
		try {
			str = new String(decodeBase64, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		if(str==null){
		   return null;
		}
		try {
		   return JSON.parseObject(str, calzz);
		} catch (Exception e) {
		  e.printStackTrace();
		  return null;
		}
	}
	
	/**
	 * 获取有一个class对象
	 * @param ctx
	 * @param key
	 * @return
	 */
	public static String getObjectString(Context ctx,String key){
		String str = getSp(ctx).getString(key, "");
		if (str.equals("")) {
			return null;
		}
		byte[] decodeBase64 = Base64.decode(str);
		try {
			str = new String(decodeBase64, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		return str;
	}
	/**
	 * 清除用户数据，可以做登出使用
	 */
	public static  void clearData(){
		SharedPreferences.Editor editor = getSp(BaseApplication.getInstance()).edit();
		editor.clear();
		editor.commit();
	}
	
}
