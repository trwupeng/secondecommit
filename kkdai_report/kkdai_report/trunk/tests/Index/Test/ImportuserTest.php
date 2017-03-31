<?php
namespace Tests\Index\Test;

include realpath(__DIR__ . '/../../../conf') . '/inc4tests.php';

/**
 * 每次执行，自动注册-绑卡。。。100个用户
 * 用户规则：[]
 * Class ImportuserTest
 * @package Tests\Index\Test
 * @author LTM <605415184@qq.com>
 */
class ImportuserTest extends \Sooh\Base\Tests\ApiHttpGetJson {
	const CLIENT_ID = '1104878344';
	const CLIENT_SECRET = 's20vH9emKJ6BmT1Q';
	const REDIRECT_URI = 'https://www.baidu.com/';

	public function testDefault() {
		for ($i = 0; $i < 1; $i++) {
			$phone = '1330000' . sprintf('%04d', $i + 1);
			$cardId = '62220213040' . sprintf('%08d', mt_rand(0, 99999999));
			$params = [
				'phone'        => $phone,
				'invalidCode'  => 123456,
				'password'     => 't111111',
				'contractId'   => '111',
				'clientType'   => '222',
				'clientId'     => self::CLIENT_ID,
				'clientSecret' => self::CLIENT_SECRET,
				'redirectUri'  => self::REDIRECT_URI,
				'protocol'     => '2',
				'__VIEW__'     => 'json',
			];
			$ret = json_decode($this->jsonstrByHttpGet($this->getUrl('oauth/appreg', $params)), true);
			if ($ret['code'] == 200) {
				$loginParams = [
					'code' => $ret['info']['code'],
				    'redirectUri' => $ret['info']['redirectUri'],
				    '__VIEW__' => 'json',
				];
				$retLogin = json_decode($this->jsonstrByHttpGet($this->getUrl('passport/login', $loginParams)), true);
				if ($retLogin['code'] == 200) {
					$bandParams = [
						'bankId' => $this->getBank($phone),
					    'bankCard' => $cardId,
					    'realName' => '吕子桐',
					    'phone' => $phone,
					    'idCardType' => 1,
					    'idCardSn' => '410327198905262000',
					    'cmd' => 'binding',
					];
					$retBinding = json_decode($this->jsonstrByHttpGet($this->getUrl('user/bindcard', $bandParams)), true);
					if ($retBinding['code'] == 200) {
						$bankcodeParams = [
							'cmd' => 'bindingcode',
						    'smsCode' => 123456,
						    'ticket' => $retBinding['retAll']['got']['ticket'],
						];
						$retBindingCode = json_decode($this->jsonstrByHttpGet($this->getUrl('user/bindcard', $bankcodeParams)), true);
						if ($retBindingCode['code'] == 200) {
							continue;
						} else {
							//TODO log
							error_log(111);
						}
					} else {
						//TODO log
						error_log(222);
					}
				} else {
					//TODO log
					error_log(333);
				}
			} else {
				//TODO log
				error_log(444);
			}

		}
	}

	/**
	 * 获取银行缩写
	 * @param $phone
	 * @return string
	 */
	private function getBank($phone) {
		$num = substr($phone, -4);
		if ($num < 11) {
			$bank = 'abc';
		} elseif ($num < 21) {
			$bank = 'icbc';
		} elseif ($num < 31) {
			$bank = 'ccb';
		} elseif ($num < 41) {
			$bank = 'cmb';
		} elseif ($num < 51) {
			$bank = 'boc';
		} elseif ($num < 61) {
			$bank = 'comm';
		} elseif ($num < 71) {
			$bank = 'psbc';
		} elseif ($num < 81) {
			$bank = 'cib';
		} elseif ($num < 91) {
			$bank = 'spdb';
		} else {
			$bank = 'cmbc';
		}
		return $bank;
	}
}