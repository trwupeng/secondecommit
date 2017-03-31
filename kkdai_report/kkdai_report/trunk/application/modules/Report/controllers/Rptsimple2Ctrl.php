<?php
use \Rpt\EvtDaily\Base as prjlib_evtdaily;
/**
 * 每日数据简报，包括注册数，购买数，绑卡数等（数字、分布图、走势图）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Rptsimple2Ctrl extends \Prj\ManagerCtrl {
	protected $weekdays=array('日','一','二','三','四','五','六','日');
	protected $evtNames;
	protected function topmenu()
	{
		
	}

	/**
	 * 日报摘要
	 * 
	 */
	public function recentAction()
	{
        switch ($this->_request->get('selectA')) {
            case 'rb':
                $slectA = '手机版';
                break;
            case 'pc':
                $slectA = 'PC版';
                break;
            case 'cms':
                $slectA = 'CMS系统';
                break;
            default:
                '日报';
        }
        $this->_view->assign('selectA', $slectA);

		$db = $this->getDB();
		$rs = array();
		//$dt = time();
		$dt = mktime(0,0,0,1,4,2015);
		for($i=0;$i<7;$i++){
			$ymd = date('Ymd',$dt);
			$rs[$ymd]['Accounts'] = prjlib_evtdaily::getCopy('Accounts')->numOfAct($db, $ymd);
			$rs[$ymd]['BoughtTotalNum'] = prjlib_evtdaily::getCopy('BoughtTotalNum')->numOfAct($db, $ymd); 
			$rs[$ymd]['Stock'] = prjlib_evtdaily::getCopy('Stock')->numOfAct($db, $ymd);
			$rs[$ymd]['NewRegister']=prjlib_evtdaily::getCopy('NewRegister')->numOfAct($db, $ymd,null);
			
			$rs[$ymd]['NewChargeNum'] = prjlib_evtdaily::getCopy('NewChargeNum')->numOfAct($db, $ymd); 
			$rs[$ymd]['NewBought'] = prjlib_evtdaily::getCopy('NewBought')->numOfAct($db, $ymd);
			$rs[$ymd]['BoughtTotal'] = prjlib_evtdaily::getCopy('BoughtTotal')->numOfAct($db, $ymd);
			
			$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd);
			$rs[$ymd]['BindFailed']=prjlib_evtdaily::getCopy('BindFailed')->numOfAct($db, $ymd);
			$rs[$ymd]['BindRequest']=prjlib_evtdaily::getCopy('BindRequest')->numOfAct($db, $ymd); 
			
			$rs[$ymd]['BoughtAmount'] = prjlib_evtdaily::getCopy('BoughtAmount')->numOfAct($db, $ymd);
			$rs[$ymd]['StockChange'] = prjlib_evtdaily::getCopy('StockChange')->numOfAct($db, $ymd);
			$rs[$ymd]['ChargeAmount'] = prjlib_evtdaily::getCopy('ChargeAmount')->numOfAct($db, $ymd);
			$rs[$ymd]['WithdrawAmount'] = prjlib_evtdaily::getCopy('WithdrawAmount')->numOfAct($db, $ymd);

			$dt-=86400;
		}
		$this->_view->assign('records',$rs);

		
		$this->_view->assign('copartners', \Prj\Misc\Funcs::copartnerIdWithcopartnerName());
	}

	public function detailAction () {
	    $pageId = $this->_request->get('pageId',1)-0;
	    $pageSize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
	    $isDownloadExcel = $this->_request->get('__EXCEL__');
	    $pager = new \Sooh\DB\Pager($pageSize,$this->pageSizeEnum,false);
	    
		$actname = $this->_request->get('_pkey_val');
		$dt = time();
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem ('_ymd_g2', form_def::factory('起始日期', date('Ymd', $dt-6*86400), form_def::date))
			->addItem('_ymd_l2', form_def::factory('起始日期', date('Ymd', $dt), form_def::date))
			->addItem('_act_eq', form_def::factory('', $actname, form_def::hidden));
		
		$frm->fillValues();
// var_log($actname, 'actname>>>>>>>>>');		
		$where = $frm->getWhere();
		if ($isDownloadExcel == 1) {
		    $where = $this->_request->get('where');
		    $where['ymd]'] = $where['ymdFrom'];
		    $where['ymd['] = $where['ymdTo'];
		    $where['act='] = $where['act'];
		    unset($where['ymdFrom']);
		    unset($where['ymdTo']);
		    unset($where['act']);
		}
		if (empty($actname)) {
		    $actname = $where['act='];
		}
		$db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
		
		$act = '\\Rpt\\EvtDaily\\'.$where['act='];
		$actObj = new $act();		
		if ($actObj->formula() != null) {
		    $ymdFrom = $where['ymd]'];
		    $ymdTo = $where['ymd['];
		    $rs = array();
		    while ($ymdTo >= $ymdFrom){
		        $n = $db->getRecordCount(\Rpt\Tbname::tb_evtdaily,array('ymd'=>$ymdTo,'act'=>$actObj->formula()));
// var_log(\Sooh\DB\Broker::lastCmd(),'lastCmd>>>>>>>>>>>');		        
// var_log($n, $ymdTo.'>>>>>>>>>>>>>>>>>');		        
		        if ($n<1) {
		            $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
		            continue;
		        }
		        $rs[] = array('ymd'=>$ymdTo,'n'=>$actObj->numOfAct($db,$ymdTo,null)/$actObj->divisor());
		        
		        $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
		    }
		}else {
		  $rs = $db->getRecords(\Rpt\Tbname::tb_evtdaily, 'ymd, sum(n)/100 as n',$where, 'groupby ymd rsort ymd');
		}
        $headers = array('日期'=>40, $this->evtNames[$actname]=>70);
        if ($isDownloadExcel==1){
            return $this->downExcel($rs,array_keys($headers),null,false);
        }
// 		error_log(\Sooh\DB\Broker::lastCmd());
		$this->_view->assign('records', $rs);
		$this->_view->assign('headers', $headers);
		$where['ymdFrom'] = $where['ymd]'];
		$where['ymdTo'] = $where['ymd['];
		$where['act'] = $where['act='];
		unset($where['ymd]']);
		unset($where['ymd[']);
		unset($where['act=']);
		$this->_view->assign('where', $where);
	}

	public function init()
	{
		parent::init();
		define('SOOH_ACL_CLASS', '\PrjLib\Acl\JYM\sess');
		
		$this->_view->assign('actName',$this->evtNames=prjlib_evtdaily::AllEvt());
		$this->_view->assign('weekdayName',  $this->weekdays);
	}
	
	
	public function weekforAction()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		} else
			$this->_view->assign('showLogin', 0);
		$db = $this->getDB();
		$chart = new \Rpt\Misc\Chart1;
		$chart->parseInput($this->_request->getQuery());
		$grpBy = $chart->grpBy;
		
		$ymds=[];
		$dt = strtotime($chart->ymd);
		$nDays=6;
		for($i=$nDays;$i>=0;$i--){
			$n = $dt-86400*$i;
			$ymds[]= date('Ymd',$n);
		}
		$header=array();
		$records=array();
		$actList = $chart->where['act'];
		unset($chart->where['act']);
		$pos=0;
		if(!empty($chart->grpBy)){
			$funcForNameOfGrp='\\Rpt\\Misc\\NameOfGrpInChart::'.$chart->grpBy;
		}
		foreach($ymds as $pos=>$ymd){
			$ymdDesc = floor(($ymd%10000)/100).'-'.($ymd%100).'('.$this->weekdays[date('w',  strtotime($ymd))].')';
			$header[]=$ymdDesc;
			foreach($actList as $actId){
				$actDesc = $this->nameOfAct($actId);
				if(!empty($chart->grpBy)){
					$tmp = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $ymd,$chart->grpBy,$chart->where);
					foreach($tmp as $k=>$n){
						$records[$actDesc.'('.call_user_func($funcForNameOfGrp,$k,$actId).')'][$pos]=$n;
					}
				}else{
					$records[$actDesc][$pos] = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $ymd,null,$chart->where);
				}
			}
			$pos++;
		}
		if(!empty($chart->grpBy)){
			foreach($records as $s=>$r){
				for($pos=0;$pos<=$nDays;$pos++){
					if(!isset($r[$pos])){
						$records[$s][$pos]=0;
					}
					ksort($records[$s]);
				}
			}
		}
