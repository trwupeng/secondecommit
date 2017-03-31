<?php
namespace Prj;
/**
 * 错误描述
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ErrCode extends \Exception{
	const errNotLogin = '未登入、超时或没有权限，请登入';
	const errNoRights = '没有权限';
	const errCommonError='服务器忙，请稍候再试';
	const errItemBelongSomeone='道具已经分配过了';
	const errMaintainTime='系统当前处于维护期间，请稍后再试';
	const errAccountError='账号异常，请联系客服';
	public function __construct($message, $code=400, $previous=null) {
		parent::__construct($message, $code, $previous);
	}
}
