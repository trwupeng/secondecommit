#!/bin/bash
#用户（tb_user_final）
php loopall.php Standalone.CrondNewRegister

#订单（tb_orders_final）
php loopall.php Standalone.CrondOrder

#产品（tb_products_final）
php loopall.php Standalone.CrondProducts

#绑卡
php loopall.php Standalone.CrondBindcard

#充值、提现
php loopall.php Standalone.CrondRecharges

#红包
php loopall.php Standalone.CrondPacket

#券
php loopall.php Standalone.CrondVoucher



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