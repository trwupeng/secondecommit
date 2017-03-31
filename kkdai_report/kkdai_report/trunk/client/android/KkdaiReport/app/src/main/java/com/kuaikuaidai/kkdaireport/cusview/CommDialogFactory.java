package com.kuaikuaidai.kkdaireport.cusview;


import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.view.Display;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;


public class CommDialogFactory {

	private static Dialog progressDialog;
	private static int showRef = 0;


	private static Dialog progressDialog(Context ctx, String content) {

		View progressView = View.inflate(ctx, R.layout.view_dialog_progress,
				null);

		TextView tv_content = (TextView) progressView
				.findViewById(R.id.tv_content);
		tv_content.setText(content);
		return rootDialog(ctx, progressView);
	}
	
	
	private static Dialog progressDialog(Context ctx, OnDialogShowText listener) {

		View progressView = View.inflate(ctx, R.layout.view_dialog_progress,
				null);

		TextView tv_content = (TextView) progressView
				.findViewById(R.id.tv_content);
		if(listener != null){
			listener.showText(tv_content);
		}
		return rootDialog(ctx, progressView);
	}
	
	
	private static Dialog progressDialogScollView(Context ctx, String content){
		View progressView = View.inflate(ctx, R.layout.view_dialog_progress,
				null);

		TextView tv_content = (TextView) progressView
				.findViewById(R.id.tv_content);
		return rootDialog(ctx, progressView);
	}
	
	
	
	public interface OnDialogShowText{
		
		public void showText(TextView view);
		
	}
	

	private static Dialog rootDialog(Context ctx, View rootView) {

		Dialog d = new Dialog(ctx, R.style.smart_dialog);

		Window window = d.getWindow();

		window.setGravity(Gravity.CENTER);

//		window.setWindowAnimations(R.style.dialog_style);

		d.setCancelable(false);
		d.setCanceledOnTouchOutside(false);

		d.setContentView(rootView);

		Display display = ((Activity)ctx).getWindowManager().getDefaultDisplay();

		WindowManager.LayoutParams lp = window.getAttributes();

//		lp.width = (int) (display.getWidth());

		d.show();

		window.setAttributes(lp);

		return d;
	}

	
	public static Dialog InfoBaseDialog(Context ctx, String title,
			String content, String cancelText, String okText,
			OnClickListener cancel, OnClickListener ok) {

		View view = View.inflate(ctx, R.layout.view_dialog_base_info, null);
		TextView tv_title = (TextView) view.findViewById(R.id.tv_title);
		TextView tv_content = (TextView) view.findViewById(R.id.tv_content);
		final Button btn_cancel = (Button) view.findViewById(R.id.btn_cancel);
		final Button btn_ok = (Button) view.findViewById(R.id.btn_ok);

		tv_title.setText(title);
		tv_content.setText(content);
		btn_cancel.setText(cancelText);
		btn_ok.setText(okText);
		btn_cancel.setOnClickListener(cancel);
		btn_ok.setOnClickListener(ok);

		return rootDialog(ctx, view);
	}
	
	public static void showLoadingDialog(Context ctx, OnDialogShowText listener) {
		progressDialog = progressDialog(ctx, listener);
	}
	
	public static void showLoadingDialog(Context ctx, String content) {
		if(progressDialog==null){
		   progressDialog = progressDialog(ctx, content);
		   showRef = 1;
		}else if(progressDialog!=null&&!progressDialog.isShowing()){
		   progressDialog = progressDialog(ctx, content);
		   showRef = 1;
		}else {
			++showRef;
		}
	}

	public static void showLoadingDialog(Context ctx) {
		   progressDialog = progressDialog(ctx, "请求中...");
	}

	public static void dismissLoadingDialog() {
		if (progressDialog != null) {
			if ( showRef > 0 )
			{
				--showRef;
			}
			if ( showRef <= 0 )
			{
				showRef = 0;
				progressDialog.dismiss();
			}
		}
	}
	
	
	public static void dissmissNull(){
		if (progressDialog != null) {
			progressDialog.dismiss();
			progressDialog=null;
		}
	}
	
	
	public void requestFouce(){
		if (progressDialog != null) {
		}
	}
}
