//File Name: perfdst.js
//
//Description:
//
//Author: token.tong
//
//Create date: 2017-03-24 14:45:12
//

var perfdst = {};
perfdst.s = ( function()
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
			
			onEdit : function ( json, tr )
			{
				if ( !_isValidType( json.type ) )
				{
					return ;
				}
				
				var cells = tr[0].cells;
				var last = cells.length - 1;
				var td = cells[last];
				var str = td.innerHTML;
				if ( -1 == str.indexOf( "<input" ) )
				{
					var name = _getInputTypeName( json.type );
		    		str = str + "<input type=\"checkbox\" name=\"" + name + "checkbox\" value=\"" + json._id + "\" />";
		    		td.innerHTML = str;
				}

		    	_showReply( json.type, true );
			},
		};

		//
		//here define private properties and functions.
		//

		//real initialize.
		var _doinit = function()
		{
		}
		
		var _isValidType = function( type )
		{
			return ( type >= 1 && type <= 4 );
		}
		
		var _getInputTypeName = function( type )
		{
			var name = 'none';
			switch( type )
			{
			case 1: 
				{
					name = "day_dst_";
				}
				break;
				
			case 2:
				{
					name = "week_dst_";
				}
				break;
				
			case 3:
				{
					name = "month_dst_";
				}
				break;
				
			case 4:				
				{
					name = "quarter_dst_";
				}
				break;
			
			default:
				{				
				}
				break;
			}		
			return name;
		}
		
		var _showReply = function( type, show )
		{
			var reply = document.getElementById( "_dst_reply_" + type );
			var trace = document.getElementById( "_dst_trace_" + type );
			var at = document.getElementById( "_dst_at_" + type );
			var prop = 'hidden';
			if ( show )
			{
				prop = 'visible';
			}
			if ( reply )
			{
				reply.style.visibility="visible";
			}
			if ( trace )
			{
				trace.style.visibility="visible";
			}
			if ( at )
			{
				at.style.visibility="visible";
			}
		}

	return ret;
})();
