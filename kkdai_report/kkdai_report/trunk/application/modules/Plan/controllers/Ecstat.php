<?php

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/12/22
 * Time: 9:44
 */
class EcstatController extends \Prj\ManagerCtrl
{
    protected $header = [
        ['loginName','通话人ID'],
        ['nickname','通话人'],
        ['userId','ECID'],
        ['total','通话次数'],
    ];

	protected $detailHeader = [
			['contactTime', '通话日期'],
			['customerName','客户名字'],
			['calltime', '通话时长'],
		];
	
	protected $customerHeader = [
			['customerId', '客户ID'],
			['customerName', '客户姓名'],
			['nickname', '通话人'],
			['contactTime', '反馈日期 '],
			['content', '反馈内容'],
		];

	public function detailAction()
	{
		$loginName = $this->_request->get( 'loginName' );
		$startDate = $this->_request->get( 'startDate' );
		$endDate = $this->_request->get( 'endDate' );
        $userInfo = \Prj\Data\Manager::getCopy($loginName);
		$userInfo->load();
        if($userInfo->exists() && $userInfo->getField('ec')) {
			$ecUserId = $userInfo->getField('ec');
			$viewFK = \Prj\Misc\ViewFK::getInstance();
			foreach( $this->detailHeader as $v ) {
				$viewFK->addRow( $v[0], $v[1] );
			}
			$viewFK->hideDefaultBtn();
			$viewFK->setPk( 'id' );
			$retData = \Prj\Data\EcData::getPhoneDetailList( $ecUserId, $startDate, $endDate );
			$tmp = [];
			foreach( $retData as $rs )
			{
				//var_log( $rs, 'rs' );
				if ( $rs['calltime'] == '0' )
				{
					$rs['calltime'] = '未接通';
				}
				else
				{
					$rs['calltime'] = \Prj\Tool\Func::changeTimeType( $rs['calltime'] );
				}
				if ( strlen($rs['customerName']) == 0 )
				{
					$rs['customerName'] = '无名氏';
				}
				//是否有反馈记录
				$hasRecord = true;
				if ( 0 == (intval($rs['customerId'])) )
				{
					$hasRecord = false;
				}
				else if ( 0 == \Prj\Data\EcData::getCustomerRecordNum($rs['customerId']) )
				{
					$hasRecord = false;
				}
				if ( $hasRecord )
				{
					$rs['customerName'] = array( 'value'=>$rs['customerName'],
												 'url'=>'/plan/ecstat/customerinfo?customerid=' . $rs['customerId'],
												 'toggle'=>'navtab',
												 'title'=>'EC客户反馈记录' );
				}
				else 
				{
					$rs['customerName'] = array( 'value'=>$rs['customerName'],
												'url'=>'javascript:alert(\'该客户无反馈记录\');',
												'alert'=>'javascript:alert(\'该客户无反馈记录\');');
				}
				if ( strlen($rs['url']) == 0 )
				{
					$rs['show'] = 0;
				}
				else
				{
					$rs['show'] = 1;
				}
				$tmp[] = $rs;
			}
			var_log( $tmp, 'tmp' );
			$viewFK->setData( $tmp );
			$uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','form'=>1,'startDate'=>$startDate,'endDate'=>$endDate],'voice');
			$viewFK->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('通话录音', $uri ), 'show' );
			$viewFK->setExtHeader( '<p><div><b>跟进人:' . $userInfo->getField( 'nickname' ) . '</b></div><p>' );
        }
		else
		{
			//todo 
			//这边需要一个提示，没有ec账户
		}

	}
	
	public function customerInfoAction()
	{
		$customerId = $this->_request->get( 'customerid' );
		$retData = \Prj\Data\EcData::getCustomerRecord($customerId);
		foreach( $retData as $rs )
		{
			
		}
	}
	
	public function voiceAction()
	{
		$id = $this->_request->get( 'id' );
		$retData = \Prj\Data\EcData::getPhoneUrl($id);
		var_log( $retData, 'phone url' );
		if ( count($retData) > 0 )
		{
			if ( strlen($retData[0]['url']) > 0 )
			{
				$this->_view->assign( 'voice', 'true' );
			}
			else
			{
				$this->_view->assign( 'voice', false );
			}
			$this->_view->assign( 'url', $retData[0]['url'] );
		}
		
	}
	
    public function indexAction(){
        $viewFK = \Prj\Misc\ViewFK::getInstance();
        foreach ($this->header as $v) {
            $viewFK->addRow($v[0] , $v[1]);
        }
        $viewFK->addSearchOT(\Prj\Misc\View::mb_zzjgck('checkbox'));
		$startDate = $this->_request->get( 'start' );
		$endDate = $this->_request->get( 'end' );
		if ( strlen( $startDate ) == 0 )
		{
			$startDate = date( 'Y-m-d', time()-60*60*24*30 );
		}
		if ( strlen( $endDate ) == 0 )
		{
			$endDate = date( 'Y-m-d', time()-60*60*24 );
		}
		$viewFK->setPk( 'loginName' );
		$uri = \Sooh\Base\Tools::uri(['loginName'=>'{$id}','form'=>1,'startDate'=>$startDate,'endDate'=>$endDate],'detail');
		$viewFK->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('EC详情', $uri ) );
		$userIds = $this->_request->get( 'userIds' );
        $viewFK->hideDefaultBtn()->showSearch(false)
            ->addSearch('userIds','跟进人名字或ID','text', $userIds )
            ->addSearch('start','起始日期','datepicker', $startDate )
            ->addSearch('end','截止日期','datepicker', $endDate );
        $data = $this->getData( $userIds, $startDate, $endDate );
       
