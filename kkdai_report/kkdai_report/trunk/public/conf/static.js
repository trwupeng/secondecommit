//固定不变的部分
//location.href=xxx的替换写法：$globals.redir(xxxx)
//$globals.urlJsonp(  'loger/openpage',   {tst:"中文"},   'api') 
function my_globals_define()
{
	this.urls={
		"api":"http://wwwdev.miaojilicai.com/",
		"auth":"http://authdev.miaojilicai.com/",
	};

	this.cookieDomain=".miaojilicai.com";
	this.clientType=900;

	this.loadConf = function (){
		this.AjaxPage( '', '/conf/conf.js?v='+(new Date()).valueOf() ); 
	}

	this.dl= function (type, identifier)
	{
		if(this.confLoaded){
			if(type=='js'){
				this.AjaxPage( identifier, '/js/'+identifier+'/'+this.map['js_'+identifier]+'.js' ); 
				this.loading['js.'+identifier]=false;
			}else{
				var head = document.getElementsByTagName('head')[0];
				var link = document.createElement('link');
				link.href = '/css/'+identifier+'/'+this.map['css_'+identifier]+'.css' ;
				link.rel = 'stylesheet';
				link.type = 'text/css';
				head.appendChild(link);
			}
		}else{
			this.tobeLoad.push(new Array(type,identifier));
		}
	};
    this.loading={};
	this.GetHttpRequest= function () 
	{ 
	  if ( window.XMLHttpRequest ) {return new XMLHttpRequest() ;}
	  else if ( window.ActiveXObject ) {return new ActiveXObject("MsXml2.XmlHttp") ; }
	} 
	this.markLoad = function (sId){
		this.loading['js.'+sId]=true;
		var allTrue=true;
		for(var k in this.loading){
			if(this.loading[k]==false){
				allTrue=false;
				break;
			}
		}
		if(allTrue){
			this.onLoaded();
		}
	};
	this.tobeLoad=new Array();
	this.confLoaded=false;
	this.AjaxPage=function (sId, url){ 
	  var oXmlHttp = this.GetHttpRequest() ; 
	  oXmlHttp.onreadystatechange = function() { 
		  if ( oXmlHttp.readyState == 4 ) { 
			 if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 ) { 
				    IncludeJS( sId, url, oXmlHttp.responseText ); 
					if(sId!=''){
						$globals.markLoad(sId);
					}else{
						$globals.confLoaded=true;
						for(var i in $globals.tobeLoad){
							$globals.dl($globals.tobeLoad[i][0],$globals.tobeLoad[i][1]);
						}
					}
			  }  else { 
				   alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ; 
			  } 
		   } 
		} 
		oXmlHttp.open('GET', url, true); 
		oXmlHttp.send(null); 
	} ;
	
	this.map={};
	this.onLoaded=function (){};

	this._fmtUrl = function(cmd,data, host)
	{
		var urlReal = this.urls[host];
		urlReal = urlReal+cmd+'?clientType='+this.clientType;
		for (var k in data){
			urlReal = urlReal+'&'+k+'='+encodeURI(data[k]);
		}
		return  urlReal;
	}
	this.urlJson = function(cmd,data, host)
	{
		var urlReal = this._fmtUrl(cmd,data, host);
		return  urlReal+'&__VIEW__=json';
	}
	this.urlJsonp = function(cmd,data, host)
	{
		var urlReal = this._fmtUrl(cmd,data, host);
		return  urlReal+'&__VIEW__=jsonp&jsonp=?';
	}
	this.redir = function (url){
		if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){
			var referLink = document.createElement('a');
			referLink.href = url;
			document.body.appendChild(referLink);
			referLink.click();
		} else {
			location.href = url;
		}
	}
}
function IncludeJS (sId, fileUrl, source) 
	{ 
		if ( ( source != null ) && ( !document.getElementById( sId ) ) ){ 
			var oHead = document.getElementsByTagName('HEAD').item(0); 
			var oScript = document.createElement( "script" ); 
			oScript.language = "javascript"; 
			oScript.type = "text/javascript"; 
			oScript.id = sId; 
			oScript.defer = true; 
			oScript.text = source; 
			oHead.appendChild( oScript ); 
		} 
	} 


var $globals =  new my_globals_define();

