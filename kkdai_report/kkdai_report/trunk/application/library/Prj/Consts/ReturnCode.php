<?php
namespace Prj\Consts;
/**
 * 操作返回的code的值
 *
 * @author simon.wang
 */
class ReturnCode {
	const ok = 200; //正常结束
	const clientError = 400;//一般错误，通常由客户端参数原因造成
	const internalError = 500;//系统内部错误
	const notLogin = 401;//没有登入
	const notBind = 402;//没有绑卡
	const walletOut = 403;//余额不足，请去充值
	const userLocked = 501;//账户状态不对，比如已经被锁定
	const recordLocked = 502;//账户状态不对，比如已经被锁定
	const dbError=505;//数据库操作异常，比如插入新纪录失败，磁盘满了
}
