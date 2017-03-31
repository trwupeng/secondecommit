//
//  DetailViewController.swift
//  tiger
//
//  Created by wayne on 16/10/11.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

class DetailViewController: UIViewControllerEx{
    
    var api:ApiString!
    var backView:UIView!
    var backViewFrame:CGRect!
    
    
    
    init(apiString:ApiString) {
        super.init()
        api = apiString
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }

    override func viewDidLoad() {
        super.viewDidLoad()
        
//        self.title = "图表展示"
        view.backgroundColor = UIColor.white
        
        creatUI()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func creatUI() {
        
//        print(UIApplication.shared.statusBarFrame,self.navigationController?.navigationBar.frame)
        backViewFrame = CGRect (x: 0, y: 52, width: ScreenWid, height: ScreenHei - 52)
        backView = UIView (frame: backViewFrame)
        backView.backgroundColor = UIColor.white
        view.addSubview(backView)
        
        addSubViewToBackView()
        
        return
        
        
//        let timeView = TimeView.init(frame: CGRect (x: 0, y:40, width: ScreenWid, height: 40), superView: view)
//        let timeView = TimeView.init(frame: CGRect (x: 0, y:40, width: ScreenWid, height: 40), superView: (navigationController?.view)!, otherSeleString: "渠道选择：", list: ["小米","华为","苹果","三星","众星","辣椒","iPad","Mac","对大家快乐的快乐纳斯达克说的是疯狂"])
//        timeView.delegate = self
//        view.addSubview(timeView)
//        
//        let annotationView = AnnotationView.init(frame: CGRect (x: 0, y: timeView.frame.maxY + 5, width: 0, height: 0), list: ["当天注册","前一天至前五天注册","一年前注册"])
//        view.addSubview(annotationView)
        
    }
    
    /************** 发送网络请求 ***********/
    private func requestData(startTime: String, endTime: String, selectText: String) {
        
        
        
    }
    
    /***************** 创建子试图 ******************/
    func addSubViewToBackView() {
        
        let frame = CGRect (x: 0, y: 0, width: backViewFrame.width, height: backViewFrame.height)
        
        
        switch api {
        case .some(api):
            let url:ApiString = api
            
            switch url {
            case .pcsitetraffic: //网页流量
                
                let pcsitetrafficView = PcsitetrafficView.init(mainView: (navigationController?.view)!, frame: frame)
                backView.addSubview(pcsitetrafficView)
            case .umengdata: //APP流量
                let appTrafficView = AppTrafficView.init(mainView: (navigationController?.view)!, frame: frame)
                backView.addSubview(appTrafficView)
                
            default:
                break
            }
        default:
            break
        }
    }
   

}