//var_log($header);
//array (  0 => '12-29(一)',  1 => '12-30(二)',  2 => '12-31(三)',  3 => '1-1(四)',  4 => '1-2(五)',  5 => '1-3(六)',  6 => '1-4(日)',)

//var_log($records);
//array (
//  '当日申请绑卡总人数' =>  array (0 => 0,    1 => 0,    2 => 0,    3 => 44,    4 => 88,    5 => 44,    6 => 88,  ),
//  '当日成功绑卡人数' =>   array (    0 => 0,    1 => 0,    2 => 0,    3 => 40,    4 => 80,    5 => 40,    6 => 80,  ),
//  '当日绑卡失败的人数' =>   array (    0 => 0,    1 => 0,    2 => 0,    3 => 4,    4 => 8,    5 => 4,    6 => 8,  ),
//)
		$this->_view->assign('rptData', \Rpt\Misc\Echarts::format($chart->titleMain, 
			$header, 	$records,
			$chart->titleSub
			)
		);
	}
	protected function nameOfAct($actId)
	{
		return call_user_func('\\Rpt\\EvtDaily\\'.$actId.'::displayName');
	}
	public function onedayforAction()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		}else $this->_view->assign('showLogin',0);	
		
		$db = $this->getDB();
		$chart = new \Rpt\Misc\Chart1;
		$chart->parseInput($this->_request->getQuery());
		
		$actList=  $chart->where['act'];
		unset($chart->where['act']);
		$records=array();
		if(!empty($chart->grpBy)){
			$funcForNameOfGrp='\\Rpt\\Misc\\NameOfGrpInChart::'.$chart->grpBy;
		}
		foreach($actList as $actId){
			
			$actIntro =$this->nameOfAct($actId);
			if(empty($chart->grpBy)){
				$records[$actIntro]=prjlib_evtdaily::getCopy($actId)->numOfAct($db, $chart->ymd, null,$chart->where);
			}else{
				$tmp = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $chart->ymd, $chart->grpBy,$chart->where);
				foreach($tmp as $k=>$n){
					$records[$actIntro.'('.call_user_func($funcForNameOfGrp,$k,$actId).')']=$n;
				}
			}
		}
		$header = array_keys($records);
		$tmp = array_values($records);
		$records=['data'=>$tmp];
