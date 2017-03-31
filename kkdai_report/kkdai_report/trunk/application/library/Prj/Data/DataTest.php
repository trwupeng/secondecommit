<?php
/**
 * File Name: DataTest.php
 *
 * Description:
 *
 * Author: token.tong
 *
 * Create data: 2017-03-29 14:45:37
 *
*/

namespace Prj\Data;

class DataTest
{
	public static function test()
	{
		self::testProject();	
	}
	
	public static function testProject()
	{
//		ProjectData::add('测试项目1', '2017-03-29', '2017-04-01', '这是一个测试项目', ['tongyifeng','wangyali','wupeng'], ['tongyifeng'] );
		var_log( ProjectData::getCount(), 'get' );
	}
}
