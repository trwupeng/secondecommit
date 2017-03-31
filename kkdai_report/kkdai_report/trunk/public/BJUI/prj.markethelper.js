var tokenid='';
		function loginChk(formData, jqForm, options){  
			//formData: 数组对象，提交表单时，Form插件会以Ajax方式自动提交这些数据，格式如：[{name:user,value:val },{name:pwd,value:pwd}]  var queryString = $.param(formData);   //name=1&address=2  
			//jqForm:   jQuery对象，封装了表单的元素     
			//options:  options对象
			var form = jqForm[0];
			if(!form.username.value || !form.password.value){
				alert('请输入用户名和密码');
				return false;
			}else return true;
		}  

		function loginRet(response, statusText){  
//			//dataType=xml  
//			var name = $('name', responseXML).text();  
//			var address = $('address', responseXML).text();  
//			$("#xmlout").html(name + "  " + address);  
			//dataType=json  
			if(response.result=='ok'){
				tokenid=response.token;
				//$("#frmSearch").token.value=response.token;
				$("#divLogin").hide();
				$("#divSearch").show();
			}else{
				alert(response.result);
			}
		}
	   
		function searchChk(formData, jqForm, options)
		{
			var form = jqForm[0];
			if(!form.phone.value){
				alert('请输入手机号');
				return false;
			}else return true;
		}
		function fillToken(form, options)
		{
			options['data']['token']=tokenid;
			return true;
		}
		function searchRet(response, statusText)
		{
			if(response.result=='timeout'){
				alert('超时，请重新登入');
				$("#divLogin").show();
				$("#divSearch").hide();
				return;
			}
			if(response.result=='ok'){
				//$("#frmSearch").asdf.value=response.token;
				$('#divSearchRet').html('['+(response.bindok==1?'已绑卡':'未绑卡')+']注册日期：'+response.ymdreg);
			}else{
				alert('系统错误：' + response.result);
			}
		}
		$(document).ready(function () {
			$("#frmLogin").ajaxForm(
				{  
				//target: '#output',          //把服务器返回的内容放入id为output的元素中      
				beforeSubmit: loginChk,  //提交前的回调函数  
				success: loginRet,      //提交后的回调函数  
				//url: url,                 //默认是form的action， 如果申明，则会覆盖  
				//type: type,               //默认是form的method（get or post），如果申明，则会覆盖  
				dataType: 'json',           //html(默认), xml, script, json...接受服务端返回的类型 
				//clearForm: true,          //成功提交后，清除所有表单元素的值  
				//resetForm: true,          //成功提交后，重置所有表单元素的值  
				timeout: 3000               //限制请求的时间，当请求大于3秒后，跳出请求  
				}
			);
			$("#frmSearch").ajaxForm(
				{  
				//target: '#output',          //把服务器返回的内容放入id为output的元素中      
				beforeSubmit: searchChk,  //提交前的回调函数  
				success: searchRet,      //提交后的回调函数  
				beforeSerialize:fillToken,
				data:{},
				//url: url,                 //默认是form的action， 如果申明，则会覆盖  
				//type: type,               //默认是form的method（get or post），如果申明，则会覆盖  
				dataType: 'json',           //html(默认), xml, script, json...接受服务端返回的类型 
				//clearForm: true,          //成功提交后，清除所有表单元素的值  
				//resetForm: true,          //成功提交后，重置所有表单元素的值  
				timeout: 3000               //限制请求的时间，当请求大于3秒后，跳出请求  
				}
			);
		});