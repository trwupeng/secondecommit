package com.kuaikuaidai.kkdaireport.comm;

import android.os.Handler;
import android.os.Message;

import java.util.ArrayList;
import java.util.concurrent.locks.ReadWriteLock;
import java.util.concurrent.locks.ReentrantReadWriteLock;

class TimerInfo {
	public long interval;
	public long nextTick;
	public int times;
	public String name;
	public Object userData;
	public TimerInterface callback;
}

class OrderList {
	ReadWriteLock _locker;
	long _firstTick;
	long _lastTick;
	long _min;
	long _max;
	ArrayList< TimerInfo > _lst;
	
	public OrderList() {
		_locker = new ReentrantReadWriteLock();
		_firstTick = 0;
		_lastTick = 0;
		_min = 0;
		_max = 0;
		_lst = new ArrayList< TimerInfo >();
	}
	
	boolean isContain( long interval ) {
		if ( interval < 0 ) {
			interval = 0;
		}
		return ( interval >= _min && interval <= _max );
	}
	
	boolean add( TimerInfo info ) {
		boolean insert = false;
		_locker.writeLock().lock();
		for ( int i=0; i<_lst.size(); ++i ) {
			if ( _lst.get(i).nextTick > info.nextTick ) {
				_lst.add(i, info);
				insert = true;
				break;
			}
		}
		if ( !insert ) {
			_lst.add(info);
		}
		_firstTick = _lst.get(0).nextTick;
		_lastTick = _lst.get(_lst.size()-1).nextTick;
		_locker.writeLock().unlock();
		return true;
	}
	
	boolean del( String name ) {
		_locker.writeLock().lock();
		for ( int i=0; i<_lst.size(); ++i ) {
			if ( _lst.get(i).name.equals(name) ) {
				_lst.remove(i);
				_locker.writeLock().unlock();
				return true;
			}
		}
		_locker.writeLock().unlock();
		return false;
	}
	
	TimerInfo get( long tick ) {
		return get( tick, 0 );
	}
	TimerInfo get( long tick, long interval ) {
		if ( 0 == interval ) {
			interval = _min;
		}
		TimerInfo info = null;
		_locker.writeLock().lock();
		if ( _lst.isEmpty() ) {
			_locker.writeLock().unlock();
			return null;
		}
		info = _lst.get(0);
		if ( info.nextTick > (tick-interval) ) {
			info = null;
		}
		else {
			_lst.remove(0);
			if ( _lst.isEmpty() ) {
				_firstTick = 0;
				_lastTick = 0;
			}
			else {
				_firstTick = _lst.get(0).nextTick;
				_lastTick = _lst.get(_lst.size()-1).nextTick;
			}
		}
		_locker.writeLock().unlock();
		return info;
	}
	
	boolean hasData() {
		return !_lst.isEmpty();
	}
	
	void setMin( long min ) {
		_min = min;
	}
	void setMax( long max ) {
		_max = max;
	}
}

public class TimerCtrl {
	static ArrayList< OrderList > _vInfo = null;
	static Handler _handler = null;
	
	public static void init() {
		if ( null != _vInfo ) {
			return ;
		}
		initArray();
		startThread();
	}
	
	public static void addTimer( String name, long interval, TimerInterface callback ) {
		addTimer( name, interval, callback, null );
	}
	public static void addTimer( String name, long interval, TimerInterface callback, Object userData ) {
		addTimer( name, interval, callback, userData, false );
	}
	public static void addTimer( String name, long interval, TimerInterface callback, Object userData, boolean startCall ) {
		addTimer( name, interval, callback, userData, startCall, 0 );
	}
	public static void addTimer( String name, long interval, TimerInterface callback, Object userData, boolean startCall, int times ) {
		if ( null == name || null == callback ) {
			return ;
		}
		init();
		
		long tick = System.currentTimeMillis();
		TimerInfo info = new TimerInfo();
		info.interval = interval;
		info.nextTick = tick + interval;
		info.name = name;
		info.userData = userData;
		info.callback = callback;
		
		if ( times <= 1 && startCall ) {
			call( info );
			return ;
		}
		
		if ( startCall ) {
			--times;
		}
		info.times = times;
		doAdd( info, tick );
		if ( startCall ) {
			call( info );
		}
	}
	
