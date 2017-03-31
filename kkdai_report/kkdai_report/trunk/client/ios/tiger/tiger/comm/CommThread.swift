//
//  CommThread.swift
//  tiger
//
//  Created by 童益丰 on 16/9/22.
//  Copyright © 2016年 kkd. All rights reserved.
//

import Foundation

enum COMM_TYPE : Int
{
    case ui = 1
    case work = 2
    case bg = 3
}

class CommError
{
    var _err : NSError?;
    var _hasError : Bool?;
    
    init( err : NSError? )
    {
        _err = err;
        if ( nil != _err )
        {
            _hasError = true;
        }
        else
        {
            _hasError = false;
        }
    }
    
    func getError() -> NSError?
    {
        return _err!;
    }
    
    func hasError() -> Bool
    {
        return _hasError!;
    }
}

class CommInfo
{
    var _request : NSMutableURLRequest?;
    var _url : String = "";
    var _interval : Int = 0;
    var _callback : (_ code : Int, _ data : Data?, _ err : CommError?, _ sn : Int, _ response : HTTPURLResponse?, _ userData : String? ) -> ()?;
    var _sn : Int = 0;
    var _retry : Int = 0;
    var _userData : String = "";
    var _timer : Int = 0;
    var _type : COMM_TYPE = COMM_TYPE.ui;
    
    init( callback : @escaping (_ code : Int, _ data : Data?, _ err : CommError?, _ sn : Int, _ response : HTTPURLResponse?, _ userData : String? ) -> () )
    {
        self._callback = callback;
    }
    
    
    func makeRequest()
    {
        if ( self._interval > 0 )
        {
            self._request?.timeoutInterval = TimeInterval(self._interval);
        }
        else
        {
            self._request?.timeoutInterval = 30;
        }
        let s : String = CookiesManager.get();
        if ( s.characters.count > 0 )
        {
            self._request?.addValue(s, forHTTPHeaderField: "Cookie" );
        }
    }
    func getRequest() -> URLRequest
    {
        return self._request! as URLRequest;
    }
    
    func setCallback( _ callback : @escaping (_ code : Int, _ data : Data?, _ err : CommError?, _ sn : Int, _ response : HTTPURLResponse?, _ userData : String? ) -> () )
    {
        self._callback = callback;
    }
    
    func getCallback() -> ((_ code : Int, _ data : Data?, _ err : CommError?, _ sn : Int, _ response : HTTPURLResponse?, _ userData : String? ) -> ()?)
    {
        return self._callback;
    }
    
    func getSN() -> (Int)
    {
        return self._sn;
    }
    
    func setUserData( _ data : String? )
    {
        if ( nil == data )
        {
            self._userData = "";
        }
        else
        {
            self._userData = data!;
        }
    }
    func getUserData() -> (String)
    {
        return self._userData;
    }
    
    func setRetry( _ retry : Int )
    {
        self._retry = retry;
    }
    func getRetry() -> (Int)
    {
        return self._retry;
    }
    
    func setUrl( _ url : String )
    {
        self._url = url.addingPercentEscapes(using: String.Encoding.utf8)!;
        self._request = NSMutableURLRequest(url: URL(string: self._url)!);
    }
    func getUrl() -> (String)
    {
        return self._url;
    }
    
    func setTimeout( _ i : Int )
    {
        self._interval = i;
    }
    func getTimeout() -> (Int)
    {
        return self._interval;
    }
    
    func setTimer( _ t : Int )
    {
        self._timer = t;
    }
    func getTimer() -> (Int)
    {
        return self._timer;
    }
    
    func setType( _ type : COMM_TYPE )
    {
        self._type = type;
    }
    
    func getType() -> (COMM_TYPE)
    {
        return self._type;
    }
}

class CommThread
{
    var _threads : NSMutableArray?;
    var _lock : NSLock?;
    var _arrCommInfo : NSMutableArray?;
    
    static func create( _ threadNum : Int ) -> CommThread
    {
        var n = threadNum;
        if ( n < 1 )
        {
            n = 1;
        }
        let t = CommThread();
        t.start(n);
        return t;
    }
    
    func start( _ threadNum : Int )
    {
        _lock = NSLock();
        _threads = NSMutableArray();
        _arrCommInfo = NSMutableArray();
        for _ in 0 ..< threadNum
        {
            let t = Thread.init(target: self, selector: #selector(CommThread.run), object: nil );
            t.start();
            _threads?.add(t);
        }
    }
    
    @objc func run()
    {
        while ( true )
        {
            usleep(1000*10);
            while ( true )
            {
                var p : CommInfo?;
                self._lock?.lock();
                if self._arrCommInfo?.count == 0
                {
                    self._lock?.unlock();
                    break;
                }
                p = _arrCommInfo![0] as? CommInfo;
                _arrCommInfo?.removeObject(at: 0);
                self._lock?.unlock();
                doRequest(p!);
            }
        }
    }
    
    func doRequest( _ commInfo : CommInfo )
    {
        let request : URLRequest = commInfo.getRequest();
        var res1 : URLResponse?;
        var response : HTTPURLResponse?;
        var err : NSError?;
        var data : Data?;
        do
        {
            data = try NSURLConnection.sendSynchronousRequest(request, returning: &res1);
        }
        catch ( let e as NSError )
        {
            err = e;
        }
        let commErr : CommError = CommError(err: err);
        if ( nil == err )
        {
            //无错误
            response = res1 as? HTTPURLResponse;
            let callback = commInfo.getCallback();
            DispatchQueue.main.async(execute: {
                CookiesManager.parse(response!);
                callback( (response?.statusCode)!, data, commErr, commInfo.getSN(), response, commInfo.getUserData() );
            });
        }
        else
        {
            //有错误
            var retry = commInfo.getRetry();
            if ( retry > 0 )
            {
                //需要重试
                retry -= 1;
                commInfo.setRetry(retry);
                addCommInfoHeader(commInfo);
                return ;
            }
            let callback = commInfo.getCallback();
            DispatchQueue.main.async(execute: {
                callback( 0, data, commErr, commInfo.getSN(), response, commInfo.getUserData() );
                });
        }
    }
    
    func addCommInfo( _ info : CommInfo )
    {
        _lock?.lock();
        _arrCommInfo?.add(info);
        _lock?.unlock();
    }
    
    func addCommInfoHeader( _ info : CommInfo )
    {
        _lock?.lock();
        _arrCommInfo?.insert(info, at: 0);
        _lock?.unlock();
    }
    
}
