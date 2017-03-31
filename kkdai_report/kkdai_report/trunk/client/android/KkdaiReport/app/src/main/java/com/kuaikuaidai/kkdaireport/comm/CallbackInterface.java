package com.kuaikuaidai.kkdaireport.comm;

public interface CallbackInterface
{
		void onCallback(long code, String msg, Exception e, String api, String useData);
}
