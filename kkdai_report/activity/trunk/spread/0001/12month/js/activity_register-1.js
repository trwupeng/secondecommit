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
	},false)

