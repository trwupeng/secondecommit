<?php
namespace Prj\Data;
/**
 * Description of Summary
 *
 * @author simon.wang
 */
class Summary {
	/**
	 * 首页用的累计订单，产品，收益等信息
	 * @return array ("orders"=>123123,"wares"=>234,"interest"=>12314123123.12)
	 */
	public static function homepage()
	{
		return ["orders"=>123123,"wares"=>234,"interest"=>12314123123.12];
	}
}
