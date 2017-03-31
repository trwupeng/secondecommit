//var __CONF__ = {
  //  'sendIdentCodeUrl':'http://www.kuaikuaidai.com/h5/sendCode.do',
   // 'regUrl':'http://www.kuaikuaidai.com/h5/register2.do',
    //'loginUrl':'http://www.kuaikuaidai.com/h5/login2.do',
   // 'loginWebUrl':'http://www.kuaikuaidai.com/login2.do',
   // 'regSucJumpUrl':'http://www.kuaikuaidai.com/h5/index.html',
   // 'regSucJumpWebUrl':'http://www.kuaikuaidai.com/index.html',
   // 'rootUrl':'http://act.kuaikuaidai.com/spread/',
//};
 var __CONF__ = {
     'sendIdentCodeUrl':'http://61.152.93.181/phoenix-h5-server/sendCode.do',
     'regUrl':'http://61.152.93.181/phoenix-h5-server/register2.do',
     'loginUrl':'http://61.152.93.181/phoenix-h5-server/login2.do',
    'loginWebUrl':'http://61.152.93.181/login2.do',
     'regSucJumpUrl':'http://61.152.93.181:9080/phoenix-h5-server/',
     'regSucJumpWebUrl':'http://61.152.93.181/index.html',
     'rootUrl':'http://120.55.84.244/spread/',
 };

function changeToWap()
{
    var parts = top.location.href.split('/');
    for (var i in parts )
    {
        if(parts[i]=='spread'){
            i = parseInt(i);
            var copartner = parts[i+1];
            var dtid = parts[i+2];
            var file = parts[i+3];
            var mod = parseInt(dtid)%100;
            dtid = dtid-mod+30+(mod%10);
            top.location.href='/spread/'+copartner+'/'+dtid+'/'+file;
            break;
        }
    }
}

function changeToWeb()
{
    var parts = top.location.href.split('/');
    for (var i in parts )
    {
        if(parts[i]=='spread'){
            i = parseInt(i);
            var copartner = parts[i+1];
            var dtid = parts[i+2];
            var file = parts[i+3];
            var mod = parseInt(dtid)%100;
            dtid = dtid-mod+(mod%10);
            top.location.href='/spread/'+copartner+'/'+dtid+'/'+file;
            break;
        }
    }
}

function getContractId()
{
    var parts = top.location.href.split('/');
    for (var i in parts )
    {
        if(parts[i]=='spread'){
            i = parseInt(i);
            var copartner = parseInt(parts[i+1]);
            if(copartner==0)return 0;
            else{
                var dtid = parts[i+2];
                var file = parts[i+3];
                return copartner+dtid+file.substr(0,4);
            }
        }
    }
}