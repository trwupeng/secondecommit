//
//  CookiesManager.swift
//  tiger
//
//  Created by 童益丰 on 16/9/26.
//  Copyright © 2016年 kkd. All rights reserved.
//

import Foundation
fileprivate func < <T : Comparable>(lhs: T?, rhs: T?) -> Bool {
  switch (lhs, rhs) {
  case let (l?, r?):
    return l < r
  case (nil, _?):
    return true
  default:
    return false
  }
}

fileprivate func > <T : Comparable>(lhs: T?, rhs: T?) -> Bool {
  switch (lhs, rhs) {
  case let (l?, r?):
    return l > r
  default:
    return rhs < lhs
  }
}


class CookieInfo
{
    var _key : String?;
    var _value : String?;
    var _domain : String?;
    var _path : String?;
    var _expired : String?;
    var _data : NSMutableDictionary?;
    
    static func create( _ cookie : String ) -> CookieInfo
    {
        let p : CookieInfo = CookieInfo();
        p.parse(cookie);
        return p;
    }
    
    func isValid() -> Bool
    {
        if ( _key?.characters.count == 0 || _value?.characters.count == 0 )
        {
            return false;
        }
        if ( _expired?.characters.count == 0 )
        {
            return true;
        }
        let formatter : DateFormatter = DateFormatter();
        formatter.dateFormat = "EEE, dd-MMM-yyyy HH:mm:ss";
        let dateExpired : Date = formatter.date(from: _expired!)!;
        let dateCur = Date();
        return (dateExpired.timeIntervalSince1970 > dateCur.timeIntervalSince1970);
    }
    
    func get() -> String
    {
        if ( !isValid() )
        {
            return "";
        }
        var s : String = _key! + "=" + _value!;
        let keys : NSArray = (_data?.allKeys)! as NSArray;
        for k in keys
        {
            let v : String = (_data?.object(forKey: k))! as! String;
            s += ( ";" + (k as! String) + "=" + v );
        }
        return s;
    }
    
    func serial() -> String
    {
        if ( !isValid() )
        {
            return "";
        }
        
        if ( _expired?.characters.count == 0 )
        {
            return "";
        }
        
        var s : String = get();
        
        if ( _domain?.characters.count > 0 )
        {
            s += ( ";" + "domain=" + _domain! );
        }
        
        if ( _path?.characters.count > 0 )
        {
            s += ( ";" + "path=" + _path! );
        }
        
        if ( _expired?.characters.count > 0 )
        {
            s += ( ";" + "expired=" + _expired! );
        }
        
        return s;
    }
    
    func parse( _ cookie : String )
    {
        _data = NSMutableDictionary();
        let arr : Array = cookie.components(separatedBy: ";");
        var index : Int = 0;
        for line in arr
        {
            index += 1;
            let s = Utils.trim(line );
            parseOne(s, index: index);
        }
    }
    
    func parseOne( _ line : String, index : Int ) ->  Bool
    {
        let arr : Array = line.components(separatedBy: "=");
        if ( arr.count < 2 )
        {
            return false;
        }
        
        let key = Utils.trim( arr[0] );
        let value = Utils.trim( arr[1] );
        if ( 0 == index )
        {
            _key = key;
            _value = value;
            return true;
        }
        
        if ( key.lowercased() == "domain" )
        {
            _domain = value;
        }
        else if ( key.lowercased() == "path" )
        {
            _path = value;
        }
        else if ( key.lowercased() == "expired" )
        {
            _expired = value;
        }
        else
        {
            _data?.setValue(_value, forKey: _key!);
        }
        
        return true;
    }
    
}

class CookiesManager
{
    static var _cookies : NSMutableArray?;
    
    static func parse( _ res : HTTPURLResponse )
    {
        _cookies = NSMutableArray();
        let dic : NSDictionary = res.allHeaderFields as NSDictionary;
        let keys : NSArray = dic.allKeys as NSArray;
        for k in keys
        {
            let v = dic.object(forKey: k) as! String;
            if ( (k as AnyObject).lowercased == "Set-Cookie" )
            {
                _cookies?.add(CookieInfo.create(v));
            }
        }
        
    }
    
    static func get() -> String
    {
        var s : String = String();
        for info in _cookies!
        {
            let cookieInfo = (info as! CookieInfo);
            if ( cookieInfo.isValid() )
            {
                s += cookieInfo.get();
                s += ";";
            }
        }
        
        return s;
    }
    
    static func save()
    {
        let paths : NSArray = NSSearchPathForDirectoriesInDomains(FileManager.SearchPathDirectory.cachesDirectory,
                                                                  FileManager.SearchPathDomainMask.userDomainMask, true) as NSArray;
        doSave(paths[0] as! String);
    }
    
    static func doSave( _ path : String )
    {
        var s : String = String();
        for info in _cookies!
        {
            let cookieInfo = (info as! CookieInfo);
            if ( cookieInfo.isValid() )
            {
                let tmp = cookieInfo.serial();
                if ( tmp.characters.count > 0 )
                {
                    if ( s.characters.count > 0 )
                    {
                        s += "*";
                    }
                    s += tmp;
                }
            }
        }
        do
        {
            try s.write(toFile: path, atomically: true, encoding: String.Encoding.utf8);
        }
        catch ( _ as NSError )
        {
            
        }
    }
    
    static func load()
    {
        let paths : NSArray = NSSearchPathForDirectoriesInDomains(FileManager.SearchPathDirectory.cachesDirectory,
                                                                  FileManager.SearchPathDomainMask.userDomainMask, true) as NSArray;
        doLoad(paths[0] as! String);
    }
    
    static func doLoad( _ path : String )
    {
        _cookies = NSMutableArray();
        do
        {
            let s : String = try String.init(contentsOfFile: path, encoding: String.Encoding.utf8);
            let arr : Array = s.components(separatedBy: "*");
            for line in arr
            {
                _cookies?.add(CookieInfo.create(line ));
            }
        }
        catch( _ as NSError )
        {
            
        }
    }
}