	static void call( TimerInfo info )
	{
		Message msg = new Message();
		msg.obj = info;
		_handler.sendMessage(msg);
	}
	
	public static void delTimer( String name ) {
		if ( null == name ) {
			return ;
		}
		init();
		
		for ( int i=0; i<_vInfo.size(); ++i ) {
			if ( _vInfo.get(i).del(name) ) {
				break;
			}
		}
	}
	
	static void addNext( TimerInfo info, long tick ) {
		while( true ) {
			info.nextTick = tick + info.interval;
			if ( info.nextTick <= tick ) {
				call( info );
				continue;
			}
			break;
		}
		doAdd( info, tick );
	}
	
	static void doAdd( TimerInfo info, long tick ) {
		for ( int i=0; i<_vInfo.size(); ++i ) {
			if ( _vInfo.get(i).isContain(info.nextTick-tick) ) {
				_vInfo.get(i).add(info);
				break;
			}
		}
	}
	
	static void startThread() {
		_handler = new Handler() {
			public void handleMessage(Message msg) { 
				TimerInfo info = (TimerInfo)msg.obj;
				info.callback.onCallback(info.name, info.userData);
			}
		};
		
		TimerThreadEntry p = new TimerThreadEntry();
		p.setInfo(_vInfo);
		new Thread(p).start();
	}
	
	static void initArray() {
		_vInfo = new ArrayList< OrderList >();
		{
	        //0-1秒
	        OrderList l = new OrderList();
	        l.setMin(0);
	        l.setMax(1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //1-5秒
	        OrderList l = new OrderList();
	        l.setMin(1000);
	        l.setMax(5000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //5-10秒
	        OrderList l = new OrderList();
	        l.setMin(5000);
	        l.setMax(10*1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //10-60秒
	        OrderList l = new OrderList();
	        l.setMin(10*1000);
	        l.setMax(60*1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //1-5分钟
	        OrderList l = new OrderList();
	        l.setMin(60*1000);
	        l.setMax(5*60*1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //5-10分钟
	        OrderList l = new OrderList();
	        l.setMin(5*60*1000);
	        l.setMax(10*60*1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //10-60分钟
	        OrderList l = new OrderList();
	        l.setMin(10*60*1000);
	        l.setMax(60*60*1000);
	        _vInfo.add(l);
	    }
	    
	    {
	        //1小时以上
	        OrderList l = new OrderList();
	        l.setMin(60*60*1000);
	        l.setMax(365*24*60*60*1000);
	        _vInfo.add(l);
	    }
	}
}

class TimerThreadEntry implements Runnable {
	private ArrayList< OrderList > _vInfo = null;
	
	void setInfo( ArrayList< OrderList > info ) {
		_vInfo = info;
	}
	@Override
	public void run() {
		while( true ) {
			try {
				Thread.sleep(1);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				Thread.interrupted();
				break;
			} catch( Exception e ) {
				break;
			}
			
			long tick = System.currentTimeMillis();
			TimerInfo info = _vInfo.get(0).get(tick);
			if ( null != info ) {
				TimerCtrl.call(info);
				if ( info.times > 0 ) {
					--info.times;
					if ( 0 != info.times ) {
						TimerCtrl.addNext(info, tick);
					}
				} else {
					TimerCtrl.addNext(info, tick);
				}
			} else {
				for ( int i=1; i<_vInfo.size(); ++i ) {
					while( true ) {
						TimerInfo info2 = _vInfo.get(i).get(tick);
						if ( null == info2 ) {
							break;
						}
						
						TimerCtrl.doAdd( info2, tick );
					}
				}
			}
		}
	}
}
