<?php
/**
 * 开发测试环境使用的，客户端请跳过
 */
class DevController extends \Lib\Dev\ApidocCtrl
{
	
	public function init() {
		parent::init();
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')>30){
			throw new \ErrorException('ctrl not found');
		}
	}
	
	public function invitesAction()
	{
		$db = \Sooh\DB\Broker::getInstance();
		$employees = $db->getPair('test.tels', 'theid', 'thename');
		//aid 谁邀请的我  bid 我
		$dates = [20160205,20160206,20160207,20160208,20160209,20160210,20160211,20160212,20160213,20160214,20160215,20160216,
			20160217,20160218,20160219,20160220,20160221,20160222,20160223,20160224,20160225,20160226,20160227,20160228,20160229,20160301,
			20160302,20160303,20160304,20160305,20160306,20160307,20160308,20160309,20160310,20160311,20160313,20160314,20160315,20160316,
			20160317,20160318,20160319,20160320,20160321,20160322,20160323,20160324,20160325,20160326,20160327,20160328,20160329,20160330,20160331,];
		
		$type = ['aId','bReg','bBuy'];
		//$type = ['bId','aReg','aBuy'];
		$where = [$type[0]=>array_keys($employees),$type[1]=>$dates];
		$rs = $db->getRecords('test.ab', '*',$where);
		$ret = [];
		foreach($rs as $r){
			$uu = $r[$type[0]];
			$ymd = $r[$type[1]];
			$ret[$uu][$ymd]['reg']++;
			if($r[$type[2]]>0){
				$ret[$uu][$ymd]['buy']++;
			}
		}
		
		\Sooh\Base\Ini::getInstance()->viewRenderType('echo');
		echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8">';
		echo '<table border=1><tr><th rowspan=2>用户<th colspan=2>'.implode('<th colspan=2>', $dates).'</tr>';
		echo '<tr>'.str_repeat('<th>注册<th>购买', sizeof($dates))."</tr>";
		foreach($ret as $uid=>$r){
			echo '<tr><td>'.$employees[$uid]."</td>";
			foreach ($dates as $ymd){
				echo '<td>'.$r[$ymd]['reg'].'<td>'.$r[$ymd]['buy'];
			}
		}
		echo '</table>';
	}
	
	public function testAction()
	{
		$url = "http://sdk.gmall.cn/interface/GetProducts?LoginName=kkjryxgs&Password=".  substr(md5('kkjr2016'),8,16)."&SmsKind=820";

		$str = \Sooh\Base\Tools::httpGet($url);
		
		$xml = simplexml_load_string($str);
		\Sooh\Base\Ini::getInstance()->viewRenderType("json");
		$this->_view->assign('data',json_decode(json_encode($xml),TRUE));
	}
}



