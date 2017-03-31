package com.kuaikuaidai.kkdaireport.comm;

import android.os.Handler;

import com.kuaikuaidai.kkdaireport.util.Logger;

import java.io.IOException;
import java.net.Socket;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;
import java.security.Principal;
import java.security.PrivateKey;
import java.security.SecureRandom;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.locks.ReadWriteLock;
import java.util.concurrent.locks.ReentrantReadWriteLock;

import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.KeyManager;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509KeyManager;
import javax.net.ssl.X509TrustManager;

import okhttp3.Cookie;
import okhttp3.CookieJar;
import okhttp3.Headers;
import okhttp3.HttpUrl;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class CommThread
{
	private ReadWriteLock _locker = null;
	private int _threadNum = 0;
	private ArrayList<CommInfo> _arrCommInfo;
	private Handler _handler = null;
	
	static CommThread create( int threadNum, Handler h )
	{
		CommThread p = new CommThread();
		if ( null == p )
		{
			return null;
		}
		if ( threadNum <= 0 )
		{
			threadNum = 0;
		}
		p._handler = h;
		p._threadNum = threadNum;
		p.start();
		return p;
	}
	
	void start()
	{
		_arrCommInfo = new ArrayList<CommInfo>();
		_locker = new ReentrantReadWriteLock();
		
		for ( int i=0; i<_threadNum; ++i )
		{
			ThreadEntry p = new ThreadEntry();
			p.setLocker(_locker);
			p.setHandler(_handler);
			p.setCommInfo(_arrCommInfo);
			new Thread(p).start();
		}
	}
	
	void addCommInfo( CommInfo info )
	{
		if ( null == info )
		{
			return ;
		}
		
		_locker.writeLock().lock();
		_arrCommInfo.add(info);
		_locker.writeLock().unlock();
	}
	
}

class ThreadEntry implements Runnable
{
	private ReadWriteLock _locker = null;
	private ArrayList<CommInfo> _arrCommInfo = null;
	private Handler _handler = null;
	private OkHttpClient _client = null;
	private Exception _curExcept = null;
	
	void setLocker( ReadWriteLock locker )
	{
		_locker = locker;
	}
	void setCommInfo( ArrayList<CommInfo> p )
	{
		_arrCommInfo = p;
	}
	void setHandler( Handler h )
	{
		_handler = h;
	}
	@Override
	public void run()
	{
		while( true )
		{
			_curExcept = null;
			try {
				Thread.sleep(10);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				Thread.interrupted();
				_curExcept = e;
				break;
			}
			catch( Exception e )
			{
				_curExcept = e;
				break;
			}
			
			CommInfo p = null;
			_locker.writeLock().lock();
			if ( _arrCommInfo.size() == 0 )
			{
				_locker.writeLock().unlock();
				continue;
			}
			
			p = _arrCommInfo.get(0);
			_arrCommInfo.remove(0);
			_locker.writeLock().unlock();
			
			//send request
			doRequest(p);
		}
	}
	
	void doRequest( CommInfo commInfo )
	{
		if ( commInfo.getRetry() < 1 )
		{
			return ;
		}
		commInfo.setRetry( commInfo.getRetry()-1 );
		OkHttpClient client = getHttpClient(commInfo);
		if ( null == client )
		{
			CallbackInterfaceInner cb = commInfo.getCallback();
			if ( null != cb )
			{
				cb.onCallback(0, null, _curExcept, commInfo.getSN(), null, commInfo.getApi(), commInfo.getUserData() );
			}
		}

		
		Request request = new Request.Builder()
				.url(commInfo.getUrl())
				.header("SoohSessId", commInfo.getCookie())
				.build();
		
		_curExcept = null;
		try {
			Logger.d( "CommThread", "request start" );
			Response res = client.newCall(request).execute();
			Logger.d( "CommThread", "responsed success" );
			Headers headers = res.headers();
			String cookie = null;
			for ( int i=0; i<headers.size(); ++i )
			{
				String key = headers.name(i);
				String value = headers.get(key);
				Logger.d( "CommThread", "key:" + key + ", value:" + value );
				if ( "Set-Cookie".equals(key) )
				{
					String front = CommConstant.COOKIE_KEY + "=";
					int n = value.indexOf( front );
					if ( 0 == n )
					{
						String tmp = value.substring(front.length());
						int m = tmp.indexOf(";");
						if ( m >= 0 )
						{
							tmp = tmp.substring(0,m);
						}
						cookie = tmp;
					}
					break;
				}
			}
			CallbackInterfaceInner cb = commInfo.getCallback();
			if ( null != cb )
			{
				if ( res.isSuccessful() )
				{
					cb.onCallback(200, res.body().string(), null, commInfo.getSN(), cookie, commInfo.getApi(), commInfo.getUserData());
				}
				else
				{
					cb.onCallback(0, res.body().string(), null, commInfo.getSN(), cookie, commInfo.getApi(), commInfo.getUserData());
				}
			}
		} catch (IOException e) {
			// TODO Auto-generated catch block
			if ( commInfo.getRetry() > 0 )
			{
				addCommInfo( commInfo );
				return ;
			}
			_curExcept = e;
			CallbackInterfaceInner cb = commInfo.getCallback();
			if ( null != cb )
			{
				cb.onCallback(0, null, e, commInfo.getSN(), null, commInfo.getApi(), commInfo.getUserData() );
			}
		} catch( Exception e )
		{
			if ( commInfo.getRetry() > 0 )
			{
				addCommInfo( commInfo );
				return ;
			}
			_curExcept = e;
			CallbackInterfaceInner cb = commInfo.getCallback();
			if ( null != cb )
			{
				cb.onCallback(0, null, e, commInfo.getSN(), null, commInfo.getApi(), commInfo.getUserData() );
			}
		}
	}
	
