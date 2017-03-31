//
//  AppTrafficView.swift
//  tiger
//
//  Created by wayne on 16/10/31.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit
import Charts

class AppTrafficView: UIView,TimeViewDelegate ,IAxisValueFormatter {
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
    
    var timeView:TimeView!
    
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
        timeView = TimeView.init(frame: CGRect (x: 0, y: diff, width: self.frame.width, height: 0), superView: mainVi, otherSelect:true)
        timeView.delegate = self
        self.addSubview(timeView)
        
        timeView.setHead(headString: "协议号：")
        
        barView = BarChartView.init(frame: CGRect (x: 0, y: timeView.frame.maxY + timeViewBarViewDiff, width: self.frame.width, height: self.frame.height - diff - timeView.frame.maxY))
        barView.noDataText = "暂无数据"
        barView.drawValueAboveBarEnabled = true
        barView.chartDescription?.text = ""
        self.addSubview(barView)
        
        requestData(startTime: nowTime.startTime, endTime: nowTime.endTime, selectString: "")
    }
    
    private func requestData(startTime:String,endTime:String, selectString:String){
        CommSender.appTraffic(startTime, ymdTo: endTime, select: selectString, callback: { (code, msg, error) in
            
            if code == 200{
                self.timeView.setList(list: AppTrafficManage.share.channel)
                self.barView.data = self.setData()
                self.barView.animate(xAxisDuration: 1.0, yAxisDuration: 1.0)
            }else{
                Utils.alertMessage(message: msg!)
            }
            }, view: self)
    }
    
    private func setData() -> BarChartData {
        
        let labelArray = AppTrafficManage.share.labelArray
        let dataDict = AppTrafficManage.share.dataDict
        
        var entryArray1 = [BarChartDataEntry]()
        var entryArray2 = [BarChartDataEntry]()
        
        let array1:[Double] = dataDict.object(forKey: labelArray[0]) as! [Double]
        let array2:[Double] = dataDict.object(forKey: labelArray[1]) as! [Double]
        
        for i in 0..<array1.count {
            entryArray1.append(BarChartDataEntry (x: Double(i), y: array1[i]))
            entryArray2.append(BarChartDataEntry (x: Double(i), y: array2[i]))
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
        requestData(startTime: startTime, endTime: endTime,selectString: selectText)
        
    }
    
    /**************** IAxisValueFormatter *****************/
    public func stringForValue(_ value: Double, axis: AxisBase?) -> String {
        
        if axis == self.barView.xAxis {
            
            if value<1 && value>=0{
                return AppTrafficManage.share.formatterArray[0]
            }else if value<2 && value>=1{
                return AppTrafficManage.share.formatterArray[1]
            }else if value<3 && value>=2{
                return AppTrafficManage.share.formatterArray[2]
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
