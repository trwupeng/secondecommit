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
			$this->import( date( 'Y-m-d', strtotime($dt->YmdFull) ) );
		}
		else 
		{
			$dt0 = strtotime($dt->YmdFull);
			return $this->import( date('Y-m-d', $dt0-86400) );
        }
        return true;
    }

	protected function import($ymd)
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
		$records = $ec->getUserRecordsByComm( $userIds, $ymd, $ymd );
		if ( !$records )
		{
			var_log( 'getUserRecordsByComm failed', 'ec comm' );
			return false;
		}

		$ec->importRecords( $records );

		return true;
	}
}
