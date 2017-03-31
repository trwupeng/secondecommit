 /****************************************** */
  //***事件器  * */  
 /****************************************** */
var Emitter = (function emitter() {           
    function constructor() {
       this.initEmitter();
    }
    constructor.prototype ={
        constructor :constructor,
        initEmitter : function() {
            this.callbacks = {};
        },
	    emit:function(eventName) {
	     
            var callbacks = this.callbacks[eventName];
            if (!callbacks) {
                console.log('No valid callback specified.');
                return;
            }
            var args = [].slice.call(arguments)
            //Eliminate the first param (the callback).
            args.shift();
            for (var i = 0; i < callbacks.length; i++) {
                callbacks[i].apply(this, args);
            }
        },
		on :function(eventName, callback) {
            if (eventName in this.callbacks) {
                var cbs = this.callbacks[eventName]
                
                if (cbs.indexOf(callback) == -1) {
                    cbs.push(callback);
                }
                
            }else{
            	this.callbacks[eventName] = [callback];
            }
        },
        removeListener :function(eventName, callback) {
            if (!(eventName in this.callbacks)) {
                return;
            }
            var cbs = this.callbacks[eventName];
            var ind = cbs.indexOf(callback);
            if (ind == -1) {
            	console.warn('No matching callback found');
            	return;
            }
            cbs.splice(ind, 1);
        }
    };
    return constructor;
})(); 
 
var emitter =new Emitter();
console.log(emitter)
var CircleLoad = (function circleload() {           
    function constructor(id,m) {
	  	console.log(this.dom)
	  	this.dom= this.dom? this.dom : this.ctreateCanvas(id);
	    this.ctx = this.dom.getContext("2d");
	    this.time;
		this.angle=0;
	    this.count=0;
		if (m<=0){
			this.m=m-m+1;
			   
		}else{
			this.m=m;
		}
		 
	    this.per=this.m;
		this.clientWidth;
		this.clientHeight;
		this.overAngle=360;
		this.d=360/this.m;
		this.id=id;
		this.isClick=false;
	}
    constructor.prototype ={
        constructor :constructor,
        
        ctreateCanvas:function(id){
      		var parent=document.getElementById(id);
			console.log($('#'+id).width());
			var w = document.getElementById('pDianchanzi').offsetWidth
			console.log(w+'111111111')
			this.clientWidth= w*1;//parent.clientWidth;
            this.clientHeight=w*1;//parent.clientHeight;
            var myCanvas = document.createElement("canvas");
            myCanvas.setAttribute("width", this.clientWidth);
            myCanvas.setAttribute("height",  this.clientHeight);
            myCanvas.setAttribute("id", id+'_canvas');
            parent.appendChild(myCanvas); 
            return myCanvas;         		 
        },
        removeElement : function(ele){
         	if(ele){
         		var _parentElement = ele.parentNode;
         		if(_parentElement){
         			_parentElement.removeChild(ele);
         		}
         		 
         	}
        },
		getS:function(){
		 
			this.per
		 
		},
		pxToRem:function(rem){ 
        	var fontsize =document.documentElement.style.fontSize;
        	if (fontsize){
            	fontsize=fontsize.split('px')[0];
            }else{
                fontsize=16;
            }
            return rem*fontsize;
		 },						 
		tof:function(fontsize){
			var size= fontsize.split('px')[0]
			size=size/1;
			return size;
		},
		draw:function (){
           
            this.ctx.save();
            this.ctx.translate(this.clientWidth/2,this.clientHeight/2);//
            this.ctx.rotate(-Math.PI/2)
            this.ctx.fillStyle="rgba(0,0,0,0.5)";
            this.ctx.beginPath();
            this.ctx.arc(0,0,this.clientWidth/2-5,Math.PI/180*this.angle,Math.PI*2,false);
            this.ctx.lineTo(0,0);
            this.ctx.closePath();
            this.ctx.fill();
            this.ctx.restore();
        },
		start:function(overangle){
            var me=this;
			if (overangle)  me.overAngle=overangle;
		    me.time=setInterval(function(){
               
                me.angle+=me.d;
                me.per--;
                me.ctx.clearRect(0,0,me.clientWidth,me.clientHeight);
                me.draw(me.angle);
				if (me.per == 0){
				 	console.log(me.per+'hang')
				 	
				 	$('#'+me.id).parents('.dian').find('p').text('');
				 	me.removeElement(me.dom)	
					
				}else{
					$('#'+me.id).parents('.dian').find('p').text(me.per+'s');
				}
				me.emits();
				if(me.angle>=me.overAngle){
               							   
			   		me.angle=0;
               		me.count=0;
               		me.per=me.m;
		       		me.overAngle=360;
		       		me.d=360/me.m;
                	clearInterval(me.time);
               }
                // me.ctx.fillText(me.per,me.clientWidth/2-2,me.clientHeight/2+4);
            },1000)
        },
		emits:function(){
			emitter.emit(this.id,this.per);							
		}
		
		
		
		
    }
    return constructor;
})();
			 
			 
			 