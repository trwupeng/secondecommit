package com.kuaikuaidai.kkdaireport.util;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.security.MessageDigest;
import android.app.Activity;
import android.content.Context;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.content.res.Resources;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.net.wifi.WifiInfo;
import android.net.wifi.WifiManager;
import android.os.Environment;
import android.provider.MediaStore;
import android.telephony.TelephonyManager;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageView;

import com.kuaikuaidai.kkdaireport.base.BaseApplication;

public class CommUtil {
	private static long lastClickTime;
	private static long clickStay = 0;
	private static long serverTimeDelta = 0;
	private static boolean debug = false;

	public static long getClickStay() {
		return clickStay;
	}

	public static void setClickStay(long clickStay) {
		CommUtil.clickStay = clickStay;
	}

	public static boolean isFastDoubleClick() {
		long time = System.currentTimeMillis();
		long timeD = time - lastClickTime;
		if (clickStay != 0) {
			if (0 < timeD && timeD < clickStay) {
				return true;
			}
		} else {
			if (0 < timeD && timeD < 500) {
				return true;
			}
		}
		lastClickTime = time;
		return false;
	}

	public static void dismissInput(Activity act) {

		InputMethodManager systemService = (InputMethodManager) act
				.getSystemService(Context.INPUT_METHOD_SERVICE);

		View currentFocus = act.getCurrentFocus();

		if (currentFocus != null) {
			systemService.hideSoftInputFromWindow(
					currentFocus.getWindowToken(),
					InputMethodManager.HIDE_NOT_ALWAYS);

		}

	}



	// 判断wifi网络是否可用
	public static boolean isWifiDataEnable(Context context) {
		try {
			ConnectivityManager connectivityManager = (ConnectivityManager) context
					.getSystemService(Context.CONNECTIVITY_SERVICE);
			boolean isWifiDataEnable = false;
			isWifiDataEnable = connectivityManager.getNetworkInfo(
					ConnectivityManager.TYPE_WIFI).isConnectedOrConnecting();
			return isWifiDataEnable;

		} catch (Exception e) {
			// TODO Auto-generated catch block
			return false;
		}
	}

	public static String getMD5(String paramString) {
		try {
			MessageDigest localMessageDigest = MessageDigest.getInstance("MD5");
			localMessageDigest.update(paramString.getBytes());
			String str = toHexString(localMessageDigest.digest());
			return str;
		} catch (Exception localException) {
		}
		return "";
	}

	public static String toHexString(byte[] paramArrayOfByte) {
		StringBuilder localStringBuilder = new StringBuilder(
				2 * paramArrayOfByte.length);
		int i = paramArrayOfByte.length;
		for (int j = 0;; j++) {
			if (j >= i) {
				return localStringBuilder.toString();
			}
			int k = paramArrayOfByte[j];
			if ((k & 0xFF) < 16) {
				localStringBuilder.append("0");
			}
			localStringBuilder.append(Integer.toHexString(k & 0xFF));
		}
	}

	public static String getMac(Context ctx) {

		String macAddress = "0";

		WifiManager wifi = (WifiManager) ctx
				.getSystemService(Context.WIFI_SERVICE);

		WifiInfo info = wifi.getConnectionInfo();

		macAddress = info.getMacAddress();

		return macAddress;

	}

	/**
	 *  得到当前的手机网络类型
	 * @return
     */
	public static String getCurrentNetType() {
		String type = "";
		ConnectivityManager cm = (ConnectivityManager) BaseApplication
				.getInstance().getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo info = cm.getActiveNetworkInfo();
		if (info == null) {
			type = "无网络连接";
		} else if (info.getType() == ConnectivityManager.TYPE_WIFI) {
			type = "wifi";
		} else if (info.getType() == ConnectivityManager.TYPE_MOBILE) {
			int subType = info.getSubtype();
			if (subType == TelephonyManager.NETWORK_TYPE_CDMA
					|| subType == TelephonyManager.NETWORK_TYPE_GPRS
					|| subType == TelephonyManager.NETWORK_TYPE_EDGE) {
				type = "2g";
			} else if (subType == TelephonyManager.NETWORK_TYPE_UMTS
					|| subType == TelephonyManager.NETWORK_TYPE_HSDPA
					|| subType == TelephonyManager.NETWORK_TYPE_EVDO_A
					|| subType == TelephonyManager.NETWORK_TYPE_EVDO_0
					|| subType == TelephonyManager.NETWORK_TYPE_EVDO_B
					|| subType == TelephonyManager.NETWORK_TYPE_HSPAP) {
				type = "3g";
			} else if (subType == TelephonyManager.NETWORK_TYPE_LTE) {// LTE是3g到4g的过渡，是3.9G的全球标准
				type = "4g";
			}
		}
		return type;
	}

