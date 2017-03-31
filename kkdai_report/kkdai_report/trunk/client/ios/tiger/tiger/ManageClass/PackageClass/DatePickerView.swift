//
//  DatePickerView.swift
//  tiger
//
//  Created by wayne on 16/10/11.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

protocol DatePickerViewDelegate: class {
    func datePickerView(time:String)
}

class DatePickerView: UIView {
    
    let formatter = DateFormatter()
    
    weak var delegate:DatePickerViewDelegate?

    var title:String {
        get{
            return self.title
        }
        set (newVal){
           titleLabel.text = newVal
        }
    }
    var time:String = ""
    var scorllEndTime = ""
    
    
    var datePickerView: UIView!
    var alphaView: UIView!
    let datePickerViewHei:CGFloat = 150
    
    var titleLabel:UILabel!
    
    var datePicker: UIDatePicker!
    
    
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        creatUI()
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    private func creatUI() {
        
        //日期样式
        formatter.dateFormat = "yyyy-MM-dd"
        
        self.isHidden = true
        
        self.frame = ScreenFrame
        
        let backView = UIView (frame: ScreenFrame)
        backView.backgroundColor = UIColor.clear
        self.addSubview(backView)
        
        alphaView = UIView (frame: ScreenFrame)
        alphaView.backgroundColor = UIColor.black
        alphaView.alpha = 0.5
        backView.addSubview(alphaView)
        
        let tap = UITapGestureRecognizer (target: self, action: #selector(dateViewDisappear))
        alphaView.isUserInteractionEnabled = true
        alphaView.addGestureRecognizer(tap)
        
        
        let tabBarHei:CGFloat = 44
        
        
        datePickerView = UIView (frame: CGRect (x: 0, y: ScreenHei, width: ScreenWid, height: datePickerViewHei))
        datePickerView.backgroundColor = UIColor.white
        self.addSubview(datePickerView)
        
        let btnWid:CGFloat = 80
        let diff:CGFloat = 40
        
        
        let tabbarView = UIView (frame: CGRect (x: 0, y: 0, width: ScreenWid, height: tabBarHei))
        datePickerView.addSubview(tabbarView)
        
        titleLabel = UILabel (frame: CGRect (x: diff + btnWid, y: 0, width: ScreenWid - 2*(diff + btnWid), height: tabBarHei))
        titleLabel.textAlignment = .center
        titleLabel.textColor = UIColor.red
        tabbarView.addSubview(titleLabel)
        
        let array = ["取消","确定"]
        
        
        for i in 0..<2 {
            let button = UIButton ()
            button.tag = 100 + i
            button.addTarget(self, action: #selector(clickButton(sender:)), for: .touchUpInside)
            var X:CGFloat
            if i == 0{
                X = diff
                button.backgroundColor = UIColor.white
                button.layer.borderColor = UIColor.red.cgColor
                button.layer.borderWidth = 2
                button.setTitleColor(UIColor.red, for: .normal)
            }else{
                X = titleLabel.frame.maxX
                button.backgroundColor = UIColor.red
                button.setTitleColor(UIColor.white, for: .normal)
            }
            
            button.layer.masksToBounds = true
            button.layer.cornerRadius = 4
            button.frame = CGRect (x: X, y: 8, width: btnWid, height: tabBarHei - 16)
            button.setTitle(array[i], for: .normal)
            tabbarView.addSubview(button)
            
        }
        
        let lineHei:CGFloat = 0.5
        
        for i in 0..<2 {
            let line = UIView ()
            line.frame = CGRect (x: 0, y: CGFloat(i) * (tabBarHei - lineHei), width: ScreenWid, height: lineHei)
            line.backgroundColor = UIColor.gray
            tabbarView.addSubview(line)
        }
        
        
        datePicker = UIDatePicker (frame: CGRect (x: 0, y: tabBarHei, width: ScreenWid, height: datePickerViewHei - tabBarHei))

        let minDate = formatter.date(from: "2014-01-01")
        
        let maxDate:Date = Date (timeIntervalSinceNow: -24*60*60)
        
        datePicker.minimumDate = minDate
        datePicker.maximumDate = maxDate
                
        datePicker.datePickerMode = .date
        datePicker.backgroundColor = UIColor.white
        
        
        datePicker .addTarget(self, action: #selector(datePickerEditEnd(sender:)), for: .valueChanged)
        datePickerView.addSubview(datePicker)
        
    }
    
    
    @objc private func datePickerEditEnd(sender:UIDatePicker) {

        scorllEndTime = formatter.string(from: sender.date)
    }
    
    let duration = 0.2
    
    func dateViewShow(time:String) {
        
        datePicker.date = formatter.date(from: time)!
        
        self.isHidden = false
        UIView.animate(withDuration: duration, animations: {
            self.datePickerView.frame = CGRect (x: 0, y: ScreenHei - self.datePickerViewHei, width: ScreenWid, height: self.datePickerViewHei)
        }) { (finished:Bool) in
            if finished{
            }
        }
    }
    
    func dateViewDisappear() {
        UIView.animate(withDuration: duration, animations: {
            self.datePickerView.frame = CGRect (x: 0, y: ScreenHei, width: ScreenWid, height: self.datePickerViewHei)
        }) { (finished:Bool) in
            if finished{
                self.isHidden = true
            }
        }
    }
    
    func clickButton(sender:UIButton) {
        dateViewDisappear()
        if sender.tag == 101{
            delegate?.datePickerView(time: formatter.string(from: datePicker.date))
        }
    }
    

}
