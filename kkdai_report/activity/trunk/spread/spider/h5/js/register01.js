// JavaScript Document
$(function(){
		  $('.product_hd li').click(function(e) {
             $(this).addClass('current').siblings().removeClass('current');
			 $(".product_bd ul").eq($(this).index()).show().siblings().hide();
          });   
})
	  