	public static String getVersion(Context act) {
		PackageManager pm = act.getPackageManager();
		try {
			PackageInfo packInfo = pm.getPackageInfo(act.getPackageName(), 0);
			return packInfo.versionName;
		} catch (NameNotFoundException e) {
			e.printStackTrace();
			// can't reach
			return "";
		}
	}

	public static File getUriToFile(Activity ctx, Uri uri) {
		File file = null;
		try {
			String[] proj = { MediaStore.Images.Media.DATA };

			Cursor actualimagecursor = ctx.managedQuery(uri, proj, null, null,
					null);

			int actual_image_column_index = actualimagecursor
					.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);

			actualimagecursor.moveToFirst();

			String img_path = actualimagecursor
					.getString(actual_image_column_index);

			file = new File(img_path);
		} catch (Exception e) {
			// TODO: handle exception
			e.printStackTrace();
		}

		return file;
	}

	/**
	 * 获取当前的网络状态 true(有网络) / false(无网络)
	 * 
	 * @param context
	 * @return
	 */
	public static boolean checkNetwork(Context context) {
		return PhoneNetUtils.checkNetwork();
	}

	public static int dip2px(Context context, double d) {
		final float scale = context.getResources().getDisplayMetrics().density;
		return (int) (d * scale + 0.5f);
	}

	public static int px2dip(Context context, double pxValue) {
		final float scale = context.getResources().getDisplayMetrics().density;
		return (int) (pxValue / scale + 0.5f);
	}

	public static boolean isSDcardExist() {

		return Environment.getExternalStorageState().equals(
				Environment.MEDIA_MOUNTED);
	}

	public static File getSDCard() {

		return Environment.getExternalStorageDirectory();

	}

	public static void changeFileMode(String f) throws IOException {

		String[] command = { "chmod", "777", f };
		ProcessBuilder builder = new ProcessBuilder(command);
		builder.start();

	}

	/**
	 * 压缩图片大小
	 * 
	 * @param image
	 * @return
	 */
	public static Bitmap compressImage(Bitmap image) {
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		image.compress(Bitmap.CompressFormat.JPEG, 50, baos);// 质量压缩方法，这里100表示不压缩，把压缩后的数据存放到baos中
		int options = 100;
		while (baos.toByteArray().length / 1024 > 50) { // 循环判断如果压缩后图片是否大于100kb,大于继续压缩
			baos.reset();// 重置baos即清空baos
			image.compress(Bitmap.CompressFormat.JPEG, options, baos);// 这里压缩options%，把压缩后的数据存放到baos中
			options -= 10;// 每次都减少10
		}
		ByteArrayInputStream isBm = new ByteArrayInputStream(baos.toByteArray());// 把压缩后的数据baos存放到ByteArrayInputStream中
		Bitmap bitmap = BitmapFactory.decodeStream(isBm, null, null);// 把ByteArrayInputStream数据生成图片
		if (bitmap != null && !bitmap.isRecycled()) {
			bitmap.recycle();
			bitmap = null;
		}
		System.gc();
		return bitmap;
	}

	public static Resources getResource() {
		return BaseApplication.getInstance().getResources();
	}

	/**
	 * 启用相机照相后保存图片的地址
	 * 
	 * @param ctx
	 * @return
	 */
	public static File getUserImageCacheDir(Context ctx, String fileName) {

		if (isSDcardExist()) {

			File sdCard = getSDCard();

			File filePath = new File(sdCard + "/Yhouse/");
			if (!filePath.exists()) {
				filePath.mkdirs();
			}

			File fs = new File(filePath, fileName + "user.jpg");

			return fs;

		} else {

			File dir = ctx.getDir("cacheFile", Context.MODE_PRIVATE
					| Context.MODE_WORLD_READABLE
					| Context.MODE_WORLD_WRITEABLE);

			File fs = new File(dir, "yhouseUser.jpg");

			try {
				changeFileMode(fs.getPath());
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			return fs;
		}

	}

	public static File saveUserImage(String fileName, Bitmap bitmap) {

		File fs = null;

		if (bitmap == null) {
			return fs;
		}

		if (isSDcardExist()) {

			File sdCard = getSDCard();

			File filePath = new File(sdCard + "/Yhouse/cacheimg/");
			if (!filePath.exists()) {
				filePath.mkdirs();
			}

			File fss = new File(filePath, fileName + ".jpg");

			try {
				FileOutputStream fos = new FileOutputStream(fss);

				if (bitmap.compress(Bitmap.CompressFormat.JPEG, 100, fos)) {
					fos.flush();
					fos.close();
				}

				fs = fss;

			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

		}

		return fs;

	}

	public static String operStr(String result) {
		if (result.lastIndexOf(".") > -1) {
			String s = result.substring(0, result.lastIndexOf(".") + 1);
			String last = result.substring(result.lastIndexOf(".") + 1,
					result.length());// (str.endsWith("."), str.length());
			if (last.length() == 1) {
				last = last + 0;
			} else if (last.length() > 2) {
				last = last.substring(0, 2);
			}
			result = s + last;
		}
		return result;
	}



	public static void wait2Seconds() {
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	/**
	 * 获取渠道名
	 * 
	 * @param ctx
	 *            此处习惯性的设置为activity，实际上context就可以
	 * @return 如果没有获取成功，那么返回值为空
	 */
	/**
	 * 获取application中指定的meta-data
	 * 
	 * @return 如果没有获取成功(没有对应值，或者异常)，则返回值为空
	 */
	public static String getAppMetaData(Context ctx, String key) {
		String resultData = null;
		try {
			PackageManager packageManager = ctx.getPackageManager();
			if (packageManager != null) {  
				ApplicationInfo applicationInfo = packageManager
						.getApplicationInfo(ctx.getPackageName(),
								PackageManager.GET_META_DATA);
				if (applicationInfo != null) {
					if (applicationInfo.metaData != null) {
						if(key!=null&&key.equals("TD_APP_ID")){
							resultData = applicationInfo.metaData.getString(key);
							return resultData;
						}else if(key!=null&&key.equals("TD_CHANNEL_ID")){
							resultData = applicationInfo.metaData.getInt(key)
									+ "".trim();
							return resultData;
						}else if(key!=null&&key.equals("APP_CHANNEL_ID")){
							resultData = applicationInfo.metaData.getString(key)
									+ "".trim();
							return resultData;
						}
						resultData = applicationInfo.metaData.getString(key);
						if (resultData == null) {
							resultData = applicationInfo.metaData.getInt(key)
									+ "".trim();
						}
					}
				}
			}
		} catch (Exception e) {
			Logger.e("getAppMetaData", "TD_CHANNEL_ID");
			return "";
		}
		return resultData;
	}



	public static String formatString(String str, String... args) {
		for (int i = 0; i < args.length; i++) {
			if (str.indexOf("{" + (i + 1) + "}") != -1) {
				str = str.replace("{" + (i + 1) + "}", args[i]);
			} else {
				break;
			}
		}
		return str;

	}

	/**
	 * 以最省内存的方式读取本地资源的图片
	 * 
	 * @param context
	 * @param resId
	 * @return
	 */
	public static Bitmap readBitMap(Context context, int resId) {
		BitmapFactory.Options opt = new BitmapFactory.Options();
		opt.inPreferredConfig = Bitmap.Config.RGB_565;
		opt.inPurgeable = true;
		opt.inInputShareable = true;
//		opt.inJustDecodeBounds = true;
		opt.inSampleSize=3;
		// 获取资源图片
		InputStream is = context.getResources().openRawResource(resId);
		return BitmapFactory.decodeStream(is, null, opt);
	}
	public static void releaseImageViewResouce(ImageView imageView) {
        if (imageView == null) return;
        Drawable drawable = imageView.getDrawable();
        if (drawable != null && drawable instanceof BitmapDrawable) {
            BitmapDrawable bitmapDrawable = (BitmapDrawable) drawable;
            Bitmap bitmap = bitmapDrawable.getBitmap();
            if (bitmap != null && !bitmap.isRecycled()) {
                bitmap.recycle();
            }
        }
    }
	
	/**
	 * 功能：检测当前URL是否可连接或是否有效, 描述：最多连接网络3 次, 如果3 次都不成功，视为该地址不可用
	 * 
	 * @param urlStr
	 *            指定URL网络地址
	 * @return URL
	 */
	public static boolean isConnect(String urlStr) {
		URL url;
		HttpURLConnection con;
		int counts = 0;
		if (urlStr == null || urlStr.length() <= 0) {
			return false;
		}
		while (counts < 3) {
			try {
				url = new URL(urlStr);
				con = (HttpURLConnection) url.openConnection();
				if (con.getResponseCode() == 200) {
					return true;
				}
				break;
			} catch (Exception ex) {
				counts++;
				continue;
			}
		}
		return false;
	}

	public static void setDebug( boolean b )
	{
		debug = b;
	}
	public static boolean isDebug()
	{
		return debug;
	}

	/**
	 * 如果是1则返回true 0则返回false
	 * 
	 * @param str
	 * @return
	 */
	public static boolean isString(String str) {
		return "1".equals(str) ? true : false;
	}
}