/*
        $realData = [];
        foreach( $data as $rs )
        {
        	$t = intval($rs['total']);
        	$tmp = [ 'type' => 'process',	//类型
					 'percent' => intval($t*100/300), //进度（0-100）
					 'value' => $t, //显示的文本
					 'prefix' => 'ecstat_index_']; //进度条id的前缀，方便js查找
        	$rs['total'] = $tmp;
        	$realData[] = $rs;
        }
        var_log( $realData, 'realData' );
        $viewFK->setData($realData);
*/       
        $viewFK->setData($data);
    }

    protected function getData( $userIds, $startDate, $endDate ){
        $ecUserIds = [];
		$loginNames = [];
		if ( $userIds ) {
			$isLoginName = false;
			$checked = false;
			$nicknames = explode( ',', $userIds );
			foreach( $nicknames as $nickname ) {
				if ( !$checked ) {
					if ( preg_match ("/^[A-Za-z]/", $nickname)) {
						$isLoginName = true;
						break;
					}
					$checked = true;
				}
				$loginNames = array_merge( $loginNames, \Prj\Data\Manager::getLoginNameByNickName( $nickname ) );	
			}
			if ( $isLoginName ) {
				$loginNames = $nicknames;
			}
		}
		else {
			$loginNames = [$this->manager->getField('loginName')];
		}
        var_log($loginNames,'loginname.>>');
        foreach ($loginNames as $v){
			error_log( $v );
            $tmp = \Prj\Data\Manager::getCopy($v);
            $tmp->load();
            if($tmp->exists() && $tmp->getField('ec')){
                $ecUserIds[$tmp->getField('ec')] = $tmp->getField('ec');
            }
        }
        if(!$userIds && ( $this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root' ))$ecUserIds = '';
        $ret = \Prj\Data\EcData::getPhoneStat($ecUserIds , $startDate , $endDate );
        foreach ($ret as &$v){
            $record = \Prj\Data\Manager::loopFindRecords(['ec' => $v['userId']])[0];
            $v['loginName'] = $record['loginName'];
            $v['nickname'] = $record['nickname'];
        }
        return $ret;
    }
}
