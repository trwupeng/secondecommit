//
//  PcsitetrafficManage.swift
//  tiger
//
//  Created by wayne on 16/10/27.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class PcsitetrafficManage{
    
    static let share = PcsitetrafficManage()
    
    var labelArray = [String]()
    var formatterArray = [String]()
    var rsDict = NSDictionary()
    
    
    func param(dict: NSDictionary) {
        
        let msg = dict["errMsg"]
        
        if msg == nil {
            let category = dict["category"] as! String
            let rs = dict["rs"] as! String
            let legendData = dict["legendData"] as! String
            
            formatterArray = Utils.getArrayString(data: category)
            labelArray = Utils.getArrayString(data: legendData)
            rsDict = Utils.getDict(data: rs)
        }
        
    }

}
