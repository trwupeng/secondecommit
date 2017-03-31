//File Name: perfmsg.js
//
//Description:
//
//Author: token.tong
//
//Create date: 2017-03-24 15:14:32
//

var perfmsg = {};
perfmsg.s = ( function()
{
	var ret = 
		{
			//
			//here define public properties and functions.
			//

			//initialize something.
			init : function()
			{
				_doinit();
			},
			
			refreshInit : function( url, param )
			{
				_refreshUrl = url;
				_refreshParam = param;
				perfmsg.s.showMsgNumAll( false );
				perfmsg.s.refresh();
			},
			
			showMsgNumAll : function( show )
			{
				var mainMsgNum = document.getElementById( "mainMsgNum" );
 				var subMsgNum = document.getElementById( "subMsgNum" );
 				_showMsgNum( mainMsgNum, show );
 				_showMsgNum( subMsgNum, show );
			},					
			
			refresh : function()
			{
				$.ajax({
					 type: "GET",
					 url: _refreshUrl + "/plan/message/msgnum",
					 async: true,
					 data: _refreshParam,
					 dataType: 'jsonp',
					 jsonp: 'callback',
					 success : function(result) 
					 		{
					 			callback( result );
					 		},
					error : function(result) 
							{
								callback( result );
							}
				});

				function callback( result ) 
				{
					if ( 200 == result.status )
					{
						var j = JSON.parse( result.responseText );
						if(j.result == 0)
						{
			 				var num = j.num;
			 				var mainMsgNum = document.getElementById( "mainMsgNum" );
			 				var subMsgNum = document.getElementById( "subMsgNum" );
			 				_showMsgNum( mainMsgNum, 0 != num );
			 				_showMsgNum( subMsgNum, 0 != num );
			 						
			 				if ( mainMsgNum )
			 				{
			 					mainMsgNum.innerHTML = num;
			 				}
			 				if ( subMsgNum )
			 				{
			 					subMsgNum.innerHTML = num;
			 				}
						}

					} 
					else 
					{
						console.log( 'get msg num failed:' + j );
					}
				}
			},
			
			markReaded : function( json )
			{
				perfmsg.s.refresh();
		        if ( json.statusCode == 200 )
		        {
		            var obj = document.getElementById('flagRead'+'_'+json.id);
		            if ( obj )
		            {
		            	obj.style.display='none';
		            }
		        }
		        else 
		        {
		        	console.log( 'markReaded failed:' + json );
		        }
			},
			
			markReadedForUnreadRecord : function(json)
			{
		    	perfmsg.s.refresh();
		        if ( json.statusCode == 200 ) 
		        {
		            var obj = document.getElementById('flagUnread'+'_'+json.id);
		            if ( obj )
		            {
		            	obj.style.display='none';
		            }
		            
		        }
		        else 
		        {
		        	console.log( 'markReadedForUnreadRecord:' + json )
		        }
		    },
			
		};

		//
		//here define private properties and functions.
		//

		//real initialize.
		var _doinit = function()
		{
		}
		
		var _showMsgNum = function( element, show )
		{
			if ( !element )
			{
				return ;
			}
			element.style.visibility = show ? 'visible' : 'hidden';
		}
		
		var _refreshUrl;
		var _refreshParam;

	return ret;
})();
