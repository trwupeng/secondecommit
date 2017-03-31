<?php
namespace Prj\Acl;
class RptManage extends \Sooh\Base\Acl\Ctrl
{
	protected function initMenu()
	{
		return array(
// 			'资产管理.添加新资产'=>array('manage','iosnatureworth1', 'test1',array(),array()),
// 			'资产管理.审核资产'=>array('manage','iosnatureworth2', 'tst2',array(),array('external'=>true)),
			
			'报表系统.日常报表'=>array('report','Rptdailybasic','recent', array(),array()),
			'报表系统.渠道导入量'=>array('report','Copartnerworth','index',array(),array()),
		    '报表系统.月报表'=>array('report','Monthreport', 'index',array(),array()),
// 			'系统管理.管理员一览'=>array('manage','managers','index',array(),array()),
// 			'系统管理.test'=>array('manage','test','index',array(),array()),
		);
	}
	
	public function dump()
	{
		var_log($this->rights,'rights========');
	}
}