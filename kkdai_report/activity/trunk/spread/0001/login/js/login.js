var login = window.login || {};
login.loginUrl = {
	url:'http://www.kuaikuaidai.com/h5/login2.do?',
	commomUrl:'http://kkdtest.kuaikuaidai.com'
}

//安卓登录入口  window.KKD_Control.jumpto( 'com.kkd.kkdapp', 'com.kkd.kkdapp.activity.LoginActivity' );

login.loginScope = (function(){
	var bridge,
		loginData;
	document.addEventListener('WebViewJavascriptBridgeReady', onBridgeReady, false)
	function onBridgeReady(event){
	    bridge = event.bridge
	    bridge.init(function(message, responseCallback){        
	        var data = { 'Javascript Responds':'Wee!' }
	        responseCallback(data)
	    })
	}

	function requset(type,url,data,jsonp,callback){//方式，路径，提交数据，是否跨域，回调函数
 	 $.ajax({
            type : type,
            url : url,
            data : data,
            dataType : jsonp ? 'jsonp':'json',
            jsonp : jsonp ? jsonp: '' ,
            success : function(result) {				
				callback(result);  
			},
			error: function(re) {
				alert('网络连接失败，请稍后再试');
				//console.log(re)
                           
            }
        });
 
 	}
	

	
	return {
		//点击首页logo旁边登录按钮
		handle :  function(dom){
			console.log(bridge)
			if(bridge){    //如果是ios app
				console.log(2222)
				loginData = {
		            data : 'gologin'
		        }
		        bridge.send(loginData, function(responseData) {
		             log('JS got response', responseData)
		        })
			}else if(getParameter('channel')){
				if(getParameter('channel')=="android"){   //如果是安卓app
					window.KKD_Control.jumpto( 'com.kkd.kkdapp', 'com.kkd.kkdapp.activity.LoginActivity' );
				}
			} else{    //如果是网页打开
				console.log(333)
				$('#mask').show()
				$('.login').hide()
      			$(dom).show()
			}
		},
		getSendData : function(){//获取用户名密码
			return {
				username : $('#tel').val(),
 				password : $('#psw').val()
			}
			
		},
		submitFlag : function(){  //检测是否输入信息
			var data = login.loginScope.getSendData();
			if(!data.username && !data.password){
				return false
			}else{
				return true
			}
		},
		loginAjax : function(loginDom){  //登录接口调用
			if(login.loginScope.submitFlag()){
				console.log(login.loginScope.getSendData())
				requset('POST',login.loginUrl.url,login.loginScope.getSendData(),'callback',function(result){
					if(result.code==0){
						$('#mask').hide();
						$(loginDom).hide();
						$('.dz').hide();
						$('.tui').show();
				    	document.cookie="token="+result.token;
						document.cookie = 'customerId='+result.customerId;
						window.location.reload();
					}else if(result.code==202){
						alert('提交非法参数');
					}else if(result.code==223){
						alert("用户名或密码错误");
					}else {
						alert('')
					}
					
				})
				
			}else{
				//$('#valide').text('请输入完整信息')
			}
			
		},
		sendDataCookie : function(){   //获取各个接口需要提交的带有已注册的cookie数据
			
			var token=getParameter('token')//获取url 上的cookie的参数值
			var customerId=getParameter('customerId')//获取url 上的的参数值
			var cookie,sendData;
			if(token && customerId){  //如果url有参数，则取出，并设置cookie
				document.cookie = 'customerId='+customerId;
				document.cookie="token="+token;
				cookie = {
					jskey:token,
			    	customerId:customerId
				}
			}else{
				if(getCookies('customerId')){
					cookie = {
						jskey:getCookies('token'),
			    		customerId:getCookies('customerId')
					}
				}
				
			}
						
			if (cookie){   		
			    sendData=cookie
			}else if (getCookies('JSESSIONID')){
						
			    sendData={
			    	jskey:getCookies('JSESSIONID')
			    }
			}else {
				sendData={jskey:''}
			}

			return sendData;
			
		},
		checkLogin : function(){  //检测是否登录
			var sendData = this.sendDataCookie()
			requset('GET',login.loginUrl.commomUrl+'/Christmas/award?__VIEW__=json',sendData,'',function(re){
				console.log(re)
		        if(re.code==400){
				  	if(re.msg=='unlogin'){	//未登录	
				  		$('.tui').hide();
						$('.dz').show();
				    }
			     
				}else{
					$('.tui').show();
					$('.dz').hide();
				}
		    });
			
			
		},
		
		
		tuiChu : function(){
			delCookie('token');
			delCookie('customerId');
			delCookie('JSESSIONID');
			this.checkLogin()
		},
		
		init : function(){
			this.checkLogin()
		}
		
	}
	
})()

var obj = login.loginScope;	
obj.init();
console.log(obj)

$('.dz .dengLu').click(function(){ 
	console.log(12333)
	$('#mask').show();
	obj.handle($('.loginYe'))
 
});

 $('.loginYe .logBtn').click(function(){
	obj.loginAjax($('.loginYe'))
 	 	
 })
 $('.loginYe .del').click(function(){
 	$('#mask').hide()
	$('.loginYe').hide()
 })
 
 
$('.tui input').click(function(){
	obj.tuiChu()
})






function getParameter(paramName){
    var searchString = window.location.search.substring(1),
            i, val, params = searchString.split("&");
    for (i = 0; i < params.length; i++) {
        val = params[i].split("=");
        if (val[0] == paramName) {
            return decodeURIComponent(val[1]);
        }
    }
    return null;
}
  
function getCookies(name){
	var allcookies = document.cookie;
	var cookie_pos = allcookies.indexOf(name);
	
	if(cookie_pos !=-1){
		cookie_pos += name.length + 1;
		var cookie_end = allcookies.indexOf(";", cookie_pos);
		
		if (cookie_end == -1){

        	cookie_end = allcookies.length;
    	}
		cookies = unescape(allcookies.substring(cookie_pos, cookie_end)); 
	}else{
		return false
	}
	
	return cookies;
	
}

	//清楚cookie
	function delCookie(name){
		var exp = new Date();
		exp.setTime(exp.getTime() - 1);
		var cval=getCookies(name);
		if(cval!=null){
			document.cookie= name + "="+cval+";expires="+exp.toGMTString();
		}					
	}
	
