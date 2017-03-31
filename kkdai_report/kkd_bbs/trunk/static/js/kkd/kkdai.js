url_prefix = '';
jQuery(function(){	
	/*进度条为0时*/
	/*var miin=jQuery('.home_miin').text();
	alert(miin)
	if(miin=="0.00%"){
		
		jQuery('.lineper').css("min-width",0);
	}*/

	var milen=jQuery('.home_miin').length;
	for(var i=0;i<milen;i++){
	 var miin=jQuery('.home_miin').eq(i).text();
	 if(miin=="0.00%" || miin=="进度：0.00%"){
		jQuery('.lineper').css("min-width",0);
	}
	}
	/*footer居底*/
	var hh1=jQuery('.footbottom').height();
	var hh2=jQuery(window).height()-jQuery('.header').height()-jQuery('.nav').height()-jQuery('.footer').height();
	if (hh1<hh2) {
		jQuery('.footbottom').css('height', hh2+'px');
	};
	jQuery('.footbottom').css('overflow', 'hidden');
	
	/*客户端移入移出*/
	jQuery(".head_web").hover(hoverIn,hoverOut);
	jQuery(".head_app").hover(hoverIn,hoverOut);

	function hoverIn(){
		jQuery(".head_web").css({"color":"#333","background":"#f3f3f3"});
		jQuery(".head_web a").css("background","url(http://image.kuaikuaidai.com/static/images/icon_3.png) no-repeat");
		jQuery(".head_app").show();
	}

	function hoverOut(){
		jQuery(".head_web").css({"color":"#b5b5b5","background":"none"});
		jQuery(".head_web a").css("background","url(http://image.kuaikuaidai.com/static/images/icon_2.png) no-repeat");
		jQuery(".head_app").hide();
	}
	/*导航移入移出*/
	jQuery('.nav_arrow').hover(function(){
		jQuery('.nav_box').css('display','block');
	},function(){
		jQuery('.nav_box').css('display','none');
	});

	// 返回顶部
	jQuery('.back2Top').click('tap', function() {
		jQuery('body,html').animate({
		  scrollTop: 0
		}, 200);
	});

	/*微信弹框*/
	jQuery('.foot_hover2').click(function(){
		jQuery('.win_warp').fadeIn('1000');
	});
	jQuery('.close').click(function(){
		jQuery('.win_warp').fadeOut('1000');
	});

	/*详情页切换*/
	var $menupro = jQuery('.project .pro_title li');
	$menupro.click(function(){
	    jQuery(this).addClass('pro_curr').siblings().removeClass('pro_curr');
	    var index = $menupro.index(this);
	    jQuery('.pro_warp > .pro_tab').eq(index).show().siblings().hide();
	});

	/*投资记录切换*/
	var $menu = jQuery('.invest .invest_title li');
	$menu.click(function(){
	    jQuery(this).addClass('invert_curr').siblings().removeClass('invert_curr');
	    var index = $menu.index(this);
	    jQuery('.inver_warp > .inver_tab').eq(index).show().siblings().hide();
	});
	 jQuery(".changenum").focus(function(){
		 increaseFun();
	 });
	 jQuery(".changenum").blur(function(){
		 //increaseFun();
	 });
	/*详情页加减*/
	//jQuery('.spinnerExample').spinner({});

	//jQuery('.spinner button').click(function(){
	 jQuery('.increase , .decrease').click(function(){
		//jQuery('.hou_hide').hide();
		//jQuery('.coupontip').hide();
		//jQuery("#coupons").val('暂不使用优惠券');
		//var dataInfo = document.getElementById("coupons").value;
        //jQuery(".moneypic").html(dataInfo);
		//var dataInfo = document.getElementById("coupons").value;
		var dataselect = jQuery(".coupon option:selected").attr("id");
		if(dataselect != '' && dataselect != undefined){
			jQuery('.hou_hide').hide();
			var spinner = jQuery('#spinner').val();
			var largemoney = jQuery(".coupon option:selected").attr("data-info");
	        if(parseInt(spinner)<parseInt(largemoney)){
	          jQuery(".coupontip").html("该优惠券最低起投金额为"+largemoney+"元");
	          return;
	        }
			if(spinner =="" || parseInt(spinner)<50){
		          jQuery(".coupontip").html("不能使用优惠券。");
		          return;
		    }
		    if(parseInt(dataselect)>parseInt(spinner)){
		          jQuery(".coupontip").html("投资金额需大于抵现券金额。");
		          return;
		    }else{
		          var surplusum = parseInt(spinner) - parseInt(dataselect);
		          jQuery(".coupontip").html('已优惠'+parseInt(dataselect)+'元，实际支付'+surplusum+'元');
		    }
		}
		increaseFun();
	});
	jQuery(".spinnerExample").blur(function(){
		var away = parseInt(jQuery('.spinnerExample').val());
		if(away >= 50){
			increaseFun();
		}
		
	})

	function increaseFun(){
		var away = jQuery('.spinnerExample').val();
		//利率隐藏域。动态计算预计收益
		var rates = jQuery('.rates').val();
		//var total = (away*rates).toFixed(2);
		var total = away*rates;
		var a = total+"";
		var aa = a.indexOf(".");
		var last = a.substring(aa+1, aa+4);
		var lastnum = last.substring(2,3);
		
        if(lastnum>=5){
        	var mmm = (away*rates)-0.01;
        	var totalss = parseFloat(mmm.toFixed(2));
        
        }else{
        	var totalss = parseFloat((away*rates).toFixed(2));
        }		
		jQuery('#away .hou_money').html(totalss);

	}
	setInterval(function(){
		var away = jQuery('.spinnerExample').val();
		//利率隐藏域。动态计算预计收益(不四舍五入)
		var rates = jQuery('.rates').val();
		var total = away*rates;
		var a = total+"";
		var aa = a.indexOf(".");
		var last = a.substring(aa+1, aa+4);
		var lastnum = last.substring(2,3);		
        if(lastnum>=5){        	
        	var mmm = (away*rates)-0.01;
        	var totalss = parseFloat(mmm.toFixed(2));  
        }else{
        	var totalss = parseFloat((away*rates).toFixed(2));
        }	
		jQuery('#away .hou_money').html(totalss);
	},0);

	jQuery('.spinnerExample').click(function(){
		if(Number(jQuery(this).val())==0){
			jQuery('.spinnerExample').val('');
		}
	});
	jQuery('.spinnerExample').keyup(function(){
		//jQuery("#coupons").val('暂不使用优惠券');
		//var dataInfo = document.getElementById("coupons").value;
        //jQuery(".moneypic").html(dataInfo);
       // jQuery('.hou_hide').hide();
       // jQuery('.coupontip').hide();
		// if(jQuery(this).val().substring(0, 1) == 0){
		// 	jQuery(this).val(0);
		// }
		if(Number(jQuery(this).val())==0){
			jQuery(this).val(0);
		}
		jQuery('#keyup').hide();
		//var dataInfo = document.getElementById("coupons").value;
		var dataselect = jQuery(".coupon option:selected").attr("id");
		if(dataselect != '' && dataselect != undefined){
			var largemoney = jQuery(".coupon option:selected").attr("data-info");
			var spinner = jQuery('#spinner').val();
	        if(parseInt(spinner)<parseInt(largemoney)){
	          jQuery(".coupontip").html("该优惠券最低起投金额为"+largemoney+"元");
	          return;
	        }
			jQuery('.hou_hide').hide();
			if(spinner =="" || parseInt(spinner)<50){
		          jQuery(".coupontip").html("不能使用优惠券。");
		          return;
		    }
		    if(parseInt(dataselect)>parseInt(spinner)){
		          jQuery(".coupontip").html("投资金额需大于抵现券金额。");
		          return;
		    }else{
		          var surplusum = parseInt(spinner) - parseInt(dataselect);
		          jQuery(".coupontip").html('已优惠'+parseInt(dataselect)+'元，实际支付'+surplusum+'元');
		    }
		}	        
	});
	/*邀请好友暂无记录*/
	var anolen=jQuery('.anonymous .nonym tr').length;
	 if(anolen==1){
		 jQuery('.inver_not').css("display",'block');
	 }
	
	/*邀请好友*/
	jQuery('#copy-button').click(function(){
		jQuery('.invite_warp').fadeIn();
	});
	jQuery('.invite_btn a').click(function(){
		jQuery('.invite_warp').fadeOut();
	});
	
	

	//天天赚剩余额度为0或购买进度100%时，认购按钮不可点
	/*var restNum=jQuery("#restNum").text();
	var curPercent=jQuery("#curPercent").text();
	if(restNum == 0 || curPercent == "100%"){
		jQuery('.paybtn').attr('disabled',"true");
		jQuery('.paybtn').css('background',"#ccc");
	}else{
		jQuery('.paybtn').removeAttr('disabled');
		jQuery('.paybtn').css('background',"##904ECB");
	}*/
	
	/*详情页判断数量是否为空*/
	jQuery('.pay_btn').click(function(){
		var away = jQuery('.spinnerExample').val();
		var dataselect = jQuery(".coupon option:selected").attr("id");
		var largemoney = jQuery(".coupon option:selected").attr("data-info");
		var spinner = jQuery("#spinner").val(); 
		//用户余额
		var user = jQuery('#user').text();
		//减去后余额
		if(dataselect == ""){
			var surplusum = parseInt(away)
		}else{
		    var surplusum = parseInt(away) - parseInt(dataselect);
		}
		//体验标
		var tasthi=jQuery("#tastein").val();
		if(tasthi == "501"){
			if(dataselect == undefined){
				if(parseFloat(away) > parseFloat(user)){
					jQuery('.hou_hide').show();
					jQuery('.hou_hide').html('你的体验金不足！');
					return
				}
			}else{
			  if(parseFloat(surplusum) > parseFloat(user)){
				jQuery('.hou_hide').show();
				jQuery('.hou_hide').html('你的体验金不足！');
				return
			  }
			}
		}
		//判断投资金额和可用金额的大小
		if(dataselect == undefined){
			if(parseFloat(away) > parseFloat(user)){
				jQuery('.hou_hide').show();
				jQuery('.hou_hide').html('可用余额不足，请速速充值。');
				return
			}
		}else{
		  if(parseFloat(surplusum) > parseFloat(user)){
			jQuery('.hou_hide').show();
			jQuery('.hou_hide').html('可用余额不足，请速速充值。');
			return
		  }
		}
		//jQuery(".moneypic").hide();
		//区分天天赚，房宝宝和房产抵押
		var flag = jQuery("[data-info]").attr("data-info");
		//隐藏域 判断是哪个标的
		var bidvalue = jQuery('#bidvalue').val();
		//投资金额不能为0
		if(parseFloat(away) == 0){
			jQuery('.hou_hide').show();
			return
		}else if(parseFloat(away) < 50){
			jQuery('.hou_hide').show();
			if(flag == 1){
				jQuery('.hou_hide').html('认购金额不能低于50元！');
			}else{
				jQuery('.hou_hide').html('投资金额不能低于50元！');
			}
			return
		}
        if(flag == 1){
        	if(away == ''){
    			jQuery('.hou_hide').show();
    			jQuery('.hou_hide').html('认购金额不能为空！');
    			return
    		}
		}else{
			if(away == ''){
				jQuery('.hou_hide').show();
				jQuery('.hou_hide').html('投资金额不能为空！');
				return
			}
			if(away%50 != 0){
				jQuery('.hou_hide').show();
				jQuery('.hou_hide').html('投资金额需为50的整数倍！');
				return
			}
		}
		
		if(parseInt(dataselect)>parseInt(away)){
	          jQuery(".coupontip").html("该优惠券最低起投金额为"+largemoney+"元");
	          return;
	    }
		var away = jQuery('.spinnerExample').val();
		if(away == 0){
			jQuery('.hou_hide').show();
		}
		
		var num = jQuery('.spinnerExample').val();
	    var reg = /.*\..*/;
	    if(reg.test(num)){
		   if(num.toString().split(".")[1].length>2){
			  jQuery('.hou_hide').hide();
			  jQuery('#keyup').show();
			  return false
		   }
	    }
		jQuery('.hou_hide').hide();
		//天天赚ajax请求投标
		if(flag == 1){
		   makeday(away,bidvalue);
		}
		//房宝宝ajax请求投标
		if(flag == 2){
		   makehouse(away,bidvalue);
		}
		//房产抵押ajax请求投标
		if(flag == 3){
		   makepledge(away,bidvalue);
		}
		
	});
	//天天赚ajax请求投标
    function makeday(away,bidvalue){
    	//loading
	    jQuery('.payload').css('display', 'block');
		jQuery('.paybtn').attr('disabled',"true");
    	$.ajax({
			url:url_prefix+"/yuebao/poi.do?amount="+away+"&bidId="+bidvalue,
			type:"post",
			dataType:"json",
			success:function(data){	
				if(data.code==-2) {
					window.location.href=url_prefix+"login.html";
					return;
				}
				//loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
			     alert(data.info);
				 location.reload();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {					
				 //loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
			     //document.body.removeChild(circle.canvas);
                 alert("请求超时，请重试");
            }  
		});
    }
  //房宝宝ajax请求投标
    function makehouse(away,bidvalue){
    	//loading
	    jQuery('.payload').css('display', 'block');
		jQuery('.paybtn').attr('disabled',"true");
    	$.ajax({
			url:url_prefix+"/yuebao/poi.do?amount="+away+"&bidId="+bidvalue,
			type:"post",
			dataType:"json",
			success:function(data){	
				if(data.code==-2) {
					window.location.href=url_prefix+"login.html";
					return;
				}
				//loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
			     alert(data.info);
				 location.reload();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {	
				//loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
                 alert("请求超时，请重试");
            }  
		});
    }
  //房产抵押ajax请求投标
    function makepledge(away,bidvalue){
    	//选择的钱数 and 投资金额
    	var dataselect = jQuery(".coupon option:selected").attr("id");
        var spinner = jQuery("#spinner").val(); 
        if(parseInt(dataselect)>parseInt(spinner)){
          jQuery(".coupontip").html("投资金额需大于抵现券金额。");
          return;
        }
        var dataid = jQuery(".coupon option:selected").attr("class");
        //选中的id优惠券
        if(dataid == undefined){
            var dataid = '';
        }else{
            var dataid = jQuery(".coupon option:selected").attr("class");
        }        
    	var reg = /.*\..*/;
 	    if(reg.test(away)){
 		   if(away.toString().split(".")[1].length>0){
 			  jQuery('#keyup').show();
 			  return false
 		   }
 	    }
    	//loading
	    jQuery('.payload').css('display', 'block');
		jQuery('.paybtn').attr('disabled',"true");
    	$.ajax({
			url:url_prefix+"/poi.do?amount="+away+"&bidId="+bidvalue+"&ccId="+dataid,
			type:"post",
			dataType:"json",
			success:function(data){	
				if(data.code==-2) {
					window.location.href=url_prefix+"login.html";
					return;
				} 
				//loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
			     alert(data.desc);
				 location.reload();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {	
				//loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
                 alert("请求超时，请重试");
            }  
		});
    }
	/*活动中心 所有活动下拉*/
	jQuery('.acti_select').hover(function(){
		jQuery('.acti_select ul').show();
	},function(){
		jQuery('.acti_select ul').hide();
	});

	/*个人账户左边*/
	jQuery('.person_ul li').click(function(){
		jQuery('.person_ul li').removeClass('person_active');
		jQuery(this).addClass('person_active');
	});

	jQuery('.person_mr1').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in3 person_in4 person_in5 person_in6 person_in7');
		jQuery('.person_mr1').addClass('person_in1');
	});
	jQuery('.person_mr2').click(function(){
		jQuery('.person_ul li').removeClass('person_in1 person_in3 person_in4 person_in5 person_in6 person_in7');
		jQuery('.person_mr2').addClass('person_in2');
	});
	jQuery('.person_mr3').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in1 person_in4 person_in5 person_in6 person_in7');
		jQuery('.person_mr3').addClass('person_in3');
	});
	jQuery('.person_mr4').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in1 person_in3 person_in5 person_in6 person_in7');
		jQuery('.person_mr4').addClass('person_in4');
	});
	jQuery('.person_mr5').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in1 person_in3 person_in4 person_in6 person_in7');
		jQuery('.person_mr5').addClass('person_in5');
	});
	jQuery('.person_mr6').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in1 person_in3 person_in4 person_in5 person_in7');
		jQuery('.person_mr6').addClass('person_in6');
	});
	jQuery('.person_mr7').click(function(){
		jQuery('.person_ul li').removeClass('person_in2 person_in1 person_in3 person_in4 person_in5 person_in6');
		jQuery('.person_mr7').addClass('person_in7');
	});

	jQuery('.person_mr1').hover(function(){
		jQuery('.person_mr1').addClass('person_ok1');
	},function(){
		jQuery('.person_mr1').removeClass('person_ok1');
	});

	jQuery('.person_mr2').hover(function(){
		jQuery('.person_mr2').addClass('person_ok2');
	},function(){
		jQuery('.person_mr2').removeClass('person_ok2');
	});

	jQuery('.person_mr3').hover(function(){
		jQuery('.person_mr3').addClass('person_ok3');
	},function(){
		jQuery('.person_mr3').removeClass('person_ok3');
	});

	jQuery('.person_mr4').hover(function(){
		jQuery('.person_mr4').addClass('person_ok4');
	},function(){
		jQuery('.person_mr4').removeClass('person_ok4');
	});

	jQuery('.person_mr5').hover(function(){
		jQuery('.person_mr5').addClass('person_ok5');
	},function(){
		jQuery('.person_mr5').removeClass('person_ok5');
	});

	jQuery('.person_mr6').hover(function(){
		jQuery('.person_mr6').addClass('person_ok6');
	},function(){
		jQuery('.person_mr6').removeClass('person_ok6');
	});
	jQuery('.person_mr7').hover(function(){
		jQuery('.person_mr7').addClass('person_ok7');
	},function(){
		jQuery('.person_mr7').removeClass('person_ok7');
	});

	/*个人账户充值和提现*/
	jQuery('.action_list1').hover(function(){
		jQuery('.action_list1 .action_hide').show();
		jQuery('.action_list1 .action_img').addClass('action_become');
	},function(){
		jQuery('.action_list1 .action_hide').hide();
		jQuery('.action_list1 .action_img').removeClass('action_become');
	});

	jQuery('.action_list2').hover(function(){
		jQuery('.action_list2 .action_hide').show();
		jQuery('.action_list2 .action_img').addClass('action_become');
	},function(){
		jQuery('.action_list2 .action_hide').hide();
		jQuery('.action_list2 .action_img').removeClass('action_become');
	});

	jQuery('.action_list3').hover(function(){
		jQuery('.action_list3 .action_hide').show();
		jQuery('.action_list3 .action_img').addClass('action_become');
	},function(){
		jQuery('.action_list3 .action_hide').hide();
		jQuery('.action_list3 .action_img').removeClass('action_become');
	});	

	jQuery('.sign_btn2').click(function(){
		jQuery('.sign_btn2').html('已签到');
	});

	/*关于我们*/
