window.onload=function(){
        function getFont(){
                var html1=document.documentElement;     
                var screen=html1.clientWidth;
                if(screen <= 320){
                     html1.style.fontSize = '51.2px';   
                }else if(screen >= 640){
                      html1.style.fontSize = '102.4px';  
                }else{
                      html1.style.fontSize=0.16*screen+'px';  
                }
                
                }
        getFont();
        window.onresize=function(){
                getFont();
        }
}// JavaScript Document