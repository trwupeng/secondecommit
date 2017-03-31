$.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [ o[this.name] ];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});

	var b = document.getElementById(this[0].id).getElementsByTagName('TABLE');
	for(i =0; i<b.length; i++){
		if(b[i].className == "easyui-datagrid"){
			o[b[i].id] = $('#'+b[i].id).datagrid('getData').rows;
		}
	}	
	return o;
};

$.fn.serializeJSonString = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [ o[this.name] ];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});

	var b = document.getElementById(this[0].id).getElementsByTagName('TABLE');
	for(i =0; i<b.length; i++){
		if(b[i].className == "easyui-datagrid"){
			o[b[i].id] = $('#'+b[i].id).datagrid('getData').rows;
		}
	}	
	return JSON.stringify(o);
};

/*获取userid的cookie的值**/
function get_site_cookie(cookieName) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split("; ");
    for(var i = 0; i < arrCookie.length; i++){
        var arr = arrCookie[i].split("=");
        if(cookieName == arr[0]){
            return arr[1];
        }
    }
    return "";
}
  // var cpId = getCookie("userid");
   //alert(cpId);
   
   /******addcookie方法******/
    function add_site_cookie(objName, objValue, objHours, domain) {
        var str = objName + "=" + encodeURI(objValue);
        if (objHours > 0) {
            var date = new Date();
            var ms = objHours * 3600 * 1000;
            date.setTime(date.getTime() + ms);
            str += "; expires=" + date.toGMTString() + "; domain=" + domain + "; path=/";
        }
        document.cookie = str;
    }
   
   
function myAjax(iUrl,iData,fun,iAsync){
	if(iAsync == undefined) iAsync = true;
	$.ajax({
		type: "GET",
		url: iUrl,
		async: iAsync,
		data: iData,
        dataType: 'jsonp',
        jsonp: 'callback',
		success : function(result) {
			if(result !== null && (Object.prototype.toString.call(result) == "[object Object]") && result.hasOwnProperty('code')){
				if(result.code == 0){
					//success
				}
				fun(result);
			} else {
				showTk('系统繁忙，请稍后再试');
			}
		},
		error : function(result) {
			showTk('系统错误');
		}
	});
}