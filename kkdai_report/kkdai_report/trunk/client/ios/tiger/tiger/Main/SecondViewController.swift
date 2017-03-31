//
//  SecondViewController.swift
//  tiger
//
//  Created by wayne on 16/10/10.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class SecondViewController: UIViewControllerEx, UITableViewDelegate,UITableViewDataSource {
    
    private let cellID = "secondCellId"
    
    
    var tableView: UITableView!
    
    var childrenOne = [ChildrenTwo]()
    
    
    
    init(cellName: [ChildrenTwo]) {
        super.init()
        childrenOne = cellName
        view.backgroundColor = UIColor.white
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }

    override func viewDidLoad() {
        super.viewDidLoad()

//        self.title = "详情列表"
        creatUI()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    

    func creatUI() {
        
        tableView = UITableView (frame: CGRect (x: 0, y: 0, width: ScreenWid, height: ScreenHei), style:.grouped)
        tableView.delegate = self
        tableView.dataSource = self
        view.addSubview(tableView)
        
    }
    
    /********************** UITableViewDelegate, UITableViewDataSource *********************/
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if childrenOne.count > 0{
            return childrenOne.count
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
        
        
        var cell = tableView .dequeueReusableCell(withIdentifier: cellID)
        
        if !(cell != nil) {
            cell = UITableViewCell (style: .default, reuseIdentifier: cellID)
        }
        let childTwo = childrenOne[indexPath.row]
        
        cell?.textLabel?.text = childTwo.capt
        
        return cell!
        
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        tableView.deselectRow(at: indexPath, animated: true)
        
        let childTwo = childrenOne[indexPath.row]
        
        let url = childTwo.url
        
        var api:ApiString?
        
        if url.contains(ApiString.login.rawValue){
            api = ApiString.login
        }else if url.contains(ApiString.menu.rawValue){
            api = ApiString.menu
        }else if url.contains(ApiString.pcsitetraffic.rawValue){
            api = ApiString.pcsitetraffic
        }else if url.contains(ApiString.umengdata.rawValue){
            api = ApiString.umengdata
        }else if url.contains(ApiString.regtoinvestmenttrans.rawValue){
            api = ApiString.regtoinvestmenttrans
        }else if url.contains(ApiString.regtoinvestmenttransrate.rawValue){
            api = ApiString.regtoinvestmenttransrate
        }else if url.contains(ApiString.newfinancial.rawValue){
            api = ApiString.newfinancial
        }else if url.contains(ApiString.newlicaiamount.rawValue){
            api = ApiString.newlicaiamount
        }else if url.contains(ApiString.newfinancialavg.rawValue){
            api = ApiString.newfinancialavg
        }else if url.contains(ApiString.oldandnewfinancial.rawValue){
            api = ApiString.oldandnewfinancial
        }else if url.contains(ApiString.oldandnewfinancialamount.rawValue){
            api = ApiString.oldandnewfinancialamount
        }else if url.contains(ApiString.oldandnewfinancialavg.rawValue){
            api = ApiString.oldandnewfinancialavg
        }else if url.contains(ApiString.fundsdata.rawValue){
            api = ApiString.fundsdata
        }else if url.contains(ApiString.retaineddata.rawValue){
            api = ApiString.retaineddata
        }else if url.contains(ApiString.compounddata.rawValue){
            api = ApiString.compounddata
        }else if url.contains(ApiString.compoundrate.rawValue){
            api = ApiString.compoundrate
        }else{
            api = ApiString.empte
        }
        
        let detailVC = DetailViewController.init(apiString: api!)
        detailVC.title = childTwo.capt
        
        navigationController?.pushViewController(detailVC, animated: true)
    }

}
