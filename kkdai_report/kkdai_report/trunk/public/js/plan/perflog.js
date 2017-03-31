//File Name: perflog.js
//
//Description:
//
//Author: token.tong
//
//Create date: 2017-03-22 10:18:28
//

var perflog = {};
perflog.s = ( function()
{
	var ret = 
		{
			//
			//here define public properties and functions.
			//
			
			init : function()
			{
				_doinit();
			},
			
			//目标类型改变时的回调
			onType : function( element )
			{
				//获取当前控件的名字
				var curTypeName = element.getAttribute( 'name' );
				
				//获取目标的类型
				var dstType = element.options[element.selectedIndex].getAttribute( 'value' );
				
				console.log( 'dstType:' + dstType );
				
				//获取对应类型的目标列表
				var dstList = _nameList[dstType];
				
				//获取目标名的select和ul
				var ret = _getDstCtrlByTypeCtrlName( curTypeName );
				var dstSelect = ret[0];
				var dstUl = ret[1]; 
				
				console.log( 'dstSelect:' + dstSelect );
				console.log( 'dstUl:' + dstUl );
				
				//清除就控件
				_clearSelect( dstSelect );
				_clearUl( dstUl );
				
				ret = _getOtherElements( dstSelect );
				var button = ret[0];
				var span = ret[1]; 
				
				//添加新数据
				var index = 0;
				for ( var k in dstList )
				{
					if ( 'string' != typeof dstList[k] )
					{
						//不是属性
						continue;
					}
					
					dstSelect.add( _createOption( k, dstList[k] ) );
					dstUl.appendChild( _createLi( dstList[k], index ) );
					
					if ( 0 == index )
					{
						button.title = dstList[k];
						span.innerText = dstList[k];
					}
					
				/*
					var div = document.createElement( 'div' );
					div.innerHTML = '<div class="btn-group bootstrap-select show-tick" style="position:absolute;">' + dstList[k] + '</div>';
					console.log( 'width:' + div.offsetWidth );
				*/
					++index;
				}
				
				
				button.parentNode.style.width = '80%';
				
				dstUl.parentNode.style.width = '100%';
				
				dstSelect.selectedIndex = 0;
			},
			
			//目标改变时的回调
			onName : function( element )
			{
				
			},
			
			//设置目标列表
			setDstList : function( dstList )
			{
				_nameList = dstList;
			},
			
			//添加日志前的判定
			onBeforeEdit : function( tr )
			{
				var td = _getDstTd( tr[0] );
				console.log( td );
				
				//获取select
				var select = td.getElementsByTagName( 'select' )[0];
				var name = select.getAttribute( 'name' );
				
				//获取与select关联的ul
				var ul = document.getElementsByName( '_' + name + '_' )[0];
				var lis = ul.getElementsByTagName( 'li' );
				
				//获取选中的所以
				var selectIndex = 0;
				for ( var i=0; i<lis.length; ++i )
				{
					var classProp = lis[i].getAttribute( 'class' );
					if ( classProp && 'selected' == classProp )
					{
						selectIndex = parseInt( lis[i].getAttribute( 'data-original-index' ) );
					}
				}
				
				var option = select.options[selectIndex];
				var value = parseInt( option.getAttribute( 'value' ) );
				
				if ( 0 == value )
				{
					$(document).alertmsg( 'error', '请选择对应的目标' );
					return false;
				}
				return true;
			}, 
			
			//表格转换为可编辑状态
			onEditEnabled : function ( table, tr, rowNum )
			{
				if ( null == tr )
				{
					if ( null == table || null == rowNum )
					{
						return ;
					}
					
					table = table.closest( 'table' );
					table = table.get(0);
					var tbody = table.getElementsByTagName( 'tbody' )[0];
					var trs = tbody.getElementsByTagName( 'tr' );
					var num = rowNum;
					for ( var i=trs.length-1; i>=0&&num>0; --i, --num )
					{
						_setDstTdWidth( trs[i] );
					}
				}
				else
				{
					tr = tr.closest( 'tr' );
					tr = tr.get(0);
					_setDstTdWidth( tr );
				}
				
				
			}
			
		};

		//
		//here define private properties and functions.
		//
	
		var _doinit = function()
		{
			_initTable();
		}
		
		var _initTable = function()
		{
			var table = document.getElementById( 'perflog_table_id' );
			if ( !table )
			{
				return ;
			}
			
			var tbody = table.getElementsByTagName( 'tbody' )[0];
			var trs = tbody.getElementsByTagName( 'tr' );
			console.log( 'tr num:' + trs.length );
			
		
			for ( var i=0; i<trs.length; ++i )
			{
				_initDstTd( trs[i] );
			}
		
		}
		
		var _initDstTd = function( tr )
		{
			var tdType = _getTypeTd( tr );
			if ( !tdType )
			{
				return ;
			}
			var type = parseInt( tdType.getAttribute( 'data-val' ) );
			var dstList = _nameList[type];
			var ret = _getDstTdArray( tr );
			
			console.log( 'ret:' + ret );
			
			var id = ret['td'].getAttribute( 'data-val' );
			if ( !id )
			{
				return ;
			}
			
			_clearSelect( ret['select'] );
			_clearUl( ret['ul'] );
			
			var index = 0;
			for ( var k in dstList )
			{
				if ( 'string' != typeof dstList[k] )
				{
					//不是属性
					continue;
				}
				
				ret['select'].add( _createOption( k, dstList[k] ) );
				var li = _createLi( dstList[k], index );
				ret['ul'].appendChild( li );
				
				if ( k == id )
				{
					ret['button'].title = dstList[k];
					ret['span'].innerText = dstList[k];
					li.setAttributeNode( _createAttr( 'class', 'selected' ) );
				}
				++index;
			}
			
		}
		
		var _getTypeTd = function( tr )
		{
			tds = tr.children;
			for ( var i=0; i<tds.length; ++i )
			{
				var key = tds[i].getAttribute( 'data-key' );
				if ( key && 'type' == key )
				{
					return tds[i];
				}
			}
			
			return null;
		}
		
		//获取目标列表的单元格
		var _getDstTdArray = function( tr )
		{
			console.log( 'tr:' + tr );
			var td = _getDstTdWithHtml( tr );
			console.log( 'td:' + td );
			
			//获取select
			var select = td.getElementsByTagName( 'select' )[0];
			var name = select.getAttribute( 'name' );
			
			//获取与select关联的ul
			var ul = document.getElementsByName( '_' + name + '_' )[0];
			
			var button = td.getElementsByTagName( 'button' )[0];
			var spans = button.getElementsByTagName( 'span' );
			var span = null;
			for ( var i=0; i<spans.length; ++i )
			{
				span = spans[i];
				if ( span.innerText.length > 0 )
				{
					break;
				}
			}
			
			var div = td.getElementsByTagName( 'div' )[0];
			
			div.style.width = '80%';
			ul.parentNode.style.width = '80%';
			
			var ret = {};
			ret['td'] = td;
			ret['select'] = select;
			ret['ul'] = ul;
			ret['button'] = button;
			ret['span'] = span;
			return ret;
		}
		
		//设置目标列表的宽度
		var _setDstTdWidth = function( tr )
		{
			var td = _getDstTdWithHtml( tr );
			
			//获取select
			var select = td.getElementsByTagName( 'select' )[0];
			var name = select.getAttribute( 'name' );
			
			//获取与select关联的ul
			var ul = document.getElementsByName( '_' + name + '_' )[0];
			
			//获取设置宽度的div
			var div = td.getElementsByTagName( 'div' )[0];
			
			div.style.width = '80%';
			ul.parentNode.style.width = '80%';
		}
		
		//通过目标类型控件名获取目标名字的控件
		var _getDstCtrlByTypeCtrlName = function( typeName )
		{
			var dstName = typeName.replace( /type/, 'name' );
			console.log( 'dstName:' + dstName );
			var dstSelect;
			arrSelect = document.getElementsByName( dstName );
			for ( var i=0; i<arrSelect.length; ++i )
			{
				console.log( 'tagName:' + arrSelect[i].tagName );
				if ( arrSelect[i].tagName.toLowerCase() == 'select' )
				{
					dstSelect = arrSelect[i];
					break;
				}
			}
			
			var arrUl = document.getElementsByName( "_" + dstName + "_" );
			var dstUl;
			for ( var i=0; i<arrUl.length; ++i )
			{
				console.log( 'tagName:' + arrUl[i].tagName );
				if ( arrUl[i].tagName.toLowerCase() == 'ul' )
				{
					dstUl = arrUl[i];
					break;
				}
			}
			return [dstSelect, dstUl];
		}
		
		//清除select
		var _clearSelect = function( element )
		{
			var len = element.options.length;
			for ( var i=0; i<len; ++i )
			{
				element.remove(0);
			}
		}
		
		//清除ul
		var _clearUl = function( element )
		{
			var len = element.children.length;
			for ( var i=0; i<len; ++i )
			{
				element.removeChild( element.children[0] );
			}
		}
		
		//获取select所在单元格的其他元素
		var _getOtherElements = function( element )
		{
			//当前的单元格
			var td = element.closest( 'td' );
			
			//唯一的button 
			var button = td.getElementsByTagName( 'button' )[0];
			
			//获取有文本的那个span
			var spans = button.getElementsByTagName( 'span' );
			var span = spans[0];
			for ( var i=0; i<spans.length; ++i )
			{
				if ( spans[i].innerText.length > 0 )
				{
					span = spans[i];
					break;
				}
			}
			
			return [button, span];
		}
		
		//创建一个li
		/*
	    <li data-original-index="0" class="selected">
	    	<a tabindex="0" class="data-normalized-text">
				<span class="text">暂无原始计划</span>
				<span class="glyphicon glyphicon-ok check-mark"></span>
			</a>
		</li>
		*/
		var _createLi = function( text, index )
		{
			var li = document.createElement( 'li' );
			
			li.setAttributeNode( _createAttr( 'data-original-index', index ) );
			if ( 0 == index )
			{
				li.setAttributeNode( _createAttr( 'class', 'selected' ) );
			}
			
			var a = document.createElement( 'a' );
			 a.setAttributeNode( _createAttr( 'tabindex', '0' ) );
			 a.setAttributeNode( _createAttr( 'class', 'data-normalized-text' ) );
			 
			 var span1 = document.createElement( 'span' );
			 span1.setAttributeNode( _createAttr( 'class', 'text' ) );
			 span1.innerText = text;

			 var span2 = document.createElement( 'span' );
			 span2.setAttributeNode( _createAttr( 'class', 'glyphicon glyphicon-ok check-mark' ) );
			 a.appendChild( span1 );
			 a.appendChild( span2 );
			 li.appendChild( a );
			  
			 return li;
		}
		
		//创建一个属性 
		var _createAttr = function( name, value )
		{
			var attr = document.createAttribute( name );
			attr.value = value;
			return attr;
		}
		
		//创建一个option
		var _createOption = function( key, value )
		{
			var option = document.createElement( 'option' );
			option.setAttributeNode( _createAttr( 'value', key ) );
			option.text = value;
			return option;
		}
		
		//找到一行中目标名字的单元格
		var _getDstTd = function( tr )
		{
			tds = tr.cells;
			for ( var i=0; i<tds.length; ++i )
			{
				var selects = tds[i].getElementsByTagName( 'select' );
				if ( selects.length > 0 )
				{
					var name = selects[0].getAttribute( 'name' );
					if ( name )
					{
						if ( name.indexOf( '[name]' ) > 0 )
						{
							return tds[i];
						}
					}
				}
			}
			
			return null;
		}
		
		var _getDstTdWithHtml = function( tr )
		{
			tds = tr.children;
			for ( var i=0; i<tds.length; ++i )
			{
				console.log( 'td:' + tds[i].innerHTML );
				var selects = tds[i].getElementsByTagName( 'select' );
				if ( selects.length > 0 )
				{
					var name = selects[0].getAttribute( 'name' );
					console.log( 'name:' + name );
					if ( name )
					{
						if ( name.indexOf( '[name]' ) > 0 )
						{
							return tds[i];
						}
					}
				}
			}
			
			return null;
		}
		
		var _nameList;

	return ret;
})();
