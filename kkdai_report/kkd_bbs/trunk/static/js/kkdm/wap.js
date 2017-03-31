 /*(function (doc, win) {
          var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function () {
              var clientWidth = docEl.clientWidth;
              if (!clientWidth) return;
              docEl.style.fontSize = 10 * (clientWidth / 320) + 'px';
            };

          if (!doc.addEventListener) return;
          win.addEventListener(resizeEvt, recalc, false);
          doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);*/
		
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
                
                }
        getFont();
        window.onresize=function(){
                getFont();
        }
 },false);// JavaScript Document

		
		
		