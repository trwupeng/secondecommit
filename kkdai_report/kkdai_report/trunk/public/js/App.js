window.onload=function(){
        function getFont(){
                var html1=document.documentElement;     
                var screen=html1.clientWidth;
                if(screen <= 320){
                     html1.fontSize = '40px';   
                }else if(screen >= 640){
                      html1.fontSize = '80px';  
                }else{
                      html1.style.fontSize=0.125*screen+'px';  
                }
                
                }
        getFont();
        window.onresize=function(){
                getFont();
        }
}