//
//  TigerConst.swift
//  tiger
//
//  Created by wayne on 16/9/30.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

func RGBA(r:CGFloat,g:CGFloat,b:CGFloat,a:CGFloat) -> UIColor{
    return UIColor (red: r/255, green: g/255, blue: b/255, alpha: a)
}

func RGB(r:CGFloat,g:CGFloat,b:CGFloat) -> UIColor{
    return UIColor (red: r/255, green: g/255, blue: b/255, alpha: 1)
}



//屏幕的宽
let ScreenWid:CGFloat = UIScreen.main.bounds.size.width
//屏幕的高
let ScreenHei:CGFloat = UIScreen.main.bounds.size.height
//屏幕的Frame
let ScreenFrame = UIScreen.main.bounds

//浅黄
let yellowLight = RGB(r: 255, g: 165, b: 132)
//浅蓝（浅）
let blueLight = RGB(r: 135, g: 206, b: 250)
//紫色
let purpleColor = RGB(r: 218, g: 112, b: 214)
//绿色
let greenLight = RGB(r: 50, g: 205, b: 50)
//蓝色（深）
let blueColor = RGB(r: 100, g: 149, b: 235)

//柱状图线的颜色
let barViewLineColor:UIColor = UIColor.gray


let colorArray:[UIColor] = [yellowLight,blueColor,purpleColor,greenLight,blueColor]

//获取当前时间 开始时间和结束时间
let nowTime = Utils.getTime()

/**
 
 x表示每个组的 柱数量
 
 (x-1)*groupSpace
 x*barSpace
 x*barWidth
 
 上三项之和 恒等于 1
 
 */

/**
 barChart1 1个柱图
 barChart2 2个柱图
 barChart3 3个柱图
 */

let barChart1:(groupSpace:Double,barSpace:Double,barWidth:Double) = (0,0.2,0.4)
let barChart2:(groupSpace:Double,barSpace:Double,barWidth:Double) = (0.1,0.05,0.4)
let barChart3:(groupSpace:Double,barSpace:Double,barWidth:Double) = (0.05,0.05,0.25)

//选择项到导航栏的距离
let toNavBarHeight:CGFloat = 5
//选择项和柱状图之间的距离
let timeViewBarViewDiff:CGFloat = 2








