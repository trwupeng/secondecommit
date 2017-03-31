//
//  TimeView.swift
//  tiger
//
//  Created by wayne on 16/10/11.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

enum TimeStyleString:String{
    case startStyleString = "起始时间"
    case endStyleString = "结束时间"
}

let timeViewHei:CGFloat = 20

protocol TimeViewDelegate:class{
    func timeView(startTime:String, endTime:String, selectText:String)
}

class TimeView: UIView, DatePickerViewDelegate, PopupViewDelegate {
    
    weak var delegate:TimeViewDelegate?
    
    var startTime: String = ""
    var endTime: String = ""
    
    var supView: UIView!
    
    var startLabel: UILabel!
    var endLabel: UILabel!
    var selectLabel: UILabel!
    var headLabel:UILabel!
    
    
    var datePickerView: DatePickerView!
    var popupView: PopupView!
    
    
    var timeString:TimeStyleString = .startStyleString
    
    init(frame: CGRect, superView:UIView , otherSelect:Bool) {
        super.init(frame: frame)
        supView = superView
        creatUI(otherSel: otherSelect)
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    private func creatUI(otherSel:Bool)  {
        
        let btnWid:CGFloat = 60
        
        let lab1Wid:CGFloat = 88
        let lab2Wid:CGFloat = 102
        
        let labHei:CGFloat = timeViewHei
        
        let diff1:CGFloat = 10
        
        self.frame = CGRect (x: diff1/2, y: self.frame.minY, width: ScreenWid - diff1, height: labHei)
        
        var array1 = ["时间从：","时间到："]
        
        var array2 = ["2014-01-01","2016-01-01"]
        
        if otherSel{
            array1.append("")
            array2.append("所有")
        }
        
        let diff:CGFloat = (self.frame.width - CGFloat(array1.count) * (lab1Wid+lab2Wid) - btnWid)/CGFloat(array1.count)
        
        for i in 0..<array1.count {
            let lab1 = UILabel (frame: CGRect (x: CGFloat(i)*(lab1Wid+lab2Wid+diff), y: 0, width: lab1Wid, height: labHei))
            lab1.text = array1[i]
            self.addSubview(lab1)
            
            let lab2 = UILabel (frame: CGRect (x: lab1.frame.maxX, y: 0, width: lab2Wid, height: labHei))
            lab2.text = array2[i]
            lab2.textAlignment = .center
            lab2.textColor = UIColor.red
            lab2.layer.masksToBounds = true
            lab2.layer.cornerRadius = 4
            lab2.layer.borderWidth = 1
            lab2.layer.borderColor = UIColor.gray.cgColor
            
            self.addSubview(lab2)
            
            lab2.isUserInteractionEnabled  = true
            
            if i==0{
                let tap = UITapGestureRecognizer.init(target: self, action: #selector(clickStartTime))
                lab2.addGestureRecognizer(tap)
                startLabel = lab2
            }else if (i == 1){
                let tap = UITapGestureRecognizer.init(target: self, action: #selector(clickEndTime))
                lab2.addGestureRecognizer(tap)
                endLabel = lab2
            }else{
                lab2.adjustsFontSizeToFitWidth = true
                let tap = UITapGestureRecognizer.init(target: self, action: #selector(clickselectLabel))
                lab2.addGestureRecognizer(tap)
                selectLabel = lab2
                headLabel = lab1
            }
        }
        
        let queryBtn = UIButton()
        queryBtn.frame = CGRect (x: self.frame.width - btnWid, y: 0, width: btnWid, height: labHei)
        queryBtn.layer.masksToBounds = true
        queryBtn.layer.borderWidth = 1
        queryBtn.layer.cornerRadius = 4
        queryBtn.layer.borderColor = UIColor.black.cgColor
        queryBtn.setTitle("查询", for: .normal)
        queryBtn.setTitleColor(UIColor.red, for: .normal)
        queryBtn.addTarget(self, action: #selector(clickQueryBtn(sender:)), for: .touchUpInside)
        self.addSubview(queryBtn)
        
        if otherSel{
            popupView = PopupView.init(frame: CGRect (x: selectLabel.frame.minX, y: self.frame.maxY + 52, width: 0, height: 0), superView: supView)
            popupView.delegate = self
        }
        
        setTimeLable()
        datePickerView = DatePickerView.init(frame: ScreenFrame)
        datePickerView.delegate = self
        supView.addSubview(datePickerView)
                
    }
    
    
    func clickStartTime() {
        supView.bringSubview(toFront: datePickerView)
        timeString = TimeStyleString.startStyleString
        datePickerView.title = TimeStyleString.startStyleString.rawValue
        datePickerView.dateViewShow(time: startLabel.text!)
    }
    
    func clickEndTime() {
        supView.bringSubview(toFront: datePickerView)
        timeString = TimeStyleString.endStyleString
        datePickerView.title = TimeStyleString.endStyleString.rawValue
        datePickerView.dateViewShow(time: endLabel.text!)
    }
    
    func clickselectLabel() {
        popupView.show()
    }
    
    func clickQueryBtn(sender:UIButton) {

        if (selectLabel != nil) {
            if let start = startLabel.text , let end = endLabel.text,let select = selectLabel.text{
                delegate?.timeView(startTime: start, endTime: end, selectText: select)
            }
        }else{
            if let start = startLabel.text , let end = endLabel.text{
                delegate?.timeView(startTime: start, endTime: end, selectText: "")
            }
        }
    }
    
    func setTimeLable() {
        let endDate:Date = Date (timeIntervalSinceNow:  -24*60*60)
        
        let startDate:Date = Date (timeInterval: -6*24*60*60, since: endDate)
        
        let formatter = DateFormatter()
        //日期样式
        formatter.dateFormat = "yyyy-MM-dd"
        
        startLabel.text = formatter.string(from: startDate)
        endLabel.text = formatter.string(from: endDate)
    }
    /**************  DatePickerViewDelegate, PopupViewDelegate  ***************/
    func datePickerView(time: String) {
        switch timeString {
        case .startStyleString:
            self.startLabel.text = time
        case .endStyleString:
            self.endLabel.text = time
        }
    }
    
    func popupViewSelect(text: String) {
        selectLabel.text = text
    }
    func setHead(headString:String) {
        headLabel.text = headString
    }
    func setList(list:[String]) {
        popupView.setList(list: list)
    }
    
    

}