/*	jQuery('.about_left li').click(function(event) {
		var i=jQuery(this).index();
		jQuery(this).addClass('us_current').siblings('li').removeClass('us_current');
		jQuery('.about_right').eq(i).addClass('about_block').siblings('.about_right').removeClass('about_block');
	});*/
	jQuery('.about_left li').hover(function() {
		if (jQuery(this).hasClass('us_current')) {} else{
			jQuery(this).css('background', '#dcdff5');
		};
	}, function() {	
		if (jQuery(this).hasClass('us_current')) {} else{
			jQuery(this).css('background', '#fff');
		};
	});
	/*选择充值银行*/
	jQuery('.bank_box li').click(function(){
		jQuery('.bank_box li em').removeClass('bank_check');
		jQuery(this).find('em').addClass('bank_check');
		jQuery('.bank_txt3').hide();
		jQuery('.bank_txt4').hide();
	});
	/*银行卡页面*/
	jQuery('.bank_btn a').click(function(){
		var bank = jQuery('.bank_pay input').val();
		if(bank == 0){
			jQuery('.bank_right').show();
		}

		var has = jQuery('.bank_box li em').hasClass('bank_check');
		if(has){
			jQuery('.bank_txt3').hide();
		}else{
			jQuery('.bank_txt3').show();
			jQuery('.bank_txt4').show();
		}
		//loading
		/*jQuery('.bank .bank_pay .bank_btn img').css('display', 'block');
		jQuery('.bank_btn a').attr('disabled',"true");*/
	});
    /*取消loading*/
	/*function zGrey(){
		jQuery('.bank .bank_pay .bank_btn img').css('display', 'none');
		jQuery('.bank_btn a').removeAttr('disabled');
	}*/

	/*登陆密码收缩展开*/
	var curText1=jQuery('#secure_btn1').text();
	jQuery('#secure_btn1').click(function(){
	  if(jQuery('#secure_hide1').is(":visible")){
	      jQuery('#secure_hide1').slideUp();
	      if(curText1 == "设置"){
	    	  jQuery('#secure_btn1').html('设置');
	      }else if(curText1 == "修改"){
	    	  jQuery('#secure_btn1').html('修改');
	      }
	  }else{
	      jQuery('#secure_hide1').slideDown();
	      jQuery('#secure_btn1').html('取消');
	  }
	});

	/*取现密码收缩展开*/
	var curText2=jQuery('#secure_btn2').text();
	jQuery('#secure_btn2').click(function(){
	  if(jQuery('#secure_hide2').is(":visible")){
	      jQuery('#secure_hide2').slideUp();
	      if(curText2 == "设置"){
	    	  jQuery('#secure_btn2').html('设置');
	      }else if(curText2 == "修改"){
	    	  jQuery('#secure_btn2').html('修改');
	      }
	  }else{
	      jQuery('#secure_hide2').slideDown();
	      jQuery('#secure_btn2').html('取消');
	  }
	});

	/*绑定邮箱收缩展开*/
	var curText3=jQuery('#secure_btn3').text();
	jQuery('#secure_btn3').click(function(){
	  if(jQuery('#secure_hide3').is(":visible")){
	      jQuery('#secure_hide3').slideUp();
	      if(curText3 == "绑定"){
	    	  jQuery('#secure_btn3').html('绑定');
	      }else if(curText3 == "修改"){
	    	  jQuery('#secure_btn3').html('修改');
	      }
	  }else{
	      jQuery('#secure_hide3').slideDown();
	      jQuery('#secure_btn3').html('取消');
	  }
	});

	/*绑定手机收缩展开*/
	var curText4=jQuery('#secure_btn4').text();
	jQuery('#secure_btn4').click(function(){
	  if(jQuery('#secure_hide4').is(":visible")){
	      jQuery('#secure_hide4').slideUp();
	      if(curText4 == "绑定"){
	    	  jQuery('#secure_btn4').html('绑定');
	      }else if(curText4 == "修改"){
	    	  jQuery('#secure_btn4').html('修改');
	      }
	  }else{
	      jQuery('#secure_hide4').slideDown();
	      jQuery('#secure_btn4').html('取消');
	  }
	});

	/*昵称收缩展开*/
	jQuery('#secure_btn5').click(function(){
	  if(jQuery('#secure_hide5').is(":visible")){
	      jQuery('#secure_hide5').slideUp();
	      jQuery('#secure_btn5').html('修改');
	  }else{
	      jQuery('#secure_hide5').slideDown();
	      jQuery('#secure_btn5').html('取消');
	  }
	});

	/*邀请好友表格隔行变色*/
	jQuery(".table tr:odd").css("background-color",'#f2f2f2');
    jQuery(".tabley tr:odd").css("background-color",'#f2f2f2');
    jQuery(".table .mystyle tr:odd").css("background-color",'#fff');
	/*天天赚分页*/
	jQuery('.page a').click(function(){
		jQuery('.page a').removeClass('active');
		jQuery(this).addClass('active');
	});

	/*手机获取验证码倒计时*/
	var wait=60;
	function code_Time(o) {
		if (wait == 0) {
			o.removeAttribute("disabled");
			o.value="获取手机验证码";
			wait = 60;
		} else {
			o.setAttribute("disabled", true);
			o.value="重新发送(" + wait + ")";
			wait--;
			setTimeout(function() {
				code_Time(o)
			},1000)
		}
	}
	//绑定银行卡发送手机验证码
	jQuery("#bank_bton").click(function(){
		var o = jQuery(this);
	    if(bankCode == '' || bankCode == undefined){
			//alert("请选择绑卡银行！");
			return;
		}
	    
	    var realname = jQuery("#realname").val();
		if(realname==null||realname==""){
			alert("请先开通第三方支付后再进行绑卡操作！");
			return;
		}
		
		var card_no = jQuery("#card_no").val();
		if(card_no==null||card_no==""){
			jQuery('.ts1').show();
			jQuery('.ts1').html("银行卡号不能为空！");
			//alert("银行卡号不能为空！");
			return;
		}
		
		var phone = jQuery("#phone").val();
		if(phone==null||phone==""){
			jQuery('.ts2').show();
			jQuery('.ts2').html("手机号不能为空！");
			return;
		}else if(!/^1[3,4,5,7,8]\d{9}$/.test(phone)){
			jQuery('.ts2').show();
			jQuery('.ts2').html("请正确填写手机号码！");
			return;
		}
		var phoneVal=jQuery(".bank_ts2").val();
		var phoneVal1=jQuery(".min_length").val();
		if(phoneVal1==""){
			jQuery(".ts1").show();
			return;
		}else if(!/^[0-9]{16,19}$/.test(phoneVal1)){
			jQuery(".ts1").show();
			jQuery(".ts1").html("请正确填写银行卡号！");
			return;
		}else{
			jQuery(".ts1").hide();
		};

		if(phoneVal==""){
			jQuery(".ts2").show();
			return;
		}else if(!/^1[3,4,5,7,8]\d{9}$/.test(phoneVal)){
			jQuery(".ts2").show();
			jQuery(".ts2").html("请正确填写手机号码！");
			return;
		}else{
			jQuery(".ts2").hide();
		};
		jQuery(".ts3").hide();
		$.ajax({
			url:"getCardBinDingInfo.html?bankCode=" + bankCode + "&cardNo=" + card_no+ "&phone=" + phone,
			type:"post",
			dataType:"json",
			success:function(data){
				  var code = data.code;
				  var message = data.message;
				  if(code == "0"){
				    alert("验证码已发送！");
				    code_Time(o[0]);
				    //Load();
				    var ticket = data.data.ticket;
				    var bindingId = data.data.bindingId;
				    jQuery("#ticket").val(ticket);
				    jQuery("#bindingId").val(bindingId);
				  }else if(code == "2"){
			        alert(message);
				  }else if(code == "232"){
			        alert("只允许绑定一张银行卡！");
				  }else if(code == "300"){
			        alert("未开通实名认证！");
				  }else if(code == "207"){
			        alert("此银行卡号已绑定！");
				  }else{
				    alert("发送验证码失败，请确认绑卡信息是否正确！");
				  }
			}
		});
	})
    //忘记密码
	jQuery("#getyz_btn").click(function(){
		var o = jQuery(this);
		var phoneVal=jQuery(".phone_input").val();
		
		if(phoneVal==""){
			jQuery(".phone_em").show();
			return;
		}else if(!/^1[3,4,5,7,8]\d{9}$/.test(phoneVal)){
			jQuery(".phone_em").show();
			jQuery(".phone_em").html("请正确填写手机号码！");
			return;
		}else{
			//loading
		    jQuery('.yz_box .loadimg').css('display', 'block');
			o.attr('disabled',"true");
			jQuery(".phone_em").hide();
			$.ajax({
				url:url_prefix+"/customer/sendPhoneCode?type=1&phone=" + phoneVal +"",
				type:"post",
				dataType:"json",
				success: function(data){
					//取消 loading
				    jQuery('.yz_box .loadimg').css('display', 'none');
					o.removeAttr('disabled');
					if(data.code=="0"){
						jQuery('.warns').html('');
						code_Time(o[0]); 
					}else if(data.code=="noEmail"){
						jQuery('.warns').html("不存在此手机号码！");
					}else{
						jQuery('.warns').html(data.message);
					}
				}
			});
		}
	})

	//手机验证
	jQuery(".phone_submit").click(function(){
		var phoneVal=jQuery(".phone_input").val();
		var yzVal=jQuery(".yz_input").val();
		if(phoneVal==""){
			jQuery(".phone_em").show();
			jQuery(".phone_em").text("请输入手机号！");
			return;
		}else if(!/^1[3,4,5,7,8]\d{9}$/.test(phoneVal)){
			jQuery(".phone_em").show();
			jQuery(".phone_em").text("请正确填写手机号码！");
			return;
		}else{
			jQuery(".phone_em").hide();
		}
		if(yzVal==""){
			jQuery(".yzphone_em").show();
			return;
		}else{
			jQuery(".yzphone_em").hide();
		}

		checkCode(phoneVal,yzVal);

	})

	function checkCode(find_phone,emailCode){
	var url = "customer/checkCode?type=1&phone=" + find_phone + "&phoneCode=" + emailCode + "";
	$.ajax({
		type:'POST',
        url: url,
		dataType:'json',
		success: function(data){
			if(data.code==0){
				document.getElementById("phoneForm").submit();
			}else if(data.code=="228"){
				jQuery(".yzphone_em").show();
				jQuery(".yzphone_em").html("验证码输入有误！");
			}else if(data.code=="noCustomer"){
				alert("不存在该邮箱和手机号码对应的账户！");
			}else if(data.code=="227"){
				jQuery(".yzphone_em").show();
				jQuery(".yzphone_em").html("验证码已过期！");
			}
		}
	});
}
	//修改密码
	jQuery(".revise_submit").click(function(){
		
		

	});	
	//天天赚转出
	jQuery('#goout').click(function(){
		var daymoney = jQuery(".inputstyle").val();//转出金额
		var daynow = jQuery("#daynow").html();//可用金额
		if(daymoney =="" || daymoney == null){			
			jQuery('.daywarn').html('转出金额不能为空！');
			return false;	
		}
		var reg = /.*\..*/;
 	    if(reg.test(daymoney)){
 		   if(daymoney.toString().split(".")[1].length>2){
 			  jQuery('.daywarn').html('转出金额需为2位小数！');
 			  return false;
 		   }
 	    }
		var daymoney = Number(daymoney);//转出金额
		var daynow = Number(daynow);//可用金额
		if(daynow == 0){
			jQuery('.daywarn').html('您余额为0.00,无法转出！');
			return false;
		}
		if(daymoney > daynow){
			jQuery('.daywarn').html('您最多可转出'+daynow+'元！');
			return false;
		}
		if(daymoney == 0){
			jQuery('.daywarn').html('转出金额不能为零！');
			return false;
		}
		 jQuery('.daywarn').html('');
		//loading
	    jQuery('.dayoutload img').css('display', 'block');
		jQuery('.dayoutload input').attr('disabled',"true");
		$.ajax({
			url:url_prefix+"/yuebao/redeem.do?amount="+daymoney,
			type:"post",
			dataType:"json",
			success:function(data){	
				if(data.code==-2) {
					window.location.href=url_prefix+"login.html";
					return;
				}
				//loading取消
				jQuery('.dayoutload img').css('display', 'none');
				jQuery('.dayoutload input').removeAttr('disabled');
				jQuery(".inputstyle").val("");
			     alert(data.info);
				 location.reload();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {					
				 //loading取消
				jQuery('.payload').css('display', 'none');
				jQuery('.paybtn').removeAttr('disabled');
			     //document.body.removeChild(circle.canvas);
                 alert("请求超时，请重试");
            }  
		});
	})
	
	jQuery(".bank_btton").click(function(){		
		var phoneVal=jQuery(".bank_ts2").val();
		var phoneVal1=jQuery(".min_length").val();
		var bankbal=jQuery('.min_width').val();
		if(phoneVal1==""){
			jQuery(".ts1").show();
			return;
		}else if(!/^[0-9]{16,19}$/.test(phoneVal1)){
			jQuery(".ts1").show();
			jQuery(".ts1").html("请正确填写银行卡号！");
			return;
		}else{
			jQuery(".ts1").hide();
		};

		if(phoneVal==""){
			jQuery(".ts2").show();
			return;
		}else if(!/^1[3,4,5,7,8]\d{9}$/.test(phoneVal)){
			jQuery(".ts2").show();
			jQuery(".ts2").html("请正确填写手机号码！");
			return;
		}else{
			jQuery(".ts2").hide();
		};
		if(bankbal==""){
			jQuery(".ts3").show();
			return;
		}else{
			jQuery(".ts3").hide();
		}
	})
});