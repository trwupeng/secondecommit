//
//  AnnotationView.swift
//  tiger
//
//  Created by wayne on 16/10/13.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

let annotationViewHeight:CGFloat = 12


class AnnotationView: UIView {

    init(frame: CGRect,list:[String]) {
        super.init(frame: frame)
        creatUI(list: list)
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    
    private func creatUI(list:[String]){
        
        self.frame = CGRect (x: self.frame.midX, y: self.frame.midY, width: ScreenWid, height: annotationViewHeight)
        
        var widArray = [CGFloat]()
        let label = UILabel ()
        label.numberOfLines = 0
        label.font = UIFont.systemFont(ofSize: annotationViewHeight)
        
        var labelWidAll:CGFloat = 0
        
        for obj in list {
            label.text = obj
            let size = label.sizeThatFits(CGSize (width: CGFloat(MAXFLOAT), height: annotationViewHeight))
            widArray.append(size.width)
            labelWidAll += size.width
        }
        
        let diff1:CGFloat = 2
        let diff2:CGFloat = 5
        let colorWid:CGFloat = annotationViewHeight * 1.5
        
        let view = UIView (frame: CGRect (x: 0, y: 0, width: labelWidAll + CGFloat(list.count)*(diff1+colorWid) + CGFloat(list.count - 1)*diff2, height: annotationViewHeight))
        view.center = CGPoint (x: ScreenWid/2, y: annotationViewHeight/2)
        self.addSubview(view)
        
        for i in 0..<list.count {
            
            var X:CGFloat = 0
            
            if i == 0{
                X = 0
            }else{
                X = (colorWid+diff1+diff2)*CGFloat(i)
                
                for j in 0..<i {
                    X += widArray[j]
                }
            }
            let colorView = UIView (frame: CGRect (x: X, y: 0, width: colorWid, height: annotationViewHeight))
            colorView.backgroundColor = colorArray[i]
            colorView.layer.masksToBounds = true
            colorView.layer.cornerRadius = 4
            view.addSubview(colorView)
            
            let textLabel = UILabel (frame: CGRect (x: colorView.frame.maxX+diff1, y: 0, width: widArray[i], height: annotationViewHeight))
            textLabel.font = UIFont.systemFont(ofSize: annotationViewHeight)
            textLabel.textColor = UIColor.black
            textLabel.text = list[i]
            view.addSubview(textLabel)
        }
    }

}
