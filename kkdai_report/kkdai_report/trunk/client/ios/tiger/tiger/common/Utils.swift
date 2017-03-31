//
//  Utils.swift
//  tiger
//
//  Created by 童益丰 on 16/9/26.
//  Copyright © 2016年 kkd. All rights reserved.
//

import Foundation
import UIKit


class Utilsww {
    let skasn = "djksnkc"
}


class Utils
{
    static func getConfigString( _ key : String ) -> String
    {
        return getConfigString(key, strFile: "config");
    }
    
    static func getConfigString( _ key : String, strFile : String ) -> String
    {
        let path : String = Bundle.main.path(forResource: strFile, ofType: nil)!;
        do
        {
            let data : String? = try String(contentsOfFile: path, encoding: String.Encoding.utf8);
            let arr : NSArray? = data?.components(separatedBy: "\n") as NSArray?;
            for line in arr!
            {
                let s : String = trim(line as! String);
                let arrField : Array = s.components(separatedBy: ":");
                if ( arrField.count >= 2 )
                {
                    let k : String = arrField[0] ;
                    if ( k == key )
                    {
                        return arrField[1] ;
                    }
                }
            }
        }
        catch( _ as NSError )
        {
            
        }
        return "";
    }
    
    static func getConfigInt( _ key : String ) -> Int
    {
        let s : String = Utils.getConfigString(key);
        if ( s.characters.count > 0 )
        {
            return Int(s)!;
        }
        
        return 0;
    }
    static func getConfigInt( _ key : String, path : String ) -> Int
    {
        let s : String = Utils.getConfigString(key, strFile: path);
        if ( s.characters.count > 0 )
        {
            return Int(s)!;
        }
        
        return 0;
    }
    
    static func trim( _ s : String ) -> String
    {
        var tmp : String = s;
        while( tmp.characters.count > 0 )
        {
            let t : String = tmp.substring( to: tmp.startIndex );
            if ( t == " " || t == "   " || t == "\r"
                || t == "\n" || t == ";" )
            {
                tmp.remove(at: tmp.startIndex);
            }
            else
            {
                break;
            }
        }
        
        while( tmp.characters.count > 0 )
        {
            let t : String = tmp.substring( from: tmp.endIndex );
            if ( t == " " || t == "   " || t == "\r"
                || t == "\n" || t == ";" )
            {
                tmp.remove(at: tmp.endIndex);
            }
            else
            {
                break;
            }
        }
        
        return tmp;
    }
    
    /**
     判断当前设备是否有用户登录
     
     - returns: true 有用户 flase 没有用户登录
     */
    static func getUserLogin() -> Bool{
        let login:Bool  = UserDefaults.standard.bool(forKey: UserLogin) as Bool!
        if login {
            return true
        }else{
            return false
        }
        
    }
    /**
     设置用户的状态
     
     - parameter login: 传入登陆状态 bool型
     */
    static func setUserLogin(login:Bool){
        UserDefaults.standard.set(login, forKey: UserLogin)
    }
    
    //消失弹窗
    static func alertMessage(message:String) {
        if message.isEmpty{ return }
        let view = UIView (frame: ScreenFrame)
        UIApplication.shared.keyWindow?.rootViewController?.view.addSubview(view)
        view.backgroundColor = UIColor.clear
        
        let wid: CGFloat = 160
        
        let msgView = UIView ()
        msgView.layer.masksToBounds = true
        msgView.layer.cornerRadius = 4.0
        msgView.backgroundColor = UIColor.black
        view.addSubview(msgView)
        
        let label = UILabel ()
        label.backgroundColor = UIColor.clear
        label.text = message
        label.numberOfLines = 0
        label.textAlignment = .center
        label.font = UIFont.systemFont(ofSize: 12.0)
        label.textColor = UIColor.white
        label.lineBreakMode = .byWordWrapping
        let size = label.sizeThatFits(CGSize (width: wid, height: CGFloat(MAXFLOAT)))
        label.frame = CGRect (x: 0, y: 0, width: wid, height: size.height)
        msgView.addSubview(label)
        
        let diff:CGFloat = 20
        
        msgView.frame = CGRect (x: 0, y: 0, width: size.width + diff, height: size.height + diff)
        
        label.center = CGPoint (x: msgView.frame.size.width/2, y: msgView.frame.size.height/2)
        
        msgView.center = CGPoint (x: ScreenWid/2, y: ScreenHei/2)
        
        UIView.animate(withDuration: 2.2, animations: {() -> Void in
            view.alpha = 0.0
        }) { (finished:Bool) -> Void  in
            if finished{
                view.removeFromSuperview()
            }
        }
    }
    
    static func getTime() -> (startTime:String,endTime:String) {
        let endDate:Date = Date (timeIntervalSinceNow:  -24*60*60)
        
        let startDate:Date = Date (timeInterval: -6*24*60*60, since: endDate)
        
        let formatter = DateFormatter()
        //日期样式
        formatter.dateFormat = "yyyy-MM-dd"
        
        return (formatter.string(from: startDate),formatter.string(from: endDate))
    }
    
    static func getArrayString(data:String) -> [String]{
        do {
            return try JSONSerialization.jsonObject(with: data.data(using: .utf8)!, options: []) as! [String]
        } catch  {
            return []
        }
    }
    
    static func getDict(data:String) -> NSDictionary{
        do {
            return try JSONSerialization.jsonObject(with: data.data(using: .utf8)!, options: []) as! NSDictionary
        } catch  {
            return NSDictionary()
        }
    }
}
