package com.kuaikuaidai.kkdaireport.comm;


public class CommInfo
{
	static private long _maxSN = 0;
	private String _url = null;
	private int _interval = 0;
	private long _sn = 0;
	private int _retry = 1;
	private String _cookie = null;
	private String _api = null;
	private String _userData = null;
	private CallbackInterfaceInner _callback = null;
	private int _timer = 0;
	private int _type = 0;
	
	public CommInfo()
	{
		++_maxSN;
		_sn = _maxSN;
	}
	
	public void setUrl( String url )
	{
		_url = url;
	}
	public String getUrl()
	{
		return _url;
	}
	
	public void setTimeout( int t )
	{
		_interval = t;
	}
	public int getTimeout()
	{
		return _interval;
	}
	
	public void setCallback( CallbackInterfaceInner cb )
	{
		_callback = cb;
	}
	public CallbackInterfaceInner getCallback()
	{
		return _callback;
	}
	
	public long getSN()
	{
		return _sn;
	}
	
	public void setRetry( int r )
	{
		if ( r < 0 )
		{
			r = 0;
		}
		else if ( r > 100 )
		{
			r = 100;
		}
		_retry = r;
	}
	public int getRetry()
	{
		return _retry;
	}
	
	public void setCookie( String cookie )
	{
		_cookie = cookie;
	}
	public String getCookie()
	{
		if ( null != _cookie )
		{
			return _cookie;
		}
		return CommConstant.COOKIE_PREFIX + "null";
	}
	public void setApi( String api )
	{
		_api = api;
	}
	public String getApi()
	{
		return _api;
	}
	
	public void setUserData( String userData )
	{
		_userData = userData;
	}
	public String getUserData()
	{
		return _userData;
	}
	
	public void setTimer( int timer ) {
		_timer = timer;
	}
	public int getTimer() {
		return _timer;
	}
	
	public void setType( int type ) {
		_type = type;
	}
	public int getType() {
		return _type;
	}
	
	public void destroy()
	{
		_url = null;
		_cookie = null;
		_api = null;
		_userData = null;
		_callback = null;
	}
}