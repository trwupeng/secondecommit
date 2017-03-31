<?php
$html = <<<html
<script  src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<h1>推广-注册页</h1>
<table>
<form action="index/Oauth/webReg" method="get">
<input type="hidden" name="__" value="index/Oauth/webReg">
<tbody>
<tr><td>手机号：</td><td><input type="text" name="phone"></td></tr>
<tr><td>短信验证码：</td><td><input type="text" name="smsCode"></td></tr>
<tr><td>邀请码：</td><td><input type="text" name="invitationCode"></td></tr>
<tr><td class="button"><input type="button" id="ajaxSubmit" value="提交"></td></tr>
</tbody>
</form>
</table>
<script >
$('#ajaxSubmit').click(function() {
var url = 'index.php/oauth/webReg?__VIEW__=json',
data = {
	'phone' : $("[name='phone']").val(),
	'smsCode' : $("[name='smsCode']").val(),
	'invivationCode' : $("[name='invitationCode']").val(),
	'contractId' : 'ssss',
};

for (var key in data) {
	if(!data[key]) {
	delete data[key];
	}
}

$.ajax({
	url:url,
	data:data,
	type:'GET',
	success: function(data) {
	console.log(data);
		if (data.code == 200) {

		} else {
			console.log(data);
		}
	}}
);
});
</script>
html;
echo $html;