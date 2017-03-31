var oDiv = document.getElementById('paopao');
	var timer=null;	
    var Count=0;
	var data=new Array();
setTimeout(startMove,2000);

	function startMove(){
		if (Count>2){	
			Count=0;
		}
		//console.log(data[Count]);
			$('#paopao').text(data[Count]);
		clearInterval(timer);
		timer = setInterval(function(){
				var w = parseInt($(oDiv).css('width'));
				oDiv.style.left = ($(window).width()-w)/2+'px';
			//	console.log($(window).width())
			//	console.log(Count);
				oDiv.style.display = 'block'
				var speed = (700-oDiv.offsetTop)/10;
				speed=speed>0?Math.ceil(speed):Math.floor(speed);//速度取整。		
				oDiv.style.top=oDiv.offsetTop+speed+'px';
				if(oDiv.offsetTop<=200){
					clearInterval(timer);
					setTimeout(function(){
						oDiv.style.display = 'none'
						oDiv.style.top = 300+'px';
					},1000)
					setTimeout(startMove,5000)
				}
		},100)
     Count++;		
	}

function wan(n){
	if (n.toString().length>8){
	n = Math.round((n/100000000)*100)/100;	
   n = n + "亿";
		return n ;
	}else if(n.toString().length<5){
		return n;
	}else{
		n = Math.round((n /10000) * 100) / 100;
    n = n + "万";
	return n ;	
	}
}
		
$.ajax({
		type: "GET",
		url:  _url_server.url+'/public/luodiyeTongji?__VIEW__=jsonp',
		async: false,
		//data: iData,
        dataType: 'jsonp',
        jsonp: 'jsonp',
		success : function(result) {
			if (result.code==200){
				data.push(wan(result.data.newPacketTotal/100)+'元35元新手红包已领取');//188元红包
				data.push(wan(result.data.highPacketTotal/100)+'元188元新手红包已领取');//35元红包
				data.push(wan(result.data.registerTotal)+'人已注册');//注册人数
				console.log(data);
			}else{
				 showTk('网络问题,请重新加载');
			}
		},
		error : function(result) {
			 showTk('网络问题');
		}
	});