<?php
namespace Prj\Tests;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/16
 * Time: 9:51
 */

class User {
	const errAccountNotExist = '账户不存在';
	protected $target;

	/**
	 * 删除用户
	 * @param string $phones   手机号，(多个手机号以英文逗号隔开)
	 * @param string  $cameFrom 登录来源
	 * @param string  $tables   需要删除的表名，多个用逗号隔开
	 * @return array
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function del($phones, $cameFrom = 'phone', $tables) {
		if (strpos($phones, ',') != false) {
			$phones = explode(',', $phones);
		} elseif (is_string($phones)) {
			$phones = [$phones];
		}

		if (strpos($tables, ',') != false) {
			$tables = explode(',', $tables);
		} elseif (is_string($tables)) {
			$tables = [$tables];
		}

		$this->target['cameFrom'] = $cameFrom;
		$succeed                  = [];
		$failed                   = [];
		foreach ($phones as $phone) {
			$this->target['phone'] = $phone;
			$dbAccountAlias        = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, $cameFrom]);
			$dbAccountAlias->load();

			if ($dbAccountAlias->exists()) {
				$this->target['accountId'] = $dbAccountAlias->getField('accountId');
			} else {
				$failed[] = $phone;
				throw new \Sooh\Base\ErrException(self::errAccountNotExist . '! succeed:' . json_encode($succeed) . '; failed:' . json_encode($failed));
			}

			try {
				foreach ($tables as $table) {
					error_log('====[del start] ' . ucfirst($table) . ' for phone:' . $phone);
					call_user_func(['\\Prj\\Tests\\User', 'delIn' . ucfirst($table)]);
					error_log('====[del end] ' . ucfirst($table) . ' for phone:' . $phone);
				}
				$succeed[] = $phone;
			} catch (\Exception $e) {
				$failed[] = $phone;
				throw new \Sooh\Base\ErrException($e->getMessage() . '! succeed:' . json_encode($succeed) . '; failed:' . json_encode($failed), $e->getCode());
			}
		}
		return $succeed;
	}

	/**
	 * 删除login_name表
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	protected function delInLogin() {
		$dbAccountAlias = \Sooh\DB\Cases\AccountAlias::getCopy([$this->target['phone'], $this->target['cameFrom']]);
		$dbAccountAlias->load();
		if ($dbAccountAlias->exists()) {
			$this->target['accountId'] = $dbAccountAlias->getField('accountId');
			$dbAccountAlias->delete();
		} else {
			error_log('----[not exists] ' . __FUNCTION__ . ' for phone: ' . $this->target['phone']);
//			throw new \Sooh\Base\ErrException(self::errAccountNotExist . __FUNCTION__);
		}
	}

	/**
	 * 删除Account表
	 * @throws \Sooh\Base\ErrException
	 */
	protected function delInAccount() {
		$dbAccountStorage = \Sooh\DB\Cases\AccountStorage::getCopy($this->target['accountId']);
		$dbAccountStorage->load();
		if ($dbAccountStorage->exists()) {
			$dbAccountStorage->delete();
		} else {
			error_log('----[not exists] ' . __FUNCTION__ . ' for phone: ' . $this->target['phone']);
//			throw new \Sooh\Base\ErrException(self::errAccountNotExist . __FUNCTION__);
		}
	}

	/**
	 * 删除User表
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	protected function delInUser() {
		$dbUser = \Prj\Data\User::getCopy($this->target['accountId']);
		$dbUser->load();
		if ($dbUser->exists()) {
			$this->target['myInviteCode'] = $dbUser->getField('myInviteCode');
			$dbUser->delete();
		} else {
			error_log('----[not exists] ' . __FUNCTION__ . ' for phone: ' . $this->target['phone']);
//			throw new \Sooh\Base\ErrException(self::errAccountNotExist . __FUNCTION__);
		}
	}

	/**
	 * 删除Invitecodes表
	 */
	protected function delInInvitecodes() {
		\Prj\Data\InviteCode::del($this->target['myInviteCode']);
	}
}