	void addCommInfo( CommInfo info )
	{
		if ( null == info )
		{
			return ;
		}
		
		_locker.writeLock().lock();
		_arrCommInfo.add(info);
		_locker.writeLock().unlock();
	}
	
	OkHttpClient httpClient()
	{
		if ( null != _client )
		{
			return _client;
		}
		
		_client = new OkHttpClient();
		return _client;
	}
	
	OkHttpClient getHttpClient( final CommInfo commInfo )
	{
		SSLContext sc = null;
		_curExcept = null;
		try {
			sc = SSLContext.getInstance("SSL");
		} catch (NoSuchAlgorithmException e) {
			// TODO Auto-generated catch block
			_curExcept = e;
			e.printStackTrace();
			return null;
		} catch(Exception e){
			_curExcept = e;
			e.printStackTrace();
			return null;
		}
		
		TrustManager t = new X509TrustManager() {

			@Override
			public void checkClientTrusted(X509Certificate[] chain, String authType) throws CertificateException {
				// TODO Auto-generated method stub
				
			}

			@Override
			public void checkServerTrusted(X509Certificate[] chain, String authType) throws CertificateException {
				// TODO Auto-generated method stub
				
			}

			@Override
			public X509Certificate[] getAcceptedIssuers() {
				// TODO Auto-generated method stub
				X509Certificate[] x509Certificates = new X509Certificate[0];
                return x509Certificates;
			}
        };
        
        KeyManager k = new X509KeyManager() {

			@Override
			public String chooseClientAlias(String[] keyType, Principal[] issuers, Socket socket) {
				// TODO Auto-generated method stub
				return null;
			}

			@Override
			public String chooseServerAlias(String keyType, Principal[] issuers, Socket socket) {
				// TODO Auto-generated method stub
				return null;
			}

			@Override
			public X509Certificate[] getCertificateChain(String alias) {
				// TODO Auto-generated method stub
				return null;
			}

			@Override
			public String[] getClientAliases(String keyType, Principal[] issuers) {
				// TODO Auto-generated method stub
				return null;
			}

			@Override
			public String[] getServerAliases(String keyType, Principal[] issuers) {
				// TODO Auto-generated method stub
				return null;
			}

			@Override
			public PrivateKey getPrivateKey(String alias) {
				// TODO Auto-generated method stub
				return null;
			}
        	
        };
        
        _curExcept = null;
        try {
			sc.init(new KeyManager[]{k}, new TrustManager[]{t}, new SecureRandom());
		} catch (KeyManagementException e) {
			// TODO Auto-generated catch block
			_curExcept = e;
			e.printStackTrace();
			return null;
		} catch(Exception e){
			_curExcept = e;
			e.printStackTrace();
			return null;
		}
        
        OkHttpClient client = null;
        _curExcept = null;
        try{
        	long timeout = commInfo.getTimeout()==0?30:commInfo.getTimeout();
        	client = httpClient().newBuilder().sslSocketFactory(sc.getSocketFactory(), (X509TrustManager) t)
        				.hostnameVerifier(new HostnameVerifier() {
        					@Override
        					public boolean verify(String hostname, SSLSession session) {
        						return true;
        					}
        				})
        				.cookieJar(new CookieJar() {

							@Override
							public List<Cookie> loadForRequest(HttpUrl arg0) {
								// TODO Auto-generated method stub
								ArrayList<Cookie> lst = new ArrayList<Cookie>();
							
								Cookie.Builder b = new Cookie.Builder();
								b.name(CommConstant.COOKIE_KEY);
								b.value(commInfo.getCookie());
								b.domain("domain=" + CommCtrl.getDomain());
								Cookie c = b.build();
								lst.add(c);
								return lst;
							}

							@Override
							public void saveFromResponse(HttpUrl arg0, List<Cookie> arg1) {
								// TODO Auto-generated method stub
								
							}
                            
                        })
        				.connectTimeout(timeout, TimeUnit.SECONDS)
        				.writeTimeout(timeout, TimeUnit.SECONDS)
        				.readTimeout(timeout, TimeUnit.SECONDS)
        				.build();
        } catch( Exception e )
        {
        	_curExcept = e;
        	e.printStackTrace();
        	return null;
        }
        
        return client;
	}
}