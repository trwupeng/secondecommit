//
//  AppTrafficManage.swift
//  tiger
//
//  Created by wayne on 16/10/31.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class AppTrafficManage {
    
    static let share = AppTrafficManage()

    var labelArray = [String]()
    var dataDict = NSMutableDictionary()
    var channel = [String]()
    var max = NSDictionary()
    
    
    
    let formatterArray = ["激活用户","活跃用户","启动次数"]
    let numKeys = ["901","902"]
    let dictKeys = ["new_user","active_user","launches_user"]
    
    
    func param(dict: NSDictionary) {
        
        let dtime = dict["dtime"] as! String
        let dtime1 = dict["dtime1"] as! String
        channel.removeAll()
        channel = dict["channel"] as! [String]
        max = dict["max"] as! NSDictionary
        
        labelArray.removeAll()
        labelArray = [dtime.trimmingCharacters(in: NSCharacterSet (charactersIn:"\"") as CharacterSet),dtime1.trimmingCharacters(in: NSCharacterSet (charactersIn:"\"") as CharacterSet)]
        
        let remKeys = ["rem","rem1"]
        
        var array1 = [Double]()
        var array2 = [Double]()
        
        for remKey in remKeys {
            let remString = dict.object(forKey: remKey) as? String
            let remDict = Utils.getDict(data: remString!)
            
            for label in labelArray {
                
                let labelData = remDict.object(forKey: label) as? NSDictionary
                for numKey in numKeys {
                    
                    if let numDict = labelData?.object(forKey: numKey) as? NSDictionary {
                        for dictKey in dictKeys {
                            if let data = numDict.object(forKey: dictKey) as? Double{
                                if remKey == "rem"{
                                    array1.append(data)
                                }else{
                                    array2.append(data)
                                }
                            }
                            
                        }
                    }
                }
            }
        }
        
        var arr1:[Double] = [0,0,0]
        var arr2:[Double] = [0,0,0]
        
        for i in 0..<array1.count{
            let j = i%3
            
            switch j {
            case 0:
                arr1[j] += array1[i]
                arr2[j] += array2[i]
            case 1:
                arr1[j] += array1[i]
                arr2[j] += array2[i]
            case 2:
                arr1[j] += array1[i]
                arr2[j] += array2[i]
            default:
                break
            }
            
        }
        
        dataDict.removeAllObjects()
        dataDict.setValue(arr1, forKey: labelArray[0])
        dataDict.setValue(arr2, forKey: labelArray[1])
                
    }
}
