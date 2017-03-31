//
//  ChildrenOne.swift
//  tiger
//
//  Created by wayne on 16/10/10.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class ChildrenOne: NSObject {
     var capt = ""
     var url = ""
     var childer = [ChildrenTwo]()
    
    func param(dict:NSDictionary) {
        
        self.capt = dict["capt"] as! String
        let nul = NSNull()
        
        if ((dict["url"] as? String) != nil){
            self.url = dict["url"] as! String
        }
        
        
        
        let childTwoArr = dict["children"] as! [NSDictionary]
        
        for item in childTwoArr {
            let childTwo = ChildrenTwo()
            childTwo.param(dict: item)
            childer.append(childTwo)
        }
    }
}
