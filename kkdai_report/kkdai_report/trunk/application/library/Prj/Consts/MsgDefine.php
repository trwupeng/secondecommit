<?php
namespace Prj\Consts;
/**
 * 文字定义
 * @author simon.wang
 */
class MsgDefine {
	static $define = [
		'db_error'                    => '服务器忙，请稍候重试',
		'success'                     => '成功',
		'error'                       => '错误',
		'invalidcode_incorrect'       => '验证码不正确或已经超时',//即将废弃
		'smsCode_incorrect'           => '验证码不正确或已经超时',
		'request_parameter_incorrect' => '请求参数不正确',
		'page_expired'                => '页面数据过期，请刷新重试',
		'send_success'                => '发送成功',
	];
}
