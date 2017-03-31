//
//  CommCtrl.swift
//  tiger
//
//  Created by 童益丰 on 16/9/26.
//  Copyright © 2016年 kkd. All rights reserved.
//

import Foundation
import UIKit

class CommCtrl
{
    static var _httpHead = "http://";
    static var _url : String?;
    static var _started = false;
    static var _uiThread : CommThread?;
    static var _workThread : CommThread?;
    static var _silenceThread : CommThread?;
    
    static func start()
    {
        if ( _started )
        {
            return ;
        }
        
        _started = true;
        
        initialize();
        
        let uiNum : Int = Utils.getConfigInt("ui_thread");
        let workNum : Int = Utils.getConfigInt("work_thread");
        let silenceNum : Int = Utils.getConfigInt("silence_thread");
        
        _uiThread = CommThread.create(uiNum);
        _workThread = CommThread.create(workNum);
        _silenceThread = CommThread.create(silenceNum);
    }
    
    static func initialize()
    {
        let ssl : Int = Utils.getConfigInt("ssl");
        if ( 0 != ssl )
        {
            _httpHead = "https://";
        }
        else
        {
            _httpHead = "http://";
        }
        var port : String = Utils.getConfigString("port");
        if ( port.characters.count == 0 )
        {
            if ( 0 == ssl )
            {
                port = "80";
            }
            else
            {
                port = "443";
            }
        }
        _url = Utils.getConfigString("url") + ":" + port;
    }
    
    static func makeUrl( _ path : String ) -> (String)
    {
        return ( _httpHead + _url! + "/" + path + "?clientType=902&__VIEW__=json" );
    }
    
    static func send( _ url : String,
                      param : NSDictionary,
                      callback : @escaping ( _ code : Int, _ msg : String?, _ err : CommError? ) -> (),
                      type : COMM_TYPE,
                      view : UIView?,
                      loading : String?,
                      needErr : Bool,
                      exParam : NSDictionary?,
                      userData : String? ) -> (CommInfo)
    {
        var tmp : String = url;
        let arr : NSArray = param.allKeys as NSArray;
        for k in arr
        {
            let v : String = (param.object(forKey: k) as? String)!;
            tmp += ("&" + (k as! String) + "=" + v );
        }
        
        return doSend(tmp, callback: callback, type: type, view: view, loading: loading, needErr: needErr, param: exParam, userData: userData);
    }
    
    static func doSend( _ url : String,
                        callback : @escaping ( _ code : Int, _ msg : String?, _ err :  CommError? ) -> (),
                        type : COMM_TYPE,
                        view : UIView?,
                        loading : String?,
                        needErr : Bool,
                        param : NSDictionary?,
                        userData : String? ) -> (CommInfo)
    {
        start();
        let info : CommInfo = CommInfo { (code, data, err, sn, response, userData) in
            
            var api:ApiString?
            
            if url.contains(ApiString.login.rawValue){
                api = ApiString.login
            }else if url.contains(ApiString.menu.rawValue){
                api = ApiString.menu
            }else if url.contains(ApiString.pcsitetraffic.rawValue){
                api = ApiString.pcsitetraffic
            }else if url.contains(ApiString.umengdata.rawValue){
                api = ApiString.umengdata
            }else if url.contains(ApiString.regtoinvestmenttrans.rawValue){
                api = ApiString.regtoinvestmenttrans
            }else if url.contains(ApiString.regtoinvestmenttransrate.rawValue){
                api = ApiString.regtoinvestmenttransrate
            }else if url.contains(ApiString.newfinancial.rawValue){
                api = ApiString.newfinancial
            }else if url.contains(ApiString.newlicaiamount.rawValue){
                api = ApiString.newlicaiamount
            }else if url.contains(ApiString.newfinancialavg.rawValue){
                api = ApiString.newfinancialavg
            }else if url.contains(ApiString.oldandnewfinancial.rawValue){
                api = ApiString.oldandnewfinancial
            }else if url.contains(ApiString.oldandnewfinancialamount.rawValue){
                api = ApiString.oldandnewfinancialamount
            }else if url.contains(ApiString.oldandnewfinancialavg.rawValue){
                api = ApiString.oldandnewfinancialavg
            }else if url.contains(ApiString.fundsdata.rawValue){
                api = ApiString.fundsdata
            }else if url.contains(ApiString.retaineddata.rawValue){
                api = ApiString.retaineddata
            }else if url.contains(ApiString.compounddata.rawValue){
                api = ApiString.compounddata
            }else if url.contains(ApiString.compoundrate.rawValue){
                api = ApiString.compoundrate
            }else{
                api = ApiString.empte
            }
            
            CommParser.parse(code, data: data, err: err, type: type, needErr: needErr, callback: callback, sn: sn, userData: userData, api:api! );
        };
        info.setUserData(userData);
        info.setUrl(url);
        
        if ( nil != param )
        {
            var s : String? = param?.object(forKey: "timeout") as? String;
            if ( nil != s )
            {
                info.setTimeout(Int(s!)!);
            }
            
            s = param?.object(forKey: "retry") as? String;
            if ( nil != s )
            {
                info.setRetry(Int(s!)!);
            }
            
            s = param?.object(forKey: "timer") as? String;
            if ( nil != s )
            {
                info.setTimer(Int(s!)!);
            }
        }
        info.setType(type);
        
        if ( 0 == info.getTimer() )
        {
            addComm(info);
        }
        
        return info;
    }
    
    static func addComm( _ info : CommInfo )
    {
        info.makeRequest();
        switch( info.getType() )
        {
        case COMM_TYPE.ui:
            _uiThread?.addCommInfo(info);
        case COMM_TYPE.work:
            _workThread?.addCommInfo(info);
        case COMM_TYPE.bg:
            _silenceThread?.addCommInfo(info);
        }
    }
}
