<?php
namespace Lib\SMS;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/16
 * Time: 17:37
 */

class ChuangLan {
	//创蓝发送短信接口URL, 如无必要，该参数可不用修改
	const API_SEND_URL = 'http://222.73.117.158/msg/HttpBatchSendSM';
	//创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
	const API_BALANCE_QUERY_URL = 'http://222.73.117.158/msg/QueryBalance';
	//创蓝账号
//	const API_ACCOUNT = 'zhenxuan1';
	//创蓝密码
//	const API_PASSWORD = 'Aa123456';


	//创蓝账号
	const API_ACCOUNT = 'kuaidai_m';
	//创蓝密码
	const API_PASSWORD = 'Kuaidai123';
	/**
	 * 发送短信
	 * @param  string  $phone      手机号码
	 * @param   string $msg        短信内容
	 * @param string   $product    产品id,可选
	 * @param string   $needstatus 是否需要状态报告
	 * @param string   $extno      扩展码,可选
	 * @return string
	 * @throws \Sooh\Base\ErrException
	 */
	public function send($phone, $msg, $product = '314754257', $needstatus = 'true', $extno = '043914') {
		//创蓝接口参数
		$postArr = [
			'account' => self::API_ACCOUNT,
			'pswd' => self::API_PASSWORD,
			'msg' => $msg,
			'mobile' => $phone,
			'product' => $product,
			'needstatus' => $needstatus,
			'extno' => $extno,
		];
var_log($postArr, 'postArr>>>>>>>>>>>');
		$result = $this->curlPost(self::API_SEND_URL, $postArr);
		if (is_numeric($result[1]) && $result[1] == 0) {
			return 'success';
		} else {
			var_log($result);
			throw new \Sooh\Base\ErrException('发送失败');
		}
	}

	/**
	 * 查询额度
	 */
	public function queryBalance() {
		$postArr = array('account' => self::API_ACCOUNT, 'pswd' => self::API_PASSWORD);
		$result  = $this->curlPost(self::API_BALANCE_QUERY_URL, $postArr);
		return $this->execResult($result);
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url        //请求URL
	 * @param array  $postFields //请求参数
	 * @return mixed
	 */
	private function curlPost($url, $postFields) {
		$postFields = http_build_query($postFields);
		$ch         = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		$result = curl_exec($ch);
		curl_close($ch);
		return $this->execResult($result);
	}

	/**
	 * 处理返回值
	 */
	private function execResult($result) {
		$result = preg_split("/[,\r\n]/", $result);
		return $result;
	}
}