//
//  ChildrenTwo.swift
//  tiger
//
//  Created by wayne on 16/10/10.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class ChildrenTwo: NSObject {

    var capt = ""
    var url = ""
    
    func param(dict:NSDictionary) {
        self.capt = dict["capt"] as! String
        
        if ((dict["url"] as? String) != nil){
            self.url = dict["url"] as! String
        }
    }
    
}
