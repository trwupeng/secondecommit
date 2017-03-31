package com.kuaikuaidai.kkdaireport.util;

import android.util.Log;

public class Logger {
	public static int LOG_LEVEL =7;

	public static final int VERBOSE = 5;
	public static final int DEBUG = 4;
	public static final int INFO = 3;
	public static final int WARN = 2;
	public static final int ERROR = 1;
	private static final int MAX = 4000;

	public static void v(String tag, String msg) {
		if (LOG_LEVEL > VERBOSE && msg != null) {
			log(tag, msg, VERBOSE);
		}
	}

	public static void d(String tag, String msg) {
		if (LOG_LEVEL > DEBUG && msg != null) {
			log(tag, msg, DEBUG);
		}
	}

	public static void i(String tag, String msg) {
		if (LOG_LEVEL > INFO && msg != null) {
			log(tag, msg, INFO);
		}
	}

	public static void w(String tag, String msg) {
		if (LOG_LEVEL > WARN && msg != null) {
			log(tag, msg, WARN);
		}
	}

	public static void e(String tag, String msg) {
		if (LOG_LEVEL > ERROR && msg != null) {
			log(tag, msg, ERROR);
		}
	}

	public static void log(String tag, String content, int type) {
		long length = content.length();
		if (length < MAX || length == MAX) {
			output(tag, content, type);
		} else {
			while (content.length() > MAX) {
				String logContent = content.substring(0, MAX);
				content = content.replace(logContent, "");
				output(tag, logContent, type);
			}
			output(tag, content, type);
		}
	}

	public static void output(String tag, String content, int type) {
		switch (type) {
		case ERROR:
			Log.e(tag, content);
			break;
		case WARN:
			Log.w(tag, content);
			break;
		case INFO:
			Log.i(tag, content);
			break;
		case DEBUG:
			Log.d(tag, content);
			break;
		case VERBOSE:
			Log.v(tag, content);
			break;
		}
	}
}
