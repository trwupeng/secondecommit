//
//  MenuManage.swift
//  tiger
//
//  Created by wayne on 16/10/10.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class MenuManage{

    static let share = MenuManage()
    
    var children = [ChildrenOne]()
    
    func param(dict: NSDictionary) {
        let childenArray = (dict["menus"] as! NSDictionary)["children"] as! [NSDictionary]
        
        for item in childenArray {
            let childOne = ChildrenOne()
            childOne.param(dict: item)
            children.append(childOne)
        }
    }
    
}
