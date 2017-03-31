window.onload=function(){
        function getFont(){
                var html1=document.documentElement;     
                var screen=html1.clientWidth;
                if(screen <= 320){
                     html1.style.fontSize = '61.76px';   
                }else if(screen >= 640){
                      html1.style.fontSize = '123.52px';  
                }else{
                      html1.style.fontSize=0.193*screen+'px';  
                }
                
                }
        getFont();
        window.onresize=function(){
                getFont();
        }
}// JavaScript Document