//var_log($header);
//array (  0 => '当日成功绑卡人数(IOS)',  1 => '当日成功绑卡人数(安卓)',  2 => '当日绑卡失败的人数(IOS)',  3 => '当日绑卡失败的人数(安卓)',)
//var_log($records);
//array (  'data' =>   array (    0 => '40',    1 => '40',    2 => '4',    3 => '5',  ),)
		
		$this->_view->assign('rptData',  \Rpt\Misc\Echarts::format($chart->titleMain, 
			$header, $records,
			$chart->titleSub
			)
		);

	}
	public function depth1Action()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		}else $this->_view->assign('showLogin',0);		
		$forAct = $this->_request->get('forAct');
		$ymd=$this->_request->get('ymd');

		$this->_view->assign('args',array('forAct'=>$forAct,'ymd'=>$ymd));
		$this->_view->assign('grpName',$this->grpName);
	}
/**
	 * 
	 * @return \Sooh\DB\Interfaces\All
	 */
	protected function getDB()
	{
		return \Sooh\DB\Broker::getInstance('dbForRpt');
	}
	/**
	 * @return boolean 
	 */
	protected function checkLogin()
	{
		return true;
		$acl = \Sooh\DB\Acl\Acl::getInstance();
		return $acl->isLogined();
	}
	
	protected function onInit_chkLogin()
    {
		try{
			parent::onInit_chkLogin();
		} catch (\ErrorException $ex) {
			$code = $this->_request->get('transSCode',$this->session->get('transScode'));
			$act = strtolower($this->_request->getActionName());
			if( ($act==='recent' || $act==='depth1' || $act==='onedayfor' || $act==='weekfor') && $code==='kk2016dai01'){
				$this->session->set('transScode', $code);
				if($code==='kk2016dai01'){
					$this->manager = \Prj\Data\Manager::getCopy('root');
				}else{
					$this->manager = null;
				}
			}else{
				throw $ex;
			}
		}

    }
	
//	public function indexAction()
//	{
//		$pwd = $this->_request->get('pwd');
//		if(!empty($pwd)){
//			$_SESSION['rptDailybasicPassExpire']=time()+600;
//			$this->_view->assign('goto', \Sooh\Base\Tools::uri(null, 'recent'));
//		}
//	}
	public function errorAction()
	{
		
	}
	
//	public function briefAction()
//	{
//		if($this->checkLogin()==false){
//			$this->_view->assign('showLogin',1);
//			return;
//		}else $this->_view->assign('showLogin',0);
//		$db = $this->getDB();
//		$rs = array();
//		$dt = time();
//		for($i=0;$i<10;$i++){
//			$ymd = date('Ymd',$dt);
//			$rs[$ymd]['Accounts']=prjlib_evtdaily::getCopy('Accounts')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtTotal']=prjlib_evtdaily::getCopy('BoughtTotal')->numOfAct($db, $ymd);
//			$rs[$ymd]['NewBuyer']=prjlib_evtdaily::getCopy('NewBuyer')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtFeeTotal']=prjlib_evtdaily::getCopy('BoughtFeeTotal')->numOfAct($db, $ymd);
//			$rs[$ymd]['WithdrawFee']=prjlib_evtdaily::getCopy('WithdrawFee')->numOfAct($db, $ymd);
//			$rs[$ymd]['Register']=prjlib_evtdaily::getCopy('Register')->numOfAct($db, $ymd,null);
//			
//			$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd,null);
//			$rs[$ymd]['Accessed']=prjlib_evtdaily::getCopy('Accessed')->numOfAct($db, $ymd,null);
//			$rs[$ymd]['Login']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null);
//			$dt-=86400;
//		}
//		
//		$this->_view->assign('records',$rs);
//
//	}

}