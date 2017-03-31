  document.addEventListener('DOMContentLoaded',function(){
    
   function getFont(){
                var html1=document.documentElement;     
                var screen=html1.clientWidth;
                if(screen <= 320){
                     html1.style.fontSize = '46.3768px';   
                }else if(screen >= 640){
                      html1.style.fontSize = '92.7536px';  
                }else{
                      html1.style.fontSize=0.145*screen+'px';  
                }
				
				
				var screenWidth = jQuery(window).width(), screenHeigth = jQuery(window).height();
        //获取屏幕宽高  
        var scollTop = document.body.scrollTop;//jQuery(document).scrollTop();

        console.log(scollTop);		
        //当前窗口距离页面顶部的距离  
        var objLeft = (screenWidth - jQuery(".ft").width()) / 2;
        ///弹出框距离左侧距离  
        var objTop = (screenHeigth - jQuery(".ft").height()) / 2 + scollTop;
        ///弹出框距离顶部的距离  
        jQuery(".ft").css('left',objLeft + "px");
        // jQuery(".ft").css('top',objTop + "px");
                
                }
        getFont();
        window.onresize=function(){
                getFont();
        }
 },false);// JavaScript Document