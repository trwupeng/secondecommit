//
//  CommParser.swift
//  tiger
//
//  Created by 童益丰 on 16/9/29.
//  Copyright © 2016年 kkd. All rights reserved.
//

import Foundation
import UIKit

class CommParser
{
    static func parse(  _ httpCode : Int,
                        data : Data?,
                        err : CommError?,
                        type : COMM_TYPE,
                        needErr : Bool,
                        callback : ( _ code : Int, _ msg : String?, _ err : CommError? ) -> (),
                        sn : Int,
                        userData : String?, api:ApiString)
    {
        if ( err?.hasError() == true )
        {
            callback( -1, nil, err );
            return ;
        }
        
        if ( 200 != httpCode )
        {
            callback( httpCode,
                      (data != nil) ? String.init(data: data!, encoding: String.Encoding.utf8 ):nil,
                      err );
            return ;
        }
        
        do
        {
            if let dic = try JSONSerialization.jsonObject(with: data!, options: []) as? NSDictionary
            {
                let ret = doParse(dic, userData: userData, api: api);
                callback( ret.code, ret.msg, nil );
            }
            else
            {
                callback( -1, (data != nil) ? String.init(data: data!, encoding: String.Encoding.utf8 ):nil, nil );
            }
        }
        catch let error as NSError
        {
            callback( -1, nil, CommError(err: error));
        }
    }
    
    static func doParse( _ root : NSDictionary, userData : String?, api: ApiString) -> ( code : Int, msg : String )
    {
        let keys = root.allKeys;
        var msg : String = "";
        var code : Int = 200;
        for k in keys
        {
            let v  = root.object(forKey: k);
            let key : String = (k as AnyObject).lowercased;
            if ( "statusCode" == key || "statuscode" == key)
            {
                if ((v as? String) != nil){
                    code = Int((v as? String)!)!;
                }else{
                    code = v as! Int
                }
                
            }
            else if ( "message" == key )
            {
                msg = (v as? String)!;
            }
            
            
        }
        
        switch api {
        case .menu:
            MenuManage.share.param(dict: root)
        case .pcsitetraffic:
            let mssg = root["errMsg"]
            
            if (mssg != nil)  {
                msg = mssg as! String
            }else{
                PcsitetrafficManage.share.param(dict: root)
            }
        case .umengdata:
            
            if code == 200 {
                AppTrafficManage.share.param(dict: root)
            }
            
        default: break
        }
        
        
        
        return ( code, msg );
    }
}
