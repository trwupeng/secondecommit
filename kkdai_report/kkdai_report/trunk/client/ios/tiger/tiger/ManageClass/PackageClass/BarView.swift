//
//  BarView.swift
//  tiger
//
//  Created by wayne on 16/10/13.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class BarView: UIView {
    var scrollerView:UIScrollView!
    
    var annotationData:[String]!
    var categoryData:[String]!
    var data:NSDictionary!
    
    var maxNum:Int = 0
    var horizontalNum:Int = 0 //横向 数量
    var verticalNum:Int = 0 //纵向 数量
    
    
    
    
    init(frame: CGRect,annotationList:[String],categoryList:[String],dataSource:NSDictionary) {
        super.init(frame: frame)
        annotationData = annotationList
        categoryData = categoryList
        data = dataSource
        creatUI()
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    private func creatUI() {
        scrollerView = UIScrollView (frame: CGRect (x: 0, y: 0, width: self.frame.width, height: self.frame.height))
        scrollerView.showsVerticalScrollIndicator = false
        scrollerView.showsHorizontalScrollIndicator = false
        self.addSubview(scrollerView)
    }
    
    

}
