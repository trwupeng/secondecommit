<?php
namespace Prj;
/**
 * 需要用户登入的ctrl
 *     提供 this->user,但只检查了是否登入，没执行load()
 */
class UserCtrl  extends \Prj\BaseCtrl {

//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
	public function init()
	{
		parent::init();
		$this->onInit_chkLogin();
	}
	protected function onInit_chkLogin()
	{
		$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		if ($userIdentifier){
			\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
			$this->user = \Prj\Data\User::getCopy($userIdentifier);
		}else{
			throw new \ErrorException(\Prj\ErrCode::errNotLogin,401);
		}
	}

	/**
	 * 检查密码版本号
	 * @return bool true表示同步，false表示不同步需要重新登录
	 * @author LTM <605415184@qq.com>
	 */
	public function checkPwdVer() {
		$pwdVer = \Sooh\Base\Session\Data::getInstance()->get('pwdVer');
		try {
			$resource = (new \Prj\Oauth\Oauth())->getResource();
			if ($pwdVer == $resource['pwdVer']) {
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
			$this->loger->ret = $e->getMessage();
			$this->loger->ext = $e->getCode();
			return false;
		}
	}


    /**
     * 返回用户的可用券
     * By Hand
     */
    protected function _myVouchers($waresId='',$voucherType = [],$orderBy = '')
    {
        if(empty($orderBy))$orderBy = 'rsort timeCreate';
        $redPacket = [];
        $voucher = [];
        $this->user->load();
        $userId = $this->user->userId;
        $user = $this->user;
        $o = \Prj\Data\Vouchers::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $where = [
            'userId'=>$userId,
            'dtExpired]'=>\Sooh\Base\Time::getInstance()->ymdhis(),
            'statusCode'=>\Prj\Consts\Voucher::status_unuse,
        ];
        if(!empty($voucherType))
        {
            $where['voucherType'] = $voucherType;
        }
        $rs = $db->getRecords($tb,'*',$where,$orderBy);
        foreach($rs as $k=>$v)
        {
            $limit = [
                'limitsShelf'=>$v['limitsShelf'],
                'limitsType'=>$v['limitsType'],
                'limitsTag'=>$v['limitsTag'],
                'limitsAmount'=>$v['limitsAmount'],
                'limitsDeadline'=>$v['limitsDeadline'],
            ];
            try{
                if(!empty($waresId) && !\Prj\Data\Vouchers::checkLimit($waresId,$limit))
                {
                    unset($rs[$k]);
                    continue;
                }
            }catch (\ErrorException $e){
                unset($rs[$k]);
                continue;
            }

            if($v['voucherType']==\Prj\Consts\Voucher::type_real)
            {
                $redPacket[] = $v;
            }
            else
            {
                $voucher[] = $v;
            }
        }
        $new['redPacketList'] = $redPacket;
        $new['voucherList'] = $voucher;
        return $new;
    }

    /**
     * 验证支付密码
     * By Hand
     */
    protected function _checkPaypwd($paypwd)
    {
        $user = $this->user;
        $user->load();
        $tradePwd = $user->getField('tradePwd');
        $salt = $user->getField('salt');
        if($tradePwd===md5($paypwd.$salt))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	/**
	 *
	 * @var \Prj\Data\User
	 */
	protected $user=null;
	
}
