 
document.addEventListener('DOMContentLoaded',function(){
    
    function getFont(){
        var html1=document.documentElement; 
        var agreeP = document.getElementById('ag');
        var screen=html1.clientWidth;
        var lg = 0;
        if(screen <= 320){
            html1.style.fontSize = '51.2px'; 
            lg = 0
           
        }else if(screen >= 640){
            html1.style.fontSize = '102.4px'; 
            lg = 97/90+'rem';
            
        }else{
            html1.style.fontSize=0.16*screen+'px';
           	lg = (0.16*screen)/200+'rem';
            
        }
        agreeP.style.marginLeft = lg;
                
    }
    getFont();   
    window.onresize=function(){
        getFont();
       
    }
},false)