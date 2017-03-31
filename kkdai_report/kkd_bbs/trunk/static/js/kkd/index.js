var indexFun = {
    obj: jQuery('.laba_l'),
    init: function (num) {
        this.pos(num);
        if(num>1){
            this.lunBoShown(num)
        }
        var _self = this;
        this.autoscrollup(_self.obj,3)

    },
    createDom: function (num) {
        var str = '';
        for (var i = 0; i < num; i++) {
            if (i == 0) {
                str += '<li class="show"></li>';
            } else {
                str += '<li></li>';
            }
            jQuery('#banner_img').css('width', 500 * num + 'px')
        }
        jQuery('.banner .index').html(str);
    },
    pos:function(num){
        var _this = this;
        if(num==1){
            jQuery('.index').hide();
        }else{
            _this.createDom(num);
            var w = parseInt(jQuery('.index').css('width'))+15;
            var ow = 250-parseInt(w/2)
            jQuery('.index').css('left',ow+'px')
        }

    },
    lunBoShown: function (num) {
        var currentNum = 0;
        var timer = null;
        function dingshi() {
            if (currentNum > num - 1) {
                currentNum = 0;
            }
            jQuery('.banner .index li').eq(currentNum).addClass('show').siblings().removeClass();
            jQuery('#banner_img').stop().animate({'left': currentNum * -500}, 800);
            currentNum++;
        }
        clearInterval(timer);
        timer = setInterval(dingshi, 3000);

        jQuery('#banner_img li').hover(function () {
            clearInterval(timer);
        }, function () {
            timer = setInterval(dingshi, 3000);
        });

        jQuery('.banner .index li').click(function () {
            jQuery(this).addClass('show').siblings().removeClass();
            jQuery('#banner_img').stop().animate({'left': jQuery(this).index() * -500}, 800);
            currentNum = jQuery(this).index()+1;
        })

    },
    autoscrollup: function (obj, s) {
        var _this = this;
        jQuery(obj).find("ul:first").animate({marginTop: "-45px"}, 500, function () {
            jQuery(this).css({marginTop: "0px"}).find("li:first").appendTo(this);
        });
        setTimeout(function () {
            _this.autoscrollup(obj, s);
        }, s * 1000);

    }


}
var countImg = jQuery('#banner_img li').length;
if(countImg>=1){
    indexFun.init(countImg);
}
/*
jQuery.fn.on = function(){

}
*/
var alertFK = {
    form : null,
    goon : false,
    url : 'http://bbs.kuaikuaidai.com/forum.php?mod=viewthread&tid=103',
    init : function(form){
        this.form = form;
        var that = this;
        jQuery(document).on('click','.alertFK-btn-cancel',function(e){
            console.log('close...');
            that.closeAlertFK();
        });
        jQuery(document).on('click','.alertFK-btn-goon',function(e){
            console.log('goon...');
            that.goonAlertFK();
        });
    },
    isShowAlert : function(){
        return !this.goon && heiheiheiGlobal.needShowAlert();
    },
    closeAlertFK : function(){
        jQuery('.alertFK').remove();
    },
    goonAlertFK : function(){
        this.goon = true;
        this.closeAlertFK();
        if(this.ajax){
            console.log('ajax...');
            this.ajax = false;
            var data = this.args;
            if(ajaxpost)ajaxpost(data[0],data[1],data[2],data[3],data[4],data[5]);
        }else if(this.form){
            console.log('submit...');
            postpt = 0;
            this.form.submit();
        }
        this.goon = false;
    },
    showAlertFK : function(){
        var str = '<div class="alertFK" style="">'+
            '    <div style="margin-top: 20px;font-size: 14px;color: #919191">提示</div>'+
            '<div style="margin-top: 28px;font-size: 13px;text-align: left;width: 215px;margin-left: auto;margin-right: auto">您的昵称与账号相同，为保护您的个人隐私，建议您修改一个新的昵称后再进行发帖操作</div>'+
            '<div style="margin: 18px;"><a target="_blank" href="'+this.url+'" style="color: blue">如何修改昵称?</a></div>'+
            '<div style="">'+
            '<div class="alertFK-btn alertFK-btn-goon">继续发帖</div>'+
            '<div class="alertFK-btn alertFK-btn-cancel">取消</div>'+
            '<div style="clear: both"></div>'+
            '</div>'+
            '</div>'+
            '<style>'+
            '.alertFK{'+
            '    width: 256px;height: 209px;border: 1px solid;text-align: center;'+
            '    position: fixed;'+
            '    background-color: #ffffff;'+
            '    z-index: 999;'+
            '    left: 0;'+
            '    right: 0;'+
            '    top: 0;'+
            '    bottom: 0;'+
            '    margin: auto;'+
            '}'+
            '.alertFK-btn{'+
            '    float: left;width: 50%;color: #919191;'+
            '    cursor: pointer;'+
            '}'+
            '</style>';
        this.closeAlertFK();
        jQuery('body').append(str);
        return this.goon;
    },
    ajax : false,
    args : [],
    ajaxpost : function(formid, showid, waitid, showidclass, submitbtn, recall){
        this.ajax = true;
        this.args = [formid, showid, waitid, showidclass, submitbtn, recall];
    }
};


//tgh
jQuery(function(){
    alertFK.init(jQuery('#fastpostform,#postform'));
});



