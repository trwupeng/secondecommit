<?php
namespace Prj\Consts;
/**
 * 客户端类型
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ClientType {
	/**
	 * pc网站
	 */
	const www = 900; 
	/**
	 * 苹果
	 */
	const appstore=901;
	/**
	 * 安卓
	 */
	const android=902;
	/**
	 * m端
	 */
	const wap=903;
	/**
	 * 微信（保留）
	 */
	const weixin=904;

	public static function clientTypes ($id=null) {
		$r = array(
				self::www=>'PC端',
				self::appstore=>'IOS',
				self::android=>'安卓',
				self::wap =>'m端',
				self::weixin=>'微信'
			);
		if($id===null){
			return  $r;
		}else{
			return $r[$id];
		}
	}
}
