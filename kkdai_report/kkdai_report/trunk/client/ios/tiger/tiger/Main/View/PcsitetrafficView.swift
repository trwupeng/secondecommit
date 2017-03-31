//
//  PcsitetrafficView.swift
//  tiger
//
//  Created by wayne on 16/10/27.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit
import Charts

class PcsitetrafficView: UIView,TimeViewDelegate ,IAxisValueFormatter{
    /**
     
     x表示每个组的 柱数量
     
     x*barWidth
     (x-1)*groupSpace
     x*barSpace
     
     上三项之和 恒等于 1
     
    */
    let groupSpace:Double = barChart2.groupSpace
    let barSpace:Double = barChart2.barSpace
    let barWidth:Double = barChart2.barWidth

    var mainVi:UIView!
    var barView:BarChartView!
    
    
    init(mainView:UIView, frame:CGRect) {
        super.init(frame: frame)
        mainVi = mainView
        creatUI()
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
    
    private func creatUI() {
        let diff:CGFloat = toNavBarHeight
        
        //时间框
        let timeView = TimeView.init(frame: CGRect (x: 0, y: diff, width: self.frame.width, height: 0), superView: mainVi , otherSelect:false)
//        timeView.backgroundColor = UIColor.orange
        timeView.delegate = self
        self.addSubview(timeView)
        
        barView = BarChartView.init(frame: CGRect (x: 0, y: timeView.frame.maxY + timeViewBarViewDiff, width: self.frame.width, height: self.frame.height - diff - timeView.frame.maxY))
        barView.noDataText = "暂无数据"
        barView.drawValueAboveBarEnabled = true
        barView.chartDescription?.text = ""
        self.addSubview(barView)
        
        requestData(startTime: nowTime.startTime, endTime: nowTime.endTime)
    }
    
    private func requestData(startTime:String,endTime:String){
        CommSender.webTraffic(startTime, ymdTo: endTime, callback: { (code, msg, error) in
            if code == 200{
                if let mg = msg {
                    if mg == "" {
                        self.barView.data = self.setData()
                        self.barView.animate(xAxisDuration: 1.0, yAxisDuration: 1.0)
                    }else{
                        Utils.alertMessage(message: mg)
                    }
                }
            }
            }, view: self)
    }
    
    private func setData() -> BarChartData {
        
        let labelArray = PcsitetrafficManage.share.labelArray
        let rsDict = PcsitetrafficManage.share.rsDict
        
        var entryArray1 = [BarChartDataEntry]()
        var entryArray2 = [BarChartDataEntry]()
        
        let keys:[String] = ["pv_count","visitor_count","ip_count"]
        
        let rs1:NSDictionary = rsDict.object(forKey: labelArray[0]) as! NSDictionary
        let rs2:NSDictionary = rsDict.object(forKey: labelArray[1]) as! NSDictionary
        
        for i in 0..<keys.count {
            
            var y1:Double = 0
            var y2:Double = 0
            
            if let num = rs1.object(forKey: keys[i]) as? String {
                y1 = Double(Double(num)!)
            }
            
            if let num = rs2.object(forKey: keys[i]) as? String {
                y2 = Double(Double(num)!)
            }
            
            entryArray1.append(BarChartDataEntry (x: Double(i), y: y1))
            entryArray2.append(BarChartDataEntry (x: Double(i), y: y2))
        }
        
        let dataSet1:BarChartDataSet = BarChartDataSet (values: entryArray1, label: labelArray[0])
        dataSet1.colors = [yellowLight]
        dataSet1.valueTextColor = yellowLight
        let dataSet2:BarChartDataSet = BarChartDataSet (values: entryArray2, label: labelArray[1])
        dataSet2.colors = [blueLight]
        dataSet2.valueTextColor = blueLight
        
        let dataSets = [dataSet1,
                        dataSet2]
        
        let barData: BarChartData = BarChartData.init(dataSets: dataSets)
        barData.barWidth = barWidth
        
        let xAxis :XAxis = self.barView.xAxis
        xAxis.axisMinimum = 0
        xAxis.axisMaximum = barData.groupWidth(groupSpace: groupSpace, barSpace: barSpace) * Double(entryArray1.count)
        barData.groupBars(fromX: 0, groupSpace: groupSpace, barSpace: barSpace)
        xAxis.valueFormatter = self
        xAxis.centerAxisLabelsEnabled = true
        xAxis.labelPosition = .bottom
        xAxis.labelCount = 3
//        xAxis.forceLabelsEnabled = true
        
        self.barView.rightAxis.enabled = false//不绘制右边轴
        let leftAxis = self.barView.leftAxis;//获取左边Y轴
        leftAxis.axisMinimum = 0//设置Y轴的最小值

        return barData
        
    }
    
    /**************** TimeViewDelegate *****************/
    func timeView(startTime: String, endTime: String, selectText: String) {
        requestData(startTime: startTime, endTime: endTime)
        
    }
    
    /**************** IAxisValueFormatter *****************/
    public func stringForValue(_ value: Double, axis: AxisBase?) -> String {
        
        if axis == self.barView.xAxis {
            
            if value<1 && value>=0{
                return PcsitetrafficManage.share.formatterArray[0]
            }else if value<2 && value>=1{
                return PcsitetrafficManage.share.formatterArray[1]
            }else if value<3 && value>=2{
                return PcsitetrafficManage.share.formatterArray[2]
            }else{
                return ""
            }
            
        }else if axis == self.barView.leftAxis{
            return ""
        }else{
            return ""
        }
        
        
        
    }

}
