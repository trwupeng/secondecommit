function getFont(){
    var html1=document.documentElement;
    var screen=html1.clientWidth;
    if(screen <=320){
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
/**
 * form表单验证
 * @param jQuery.serializeArray() data jQuery序列化后的form
 * @param Object tips tips
 * @returns {key:value}
 * @errors string
 * @author LTM
 */
function validateForms(data, tips) {
    var newData = {},
        tips = {'phone': '手机号', 'smsCode': '验证码', 'password': '密码'};
    for (var i = 0; i < data.length; i++) {
        if (!data[i].value) {
            if (['phone', 'smsCode', 'password'].some(function (elem, index, arr) {
                    return data[i].name == elem;
                })) {
                alert(tips[data[i].name] + '不能为空');
                throw 'error';
            }
        } else {
            var k = data[i].name;
            newData[data[i].name] = data[i].value;
        }
    }
    newData.contractId = getCookie('contractId');
    newData.clientId = 'MTEwNDg3ODM0NGNsaWVudElk';
    newData.clientSecret = 'czIwdkg5ZW1LSjZCbVQxUWNsaWVudFNlY3JldA==';
    newData.protocol = '2';

    return newData;
}

function cookie(name) {
    var cookieArray = document.cookie.split("; "); //得到分割的cookie名值对
    var cookie = new Object();
    for (var i = 0; i < cookieArray.length; i++) {
        var arr = cookieArray[i].split("=");       //将名和值分开
        if (arr[0] == name)
            return decodeURI(arr[1]); //如果是指定的cookie，则返回它的值
    }
    return "";
}

function addCookie(objName, objValue, objHours, domain) {      //添加cookie
    var str = objName + "=" + encodeURI(objValue);
    if (objHours > 0) {                               //为时不设定过期时间，浏览器关闭时cookie自动消失
        var date = new Date();
        var ms = objHours * 3600 * 1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString() + "; domain=" + domain + "; path=/";
    }
    document.cookie = str;
}

function SetCookie(name, value)//两个参数，一个是cookie的名子，一个是值
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + encodeURI(value) + ";expires=" + exp.toGMTString();
}

function getCookie(objName) {//获取指定名称的cookie的值
    var arrStr = document.cookie.split("; ");
    for (var i = 0; i < arrStr.length; i++) {
        var temp = arrStr[i].split("=");
        if (temp[0] == objName)
            return decodeURI(temp[1]);
    }
}

function delCookie(name)//删除cookie
{
    document.cookie = name + "=;expires=" + (new Date(0)).toGMTString();
}