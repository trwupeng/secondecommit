package com.kuaikuaidai.kkdaireport.comm;

public interface CallbackInterfaceInner
{
		void onCallback(long code, String data, Exception e, long sn, String cookie, String api, String userData);
}
