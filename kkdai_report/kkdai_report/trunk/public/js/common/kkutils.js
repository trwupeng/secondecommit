/*
 * file name: kkutils.js
 * 
 * desc: 静态工具类
 * 
 * author: token.tong
 * 
 * create date: 2017/03/22
 */

var kkutils = {};

kkutils.s = ( function ()
{
	var ret = 
		{
			//删除左右两端的空格
			trim : function( str )
			{
				return str.replace(/(^\s*)|(\s*$)/g, "");
			},
			
			//删除左边的空格
			ltrim : function(str)
			{ 
	　　     	return str.replace(/(^\s*)/g,"");
	　　 	},
	   	
			//删除右边的空格
	　　 	rtrim : function(str)
	   		{ 
	　　     	return str.replace(/(\s*$)/g,"");
	　　 	},
		};
	
	return ret;
})();