/*********************************************************************************
* 功能：判断字符是否为空字符或null值
* 参数：字符串
**********************************************************************************/
function isEmpty (str) {
	if ((str==null)||(str.length==0)) return true;
	else return(false);
}
function isNotEmpty(str) {
	if ((str == null) || (str.length == 0)) return false;
	return true;
}
/*********************************************************************************
* 功能：判断校验数字：正数
* 参数：字符串
**********************************************************************************/
function isDigit(s)
{
	var patrn=/^\d+(\.\d+)?$/;
	if (!patrn.exec(s)) return false
		return true
}
 /*********************************************************************************
* 功能：校验手机号码
* 参数：字符串
**********************************************************************************/
function isMobile(s)
{
	var patrn=/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/i;
	if (!patrn.exec(s)) return false
		return true
}

$(function(){
	$('.validate').click(function() {
		$(this).closest('div').next('.tishi').text('').hide();
		$(this).removeClass("ipt_wrong").addClass("hh");
	});

	$('.validatePassword').blur(function() {
		var str = $(this).val();
		if (!isEmpty(str)) {
			if(str.length < 6 || str.length > 16) {
				$(this).closest('div').next('.tishi').text("请输入6到16位由数字，字母组成的密码").show();
				return false;
			}
			var pattern = /^[a-zA-Z0-9]+$/;
			if(!str.match(pattern)) {
				$(this).closest('div').next('.tishi').text("请输入6到16位由数字，字母组成的密码").show();
				return false;
			}
		}
		$(this).closest('div').next('.tishi').text('').hide();
		$(this).removeClass("ipt_wrong").addClass("hh");
		return false
	});
	$('.validateCode').blur(function() {
		var str = $(this).val();
		if (!isEmpty(str)) {
			if (str.length != 6) {
				$(this).closest('div').next('.tishi').text('验证码输入不正确').show();
				return false;
			} else {
				if (!isDigit(str)) {
					$(this).closest('div').next('.tishi').text('验证码输入不正确').show();
					return false;
				}
			}
		}
		$(this).closest('div').next('.tishi').text('').hide();
		$(this).removeClass("ipt_wrong").addClass("hh");
		return false
	});
	$('.validatePhone').blur(function(){
		var str = $(this).val();
		if (!isEmpty(str)) {
			if (isDigit(str)) {
				if (!isMobile(str)) {
					$(this).closest('div').next('.tishi').text('请输入13、14、15、17、18开头的11位手机号码').show();
					return false
				}
			} else {
				$(this).closest('div').next('.tishi').text('请输入13、14、15、17、18开头的11位手机号码').show();
				return false
			}
		}
		$(this).closest('div').next('.tishi').text('').hide();
		$(this).removeClass("ipt_wrong").addClass("hh");
		return false
	});
});

function validateForm(obj){
	var form = $(obj).closest('form');
	var bValidateResult = [];
	bValidateResult['error'] = 0;
	form.find('.validatePassword').each(function(){
		var str = $(this).val();
		if (!isEmpty(str)) {
			if(str.length < 6 || str.length > 16){
				$(this).closest('div').next('.tishi').text("请输入6到16位由数字，字母组成的密码").show();
				bValidateResult['error'] += 1;
			}
			var pattern = /^[a-zA-Z0-9]+$/;
			if(!str.match(pattern)){
				$(this).closest('div').next('.tishi').text("请输入6到16位由数字，字母组成的密码").show();
				bValidateResult['error'] += 1;
			}
		} else {
			$(this).closest('div').next('.tishi').text("请输入6到16位由数字，字母组成的密码").show();
			bValidateResult['error'] += 1;
		}
	});
	form.find('.validatePhone').each(function(){
		var str = $(this).val();
		if (!isEmpty(str)) {    
			if (isDigit(str)) {
				if (!isMobile(str)) {
					$(this).closest('div').next('.tishi').text('请输入13、14、15、17、18开头的11位手机号码').show();
					bValidateResult['error'] =+ 1;
				}
			} else {
				$(this).closest('div').next('.tishi').text('请输入13、14、15、17、18开头的11位手机号码').show();
				bValidateResult['error'] += 1;
			}
		} else {
			$(this).closest('div').next('.tishi').text('请输入13、14、15、17、18开头的11位手机号码').show();
			bValidateResult['error'] += 1;
		}
	});
	form.find('.validateCode').each(function(){
		var str = $(this).val();
		if (!isEmpty(str)) {
			if (str.length != 6) {
	            $(this).closest('div').next('.tishi').text('验证码输入不正确').show();
	            bValidateResult['error'] += 1;
	        } else {
	            if (!isDigit(str)) {
	                $(this).closest('div').next('.tishi').text('验证码输入不正确').show();
	                bValidateResult['error'] += 1;
	            }
	        }
		} else {
			$(this).closest('div').next('.tishi').text('验证码输入不正确').show();
            bValidateResult['error'] += 1;
		}
	});
	return bValidateResult;
}