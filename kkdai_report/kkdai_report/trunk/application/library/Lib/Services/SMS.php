<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SMS
{
	protected static $_instance = null;

	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\SMS
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}

	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

	/**
	 * 发送验证码信息
	 * @param string $phone 手机号
	 * @param string $code 验证码
	 * @param string $fmt 消息的格式串
	 * @return string 信息 "ok"标示成功，“错误原因”
	 */
	public function sendCode($phone, $code = '', $fmt = '')
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $phone, 'code' => $code, 'fmt' => $fmt))->send(__FUNCTION__);
		} else {

			if (empty($code)) {
				$msg = $fmt;
			} else {
				$msg = str_replace('{code}', $code, $fmt);
			}

			$smsClass = '\Lib\SMS\\' . \Sooh\Base\Ini::getInstance()->get('SMSConf');
			$sys = new $smsClass;
//			return $sys->send($phone, $msg, \Prj\Consts\SMS::channel_regcode);
			return $sys->send($phone, $msg);
		}
	}

	/**
	 * 发送通知类消息
	 * @param string $phone 手机号
	 * @param string $msg 消息内容
	 */
	public function sendNotice($phone, $msg)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $phone, 'msg' => $msg))->send(__FUNCTION__);
		} else {

			$smsClass = '\Lib\SMS\\' . \Sooh\Base\Ini::getInstance()->get('SMSConf');
			$sys = new $smsClass;
//			return $sys->send($phone, $msg, \Prj\Consts\SMS::channel_notify);
			return $sys->send($phone, $msg);
		}
	}
}
