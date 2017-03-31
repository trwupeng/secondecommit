<?php
/**
 * Description of Forward
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class IndexController extends \Prj\BaseCtrl {
	// https://addons.test.ad.jinyinmao.com.cn/V1/forward/voucherinterest?ymd=20150501&clienttype=900&settleDate=2015-6-10&yield=5.5
	// https://addons.test.ad.jinyinmao.com.cn/V1/forward/investing?ProductNo=YBC10001004&Count=10&BankCardNo=6222021077338790061&PaymentPassword=yj718603&ClientType=900&PrincipalVolumeId=51
	
	public function indexAction()
	{
		$this->_view->assign('siteSummary',  \Prj\Data\Summary::homepage());
	}
	
	
}
