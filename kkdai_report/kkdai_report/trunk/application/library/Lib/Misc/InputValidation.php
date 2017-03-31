<?php
namespace Lib\Misc;
	/**
	 * Created by PhpStorm.
	 * User: LTM <605415184@qq.com>
	 * Date: 2015/10/13
	 * Time: 11:24
	 */

/**
 * 字段验证类，可用于表单字段验证等
 * Class InputValidation
 * @package Lib\Misc
 */
class InputValidation {
	/**
	 * 常用字段-正则表达式
	 * phone
	 * password
	 * accountId
	 * contractId
	 * clientType
	 * clientId
	 * clientSecret
	 * accessToken
	 * refreshToken
	 * code
	 * cameFrom
	 * @var array
	 */
	static $define = [
		'phone'          => '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',//手机号
		'password'       => '#^[0-9a-zA-Z]{5,25}$#',//密码
		'accountId'      => '#^\d{14}$#',//帐号
		'contractId'     => '#^\w+$#',//渠道ID
		'clientType'     => '#^[1-9]\d{2}$#',
		'invitationCode' => '#^[0-9A-Z]{7}$#',//邀请码
		'clientId'       => '#^\d{10}$#',//clientId
		'clientSecret'   => '#^[a-zA-Z0-9]{16}$#',//clientSecret
		'accessToken'    => '#^\w{32}$#',//accessToken
		'refreshToken'   => '#^[a-z0-9]{32}$#',//refreshToken
		'code'           => '#^\w{32}$#',//code
		'cameFrom'       => '#^[a-zA-Z]{3,10}$#',
		'nickname'       => '/^[\x{4e00}-\x{9fa5}]{1,5}$/u',
	];

	/**
	 * 错误讯息
	 * @var string
	 */
	static $errorMsg = '字段不正确';
	/**
	 * 错误返回码
	 * @var int
	 */
	static $errorCode = 400;

	/**
	 * 用正则表达式验证表单数据
	 *      $params ['param1' => $a, 'param2' => $b]
	 *      $rules ['param1' => ['regExp1', 'errorMsg', 'errorCode'], 'param2' => ['regExp2', 'errorMsg', 'errorCode']]
	 * @param array $params 表单数据
	 * @param array $rules  规则数组,出现在rules中的字段必须验证
	 * @return bool 原样返回或者false
	 */
	static function validateParams($params, $rules) {
		if (is_array($params) && is_array($rules)) {
			foreach ($rules as $_k => $_v) {
				if (isset($params[$_k])) {
					if (preg_match($_v[0], $params[$_k]) == 0) {
						if ($_v[2] === null || !isset($_v[2])) {
							if (!empty($_v[1])) {
								self::$errorMsg = $_v[1];
								if (!empty($_v[2]) && is_numeric($_v[2])) {
									self::$errorCode = $_v[2];
								}
							}
							return false;
						} else {
							$params[$_k] = $_v[2];
						}
					}
				} else {
					self::$errorMsg = $_v[1] ? : '参数不合法';
					return false;
				}
			}
			return $params;
		} else {
			self::$errorMsg = '待验证参数类型不正确';
			return false;
		}
	}
}