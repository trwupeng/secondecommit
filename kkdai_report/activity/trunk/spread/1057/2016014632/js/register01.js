// JavaScript Document
$(function(){
		  $('.product_hd li').click(function(e) {
			 setallprocessbar();
			 stepallprocessbar();
             $(this).addClass('current').siblings().removeClass('current');
			 $(".product_bd ul").eq($(this).index()).show().siblings().hide();
          }); 
		  $('.close').click(function(e) {
               $('.xuanting').hide();
			   $('.blank').hide();
          });
		  stepallprocessbar();  
	  })
	  function stepallprocessbar(){
		  stepprocessbar("loading_id_1","processbar_id_1",11500,"11,500");
		  stepprocessbar("loading_id_2","processbar_id_2",3200,"3,200");
		  stepprocessbar("loading_id_3","processbar_id_3",2000,"2,000");
	}
	  function stepprocessbar(loading_id,processbar_id,curMoney,curMoneyTxt) {
		 var twidth = parseInt($("#"+loading_id).css("width"));
　　       var pb = $("#"+processbar_id);
		 var width = 0;
		 if(pb.css("width") != undefined){
			 width = parseInt(pb.css("width"))+ 3;
		 }
　　      pb.css({"width":width});

	　 　if (width < (curMoney/11500)*twidth) {
	　　　　setTimeout(function () {
	　　　　　　stepprocessbar(loading_id,processbar_id,curMoney,curMoneyTxt);
	　　　　}, 30);
	　　}else{
			pb.text(curMoneyTxt+"元");
		}
	}
	
	function setallprocessbar(){
		for(var i=1;i<4;i++){
			var pb = $("#processbar_id_"+i);
　　     	pb.css({"width":0});
			pb.text("");
		}
　　    
	}