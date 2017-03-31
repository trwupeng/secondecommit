<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class EcData extends BaseFK {
    protected static $_pk = 'id'; //主键
    protected static $_host = 'manage'; //配置名

    protected static $_tbname = "db_kkrpt.tb_ecRecord"; //表名

	//protected static $_baseUrl = 'http://www.kuaikuaidai.com';
	protected static $_baseUrl = 'https://open.workec.com';
	protected static $_appId = '158964799969951744';
	protected static $_appSecret = 'hg4QOAtNh6HLaBp1EA1';
	protected static $_corpId = '4016360';
	protected $_accessToken = null;


	//获取accessToken
	public function getAccessTokenByComm()
	{
		$postData = array( 'appId' => self::$_appId,
						'appSecret' => self::$_appSecret );

		$ret = $this->postData( '/auth/accesstoken', $postData );
		$arr = json_decode($ret,true);
		if ( 200 == $arr['errCode'] )
		{
			$this->_accessToken = $arr['data']['accessToken'];
			error_log('token>>>'.$this->_accessToken);
			return true;
		}

		error_log( 'getAccessToken failed:' . $arr['errCode'] );
		return false;
	}

	//获取所有的ec用户
	public function getAllUsersByComm()
	{
		$ret = $this->postData( '/user/structure', '' );
		$arr = json_decode( $ret, true );
		//var_log( $arr, 'users' );
		if ( 200 != $arr['errCode'] )
		{
			error_log( 'getAllUsers failed:' . $arr['errCode'] );
			return false;
		}
		return $arr['data']['users'];
	}

	//获取用户的ec跟进记录
	public function getUserRecordsByComm( $userIds, $startDate, $endDate )
	{
		$postData = array( 'userIds' => $userIds,
							'startDate' => $startDate,
							'endDate' => $endDate );
		$curPage = 1;
		$maxPage = 0;
		$arr = [];
		do
		{
			$postData['pageNo'] = $curPage;
			$ret = $this->postData( '/trajectory/findUserTrajectory', $postData );
			$o = json_decode( $ret, true );
			if ( 200 != $o['errCode'] )
			{
			    var_log($o,'error>>>');
				return false;
			}
			$data = $o['data'];
			$maxPage = $data['maxPageNo'];
			$curPage = $data['pageNo'] + 1;
			$arr = array_merge( $arr, $data['result'] );
		} while( $curPage <= $maxPage );
		
		return $arr;
	}
	
	//获取用户的ec历史跟进记录
	public function getUserHistoryRecordsByComm( $userIds, $year, $mon )
	{
		$postData = array( 'userIds' => $userIds,
				'year' => (int)$year,
				'month' => (int)$mon );
		$curPage = 1;
		$maxPage = 0;
		$arr = [];
		do
		{
			$postData['pageNo'] = $curPage;
			$ret = $this->postData( '/trajectory/findHistoryUserTrajectory', $postData );
			$o = json_decode( $ret, true );
			if ( 200 != $o['errCode'] )
			{
				var_log($o,'error>>>');
				return false;
			}
			$data = $o['data'];
			$maxPage = $data['maxPageNo'];
			$curPage = $data['pageNo'] + 1;
			$arr = array_merge( $arr, $data['result'] );
		} while( $curPage <= $maxPage );
	
		return $arr;
	}
	
	//获取用户的ec电话记录
	public function getUserPhoneRecordsByComm( $userIds, $startDate, $endDate )
	{
		$postData = array( 'userIds' => $userIds,
							'startDate' => $startDate,
							'endDate' => $endDate );
		$curPage = 1;
		$maxPage = 0;
		$arr = [];
		do
		{
			$postData['pageNo'] = $curPage;
			$ret = $this->postData( '/record/telRecord', $postData );
			$o = json_decode( $ret, true );
			if ( 200 != $o['errCode'] )
			{
			    var_log($o,'error>>>');
				return false;
			}
			$data = $o['data'];
			$maxPage = $data['maxPageNo'];
			$curPage = $data['pageNo'] + 1;
			$arr = array_merge( $arr, $data['result'] );
		} while( $curPage <= $maxPage );
		
		return $arr;
	}
	
	//获取用户的ec电话历史记录
	public function getUserHistoryPhoneRecordsByComm( $userIds, $year, $mon )
	{
		$postData = array( 'userIds' => $userIds,
				'year' => (int)$year,
				'month' => (int)$mon );
		$curPage = 1;
		$maxPage = 0;
		$arr = [];
		do
		{
			$postData['pageNo'] = $curPage;
			$ret = $this->postData( '/record/telRecordHistory', $postData );
			$o = json_decode( $ret, true );
			if ( 200 != $o['errCode'] )
			{
				var_log($o,'error>>>');
				return false;
			}
			$data = $o['data'];
			$maxPage = $data['maxPageNo'];
			$curPage = $data['pageNo'] + 1;
			$arr = array_merge( $arr, $data['result'] );
		} while( $curPage <= $maxPage );
	
		return $arr;
	}

	//统计跟进记录的id获取一条跟进记录
	public function getRecordById( $id )
	{
		return $this->fetchOne( array( 'id' => $id ) );
	}
	
	//获取用户的所有跟进记录
	public function getRecordsByUserId( $userId, $startDate = null, $endDate = null )
	{
		return $this->fetchAll( $this->appendDateCond( array( 'userId' => $userId ), $startDate, $endDate ));
	}

	//获取某个客户的所有跟进记录
	public function getRecordsByCustomerId( $customerId, $startDate = null, $endDate = null )
	{
		return $this->fetchAll( $this->appendDateCond( array( 'customerId' => $customerId ), $startDate, $endDate ) );
	}

	//将ec跟进记录存入本地数据库
	public function importRecords( $records )
	{
		$db = \Sooh\DB\Broker::getInstance( \Rpt\Tbname::db_rpt );
		foreach( $records as $r )
		{
			$t = $r['contactTime'];
			$n = strpos($t, '.');
			if ( $n )
			{
				$t = substr( $t, 0, $n );
			}
			$data = array( 'id' => '' . $r['md5'],
						'contactTime' => '' . $t,
						'customerId' => '' . $r['crmId'],
						'customerName' => '' . $r['customer'],
						'customerCompany' => '' . $r['customerCompany'],
						'userId' => '' . $r['userId'],
						'content' => '' . $r['content'] );
			try{
                $db->addRecord( self::$_tbname, $data );
            }catch (\ErrorException $e){
			    error_log($r['md5'].'#exists#');
            }
		}
	}
	
	//将ec跟进记录存入本地数据库
	public function importPhoneRecords( $records )
	{
		$db = \Sooh\DB\Broker::getInstance( \Rpt\Tbname::db_rpt );
		foreach( $records as $r )
		{
			$data = array( 'id' => $r['starttime'] . '_' . $r['calltono'], 
					'contactTime' => $r['starttime'],
					'phone' => '' . $r['calltono'],
					'customerId' => '' . $r['crmId'],
					'calltime' => $r['calltime'],
					'type' => $r['type'],
					'customerName' => '' . $r['customerName'],
					'customerCompany' => '' . $r['customerCompany'],
					'userId' => '' . $r['userId'],
					'url' => '' . $r['path'] );
			try{
				$db->addRecord( 'db_kkrpt.tb_ecPhoneRecord', $data );
			}catch (\ErrorException $e){
				
			}
		}
	}

	//拼接日期的查询条件
	protected function appendDateCond( $where, $startDate, $endDate )
	{
		if ( null != $startDate )
		{
			$where['contactTime>='] = $startDate;
		}
		if ( null != $endDate )
		{
			$where['contactTime<='] = $endDate;
		}
		return $where;
	}

	//查询多条记录
	protected function fetchAll( $where )
	{
		$db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
		return $db->getRecords( self::$_tbname, '*', $where );
	}

	//查询一条记录
	protected function fetchOne( $where )
	{
		$db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
		return $db->getRecord( self::$_tbname, '*', $where );
	}

	//向ec请求消息
	protected function postData( $uri, $postData )
	{
		$header = array( 'Accept: application/json',
						'CORP-ID: ' . self::$_corpId,
						'Content-Type: application/json; charset=UTF-8' );
		if ( null != $this->_accessToken )
		{
		    error_log('_accessToken:'.$this->_accessToken);
			$header[] = 'Authorization: ' . $this->_accessToken;
		}
		var_log( self::$_baseUrl . $uri, 'url' );
		var_log( $postData, 'postData' );
        return \Prj\Tool\Func::request(self::$_baseUrl . $uri , json_encode($postData) , 'POST' , false , $header);
		//return \Prj\Tool\Func::curl_post( self::$_baseUrl . $uri, json_encode($postData), 30, $header );
	}

	protected function test_import()
	{
		error_log( 'getAccessToken..' );
		if ( !$this->getAccessTokenByComm() )
		{
			return false;
		}

		error_log( 'getAllUsers..' );
		$users = $this->getAllUsersByComm();
		if ( !$users )
		{
			return false;
		}
		
		var_log( $users, 'enum users' );
		$userIds = '';
		$first = true;
		foreach( $users as $v )
		{
			if ( !$first )
			{
				$userIds = $userIds . ';';
			}
			else
			{
				$first = false;
			}
			$userIds = $userIds . $v['userId'];
		}
		$records = $this->getUserRecordsByComm( $userIds, '2016-12-10', '2016-12-20' );
		$this->importRecords( $records );
	}
	protected function test_query()
	{
	//	$r = $this->getRecordById( '6c7f8964091f518db6cb05db8498984c' );
	//	$r = $this->getRecordsByUserId( '4016973', '2016-12-16 00:00:00', '2016-12-19 00:00:00' );
		$r = $this->getRecordsByCustomerId( '500970236', '2016-12-10', '2016-12-21' );
		print_r( $r );
	}

	public function test()
	{
//		$this->test_import();
		$this->test_query();
	}

	public static function getDetailList( $userId, $start, $end ) {
	    $userStr = $userId ? "and userId = '" . $userId . "'" : "";
        $startStr = $start ? "and contactTime >= '".date('Y-m-d',strtotime($start))." 00:00:00'" : "";
        $end = $end ? $end : $start;
        $endStr = $end ? "and contactTime <= '".date('Y-m-d',strtotime($end))." 24:00:00'" : "";
	    $sql = <<<sql
select contactTime , customerName, content from tb_ecRecord 
where 
1=1
$startStr
$endStr 
$userStr
order by contactTime desc
sql;
		return self::query( $sql );
	}
	
	public static function getPhoneDetailList( $userId, $start, $end ) {
		$userStr = $userId ? "and userId = '" . $userId . "'" : "";
		$startStr = $start ? "and contactTime >= '".date('Y-m-d',strtotime($start))." 00:00:00'" : "";
		$end = $end ? $end : $start;
		$endStr = $end ? "and contactTime <= '".date('Y-m-d',strtotime($end))." 24:00:00'" : "";
		$sql = <<<sql
select id, contactTime , customerId, customerName, calltime, url from tb_ecPhoneRecord
where
1=1
$startStr
$endStr
$userStr
order by contactTime desc
sql;
		return self::query( $sql );
	}
	
	public static function getPhoneUrl( $id )
	{
		$sql = <<<sql
select url from tb_ecPhoneRecord
where
id = '$id'
sql;
		return self::query( $sql );
	}
	
	//客户是否有反馈记录
	public static function getCustomerRecordNum( $customerId ) 
	{
		$sql = <<<sql
select count(*) as total from tb_ecRecord
where
'customerId'='$customerId'
sql;
		return self::query($sql);
	}
	
	//获取客户的反馈记录
	public static function getCustomerRecord( $customerId )
	{
		$sql = <<<sql
select contactTime, customerId, customerName, userId, content from tb_ecRecord
where
'customerId'='$customerId'
order by contactTime desc
sql;
		return self::query($sql);
	}

	public static function getStat($userIds = [] , $start = '' , $end = '' ){
        $userIds = ($userIds === []) ? [0] : $userIds;
	    $userStr = $userIds ? "and userId in (".implode(',',$userIds).")" : "";
        $startStr = $start ? "and contactTime >= '".date('Y-m-d',strtotime($start))." 00:00:00'" : "";
        $end = $end ? $end : $start;
        $endStr = $end ? "and contactTime <= '".date('Y-m-d',strtotime($end))." 24:00:00'" : "";
	    $sql = <<<sql
select userId , count(1) as total from tb_ecRecord 
where 
1=1
$startStr
$endStr 
$userStr
GROUP BY userId
sql;
        return self::query($sql);
    }
    
    public static function getPhoneStat($userIds = [] , $start = '' , $end = '' ){
    	$userIds = ($userIds === []) ? [0] : $userIds;
    	$userStr = $userIds ? "and userId in (".implode(',',$userIds).")" : "";
    	$startStr = $start ? "and contactTime >= '".date('Y-m-d',strtotime($start))." 00:00:00'" : "";
    	$end = $end ? $end : $start;
    	$endStr = $end ? "and contactTime <= '".date('Y-m-d',strtotime($end))." 24:00:00'" : "";
    	$sql = <<<sql
select userId , count(1) as total from tb_ecPhoneRecord
where
1=1
$startStr
$endStr
$userStr
GROUP BY userId
sql;
    	return self::query($sql);
    }
}
