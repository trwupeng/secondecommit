//
//  EnumClass.swift
//  tiger
//
//  Created by wayne on 16/10/10.
//  Copyright © 2016年 kkd. All rights reserved.
//

import UIKit

enum SecondString: String{
    case one = "one"
}

class EnumClass: NSObject {

}

enum ApiString: String {
    case empte = ""
    case login = "manage/manager/login"
    case menu = "manage/manager/index"
    case pcsitetraffic = "report/pcsitetraffic/index"                             //网页流量
    case umengdata = "report/umengdata/index"                                     //APP流量
    case regtoinvestmenttrans = "report/regtoinvestmenttrans/index"
    case regtoinvestmenttransrate = "report/regtoinvestmenttransrate/index"
    case newfinancial = "report/newfinancial/index"
    case newlicaiamount = "report/newlicaiamount/index"
    case newfinancialavg = "report/newfinancialavg/index"
    case oldandnewfinancial = "report/oldandnewfinancial/index"
    case oldandnewfinancialamount = "report/oldandnewfinancialamount/index"
    case oldandnewfinancialavg = "report/oldandnewfinancialavg/index"
    case fundsdata = "report/fundsdata/index"
    case retaineddata = "report/retaineddata/index"
    case compounddata = "report/compounddata/index"
    case compoundrate = "report/compoundrate/index"
}
