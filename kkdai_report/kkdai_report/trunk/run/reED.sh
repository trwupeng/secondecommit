#!/bin/bash


#日报数据：用户
php loopall.php RptDaily.EDAccounts

#日报数据：购买
php loopall.php RptDaily.EDBuyers

#日报数据：标的
php loopall.php RptDaily.EDProducts

#日报数据：存量
php loopall.php RptDaily.EDStocking

#日报数据：财务业务
php loopall.php RptDaily.EDFinance
