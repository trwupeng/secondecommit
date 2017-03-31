<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondEc&ymdh=20161220"
 * User: token.tong
 * Date: 2016/12/21
 */
namespace PrjCronds;
class CrondEc extends \Sooh\Base\Crond\Task {
    public function init() {
        parent::init();
        $this->toBeContinue=true;
        $this->_iissStartAfter=60;
        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt)
    {
        if ($this->_isManual) 
		{
			$s = date( 'Y-m-d', strtotime($dt->YmdFull) );
			$arr = explode( '-', $s );
			$this->importHistory( $arr[0], $arr[1] );
		}
		else 
		{
			$dt0 = strtotime($dt->YmdFull);
			return $this->import( date('Y-m-d', $dt0-86400), date('Y-m-d', $dt0-86400) );
        }
        return true;
    }

	protected function import($ymdStart, $ymdEnd)
	{
		$ec = new \Prj\Data\EcData;
		if ( !$ec->getAccessTokenByComm() )
		{
			var_log( 'getAccessTokenByComm failed', 'ec comm' );
			return false;
		}

		$users = $ec->getAllUsersByComm();
		if ( !$users )
		{
			var_log( 'getAllUsersByComm failed', 'ec comm' );
			return false;
		}


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
		$errorCount = 0;
		{
			$records = $ec->getUserRecordsByComm( $userIds, $ymdStart, $ymdEnd );
			if ( !$records )
			{
				var_log( 'getUserRecordsByComm failed', 'ec comm' );
				++$errorCount;
			}
			else
			{
				$ec->importRecords( $records );
			}
		}
		
		{
			$records = $ec->getUserPhoneRecordsByComm( $userIds, $ymdStart, $ymdEnd );
			if ( !$records )
			{
				var_log( 'getUserPhoneRecordsByComm failed', 'ec comm' );
				++$errorCount;
			}
			else 
			{
				$ec->importPhoneRecords( $records );
			}
		}

		return 0 == $errorCount;
	}
	
	protected function importHistory( $year, $mon )
	{
		$ec = new \Prj\Data\EcData;
		if ( !$ec->getAccessTokenByComm() )
		{
			var_log( 'getAccessTokenByComm failed', 'ec comm' );
			return false;
		}
	
		$users = $ec->getAllUsersByComm();
		if ( !$users )
		{
			var_log( 'getAllUsersByComm failed', 'ec comm' );
			return false;
		}
	
	
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
		$errorCount = 0;
		{
			$records = $ec->getUserHistoryRecordsByComm( $userIds, $year, $mon );
			if ( !$records )
			{
				var_log( 'getUserRecordsByComm failed', 'ec comm' );
				++$errorCount;
			}
			else
			{
				$ec->importRecords( $records );
			}
		}
	
		{
			$records = $ec->getUserHistoryPhoneRecordsByComm( $userIds, $year, $mon );
			if ( !$records )
			{
				var_log( 'getUserPhoneRecordsByComm failed', 'ec comm' );
				++$errorCount;
			}
			else
			{
				$ec->importPhoneRecords( $records );
			}
		}
	
		return 0 == $errorCount;
	}
}
