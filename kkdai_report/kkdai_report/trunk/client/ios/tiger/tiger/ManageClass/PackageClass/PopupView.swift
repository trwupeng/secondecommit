//
//  PopupView.swift
//  tiger
//
//  Created by wayne on 16/10/12.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

protocol PopupViewDelegate:class{
    func popupViewSelect(text:String)
}

let popupViewWidth:CGFloat = 160


class PopupView: UIView , UITableViewDelegate, UITableViewDataSource{
    
    private let cellID = "PopupViewCellId"
    
    weak var delegate:PopupViewDelegate?
    
    var backView:UIView!
    
    var listArray: [String]!
    
    var tableView:UITableView!
    
    init(frame: CGRect, superView:UIView) {
        super.init(frame: frame)
        creatUI(superView:superView)
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    func creatUI(superView:UIView) {
        
        self.frame = CGRect (x: self.frame.minX, y: self.frame.minY + 5, width: popupViewWidth, height: 200)
        
        backView = UIView (frame: ScreenFrame)
        backView.isHidden = true
        backView.backgroundColor = UIColor.clear
        superView.addSubview(backView)
        
        let alphaView:UIView!
        alphaView = UIView (frame: ScreenFrame)
        alphaView.backgroundColor = UIColor.black
        alphaView.alpha = 0.5
        backView.addSubview(alphaView)
        
        let tap = UITapGestureRecognizer (target: self, action: #selector(clickBackView))
        alphaView.isUserInteractionEnabled = true
        alphaView.addGestureRecognizer(tap)
        
        backView.addSubview(self)
        self.layer.masksToBounds = true
        self.layer.borderWidth = 0.5
        self.layer.borderColor = UIColor.black.cgColor
        self.layer.cornerRadius = 4
        
        tableView = UITableView (frame: CGRect (x: 0, y: 0, width: self.frame.width, height: self.frame.height), style: .plain)
        tableView.delegate = self
        tableView.dataSource = self
        tableView.separatorStyle = .none
        self.addSubview(tableView)
        
    }
    
    func clickBackView() {
        backView.isHidden = true
    }
    
    func show() {
        backView.isHidden = false
        tableView.contentOffset = CGPoint (x: 0, y: 0)
    }
    
    /*********************UITableViewDelegate, UITableViewDataSource********************/
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if (listArray != nil) && listArray.count > 0 {
            return listArray.count
        }else{
            return 0
        }
        
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return popupViewCellHeight
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        var cell = (tableView .dequeueReusableCell(withIdentifier: cellID))
        
        if !(cell != nil) {
            cell = PopupViewCell.init(style: .default, reuseIdentifier: cellID)
        }
        (cell as! PopupViewCell).setNameLabel(name: listArray[indexPath.row])
        return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        tableView.deselectRow(at: indexPath, animated: true)
        clickBackView()
        delegate?.popupViewSelect(text: listArray[indexPath.row])
    }
    
    func setList(list:[String]) {
        listArray = list
        tableView.reloadData()
    }
    
}
