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

function myAjax(iUrl,iData,fun,iAsync){
	if(iAsync == undefined) iAsync = true;
	jQuery.ajax({
		type : "GET",
		url : iUrl,
		async : iAsync,
		data : iData,
        dataType : 'jsonp',
        jsonp : 'callback',
		success : function(result) {
			if(result.code == 0){
                //success
			}
			fun(result);
		},
		error : function(result) {
			showTk('系统错误');
		}
	});
}