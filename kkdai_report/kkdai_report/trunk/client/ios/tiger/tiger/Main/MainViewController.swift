//
//  MainViewController.swift
//  tiger
//
//  Created by wayne on 16/9/30.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class MainViewController: UIViewControllerEx, UITableViewDelegate, UITableViewDataSource {

    var tableView: UITableView?
    
    var againBtn: UIButton!
    
    
    var data = [ChildrenOne]()
    
    
//    private let arr1:Array = ["客服",["绑卡未购买"]] as [Any]
//    private let arr2:Array = ["报表",["访问权限","日报（整合版）","日常（数字版）"]] as [Any]
//    private let arr3:Array = ["财务",["财务参数设置","费用明细","线下业务收支明细"]] as [Any]
//    private let arr4:Array = ["业务",["业务参数设置","融资业务月初户数","融资业务周报","融资业务周报查看","业务进展情况","业务指标",]] as [Any]
//    private let arr5:Array = ["渠道管理",["渠道管理","协议管理","渠道转换"]] as [Any]
//    private let arr6:Array = ["系统",["管理员一览","图片"]] as [Any]
//    private let arr7:Array = ["可视化报表",["网页流量","App流量","注册至理财人数","注册至理财转化率","新增理财人数","新增理财金额","新增理财人均","新老用户理财人数","新老用户理财金额","新老用户理财人均","资金数据对比","留存数据","复投人数","复投率"]] as [Any]
//    private let arr8:Array = ["财务管理",["投标统计","流标统计","用户统计／提现统计","还款统计－投资人","还款统计－借款人","管理费","优惠券发放","优惠券使用","好友返现","标的放款明细","服务费"]] as [Any]
//
//    
//    
//    private var cellArray:[NSArray] = []
    
    override func viewDidLoad() {
        super.viewDidLoad()
        view.backgroundColor = UIColor.white
        self.title = "后台管理系统"
        
        navigationController?.navigationBar.titleTextAttributes = [NSForegroundColorAttributeName:UIColor.red]
        
//        cellArray.append(arr1 as NSArray)
//        cellArray.append(arr2 as NSArray)
//        cellArray.append(arr3 as NSArray)
//        cellArray.append(arr4 as NSArray)
//        cellArray.append(arr5 as NSArray)
//        cellArray.append(arr6 as NSArray)
//        cellArray.append(arr7 as NSArray)
//        cellArray.append(arr8 as NSArray)
        
        creatUI()
        getMenu()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    

    func creatUI() {
        tableView = UITableView (frame: CGRect (x: 0, y: 0, width: ScreenWid, height: ScreenHei), style: .grouped)
        tableView?.delegate = self
        tableView?.dataSource = self
        view.addSubview(tableView!)
        
        // 重新加载 按钮
        againBtn = UIButton (type: .roundedRect)
        againBtn.setTitle("重新加载", for: .normal)
        againBtn.frame = CGRect (x: 0, y: 0, width: 80, height: 60)
        againBtn.addTarget(self, action:#selector(clickAgain(sender:)), for: .touchUpInside)
        againBtn.center = CGPoint (x: ScreenWid/2, y: ScreenHei/2)
        view.addSubview(againBtn)
        
    }
    
    //点击重新加载
    func clickAgain(sender:UIButton) {
        getMenu()
    }
    
    //获取菜单
    private func getMenu() {
        CommSender.getMenu({ (code, msg, error) in
            if code==200{
                self.requestSuc(suc: true)
                self.data = MenuManage.share.children
                self.tableView?.reloadData()
            }else{
                self.requestSuc(suc: false)
                if (msg != nil){
                    Utils.alertMessage(message: msg!)
                }
                
            }
            }, view: view)
    }
    
    private func requestSuc(suc:Bool) {
        tableView?.isHidden = !suc
        againBtn.isHidden = suc
    }
    
    
    /********************** UITableViewDelegate, UITableViewDataSource *********************/
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        
        if data.count > 0 {
            return data.count
        }
        return 0
    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        return 10
    }
    
    func tableView(_ tableView: UITableView, heightForFooterInSection section: Int) -> CGFloat {
        return 10
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellID = "CellID"
        
        var cell:UITableViewCell!
        
        cell = tableView .dequeueReusableCell(withIdentifier: cellID)
        
        if !(cell != nil) {
            cell = UITableViewCell (style: .default, reuseIdentifier: cellID)
        }
        let childOne = data[indexPath.row]
        
        cell.textLabel?.text = childOne.capt
        
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        tableView.deselectRow(at: indexPath, animated: true)
        
        let childOne:ChildrenOne = self.data[indexPath.row]
        
        
        let secVC = SecondViewController.init(cellName: childOne.childer)
        
        secVC.title = childOne.capt
        
        navigationController?.pushViewController(secVC, animated: true)
    }

}
