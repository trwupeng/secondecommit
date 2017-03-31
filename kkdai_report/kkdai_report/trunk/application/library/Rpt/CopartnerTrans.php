<?php
namespace Rpt;
/**
 *
 * 渠道ID以及协议
 *
 */

class CopartnerTrans {

//    public static $contracts = [
//        '360'=>'100020150101100001',
//        '360zhushou_03'=>'103920150101100001',
//        '360tuiguang_02'=>'103920160129200000',
//        'anzhi_08'=>'100120150101100001',
//        'baisibudejie_13'=>'100220150101100001',
//        'baisibudejietuiguang_14'=>'100220150101100002',
//        'cankaoxiaoxi  '=>'100320150101100001',
//        'cankaoxiaoxi'=>'100320150101100001',
//        'cankaoxiaoxi                     '=>'100320150101100001',
//        'ceshi'=>'100420150101100001',
//        'csai'=>'100520150101100001',
//        'dayuwang'=>'100620150101100001',
//        'dayuwang-feed'=>'100620150101100002',
//        'dayuwang-pc'=>'100620150101100003',
//        'duanxin'=>'100720150101100001',
//        'edm'=>'100820150101100001',
//        'fensitong'=>'100920150101100001',
//        'fuwuduan_11'=>'999920150101000000',
//        'kehuduan-1'=>'999920150101000000',
//        'kehuduan-3'=>'999920150101000000',
//        'kehuduan-2'=>'999920150101000000',
//        'fuyi0923'=>'102520150101100000',
//        'huawei_06'=>'101220150101100001',
//        'huisuoping'=>'101320150101100001',
//        'huisuoping-1'=>'101320150101100002',
//        'huisuoping-2'=>'101320150101100003',
//        'huisuoping-3'=>'101320150101100004',
//        'huisuopingtuiguang_13'=>'101320160129200000',
//        'huying'=>'101420150101100001',
//        'huyingg'=>'101420150101100002',
//        'jifeng_19'=>'104820160129200000',
//        'jinritoutiaotuiguang_11'=>'101520150101100001',
//        'kuhuasuopin'=>'101720150101100001',
//        'kuhuasuoping'=>'101720150101100002',
//        'kuhuasuoping-1'=>'101720150101100003',
//        'kuhuasuoping-2'=>'101720150101100004',
//        'kuhuasuoping-3'=>'101720150101100005',
//        'leshangdian_26'=>'105520160129200000',
//        'meizu_07'=>'101820150101100001',
//        'mmshangcheng_23'=>'105220160129200000',
//        'mumayi_22'=>'105120160129200000',
//        'nduo_20'=>'104920160129200000',
//        'neihanduanzituiguang_15'=>'101920150101100001',
//        'oppoyingyongshangdian_17'=>'104620160129200000',
//        'ppzhushou_10'=>'102020150101100001',
//        'qmmf'=>'102120150101100001',
//        'sanxing_09'=>'102220150101100001',
//        'shougouzhushou_12'=>'102320150101100001',
//        'sogou'=>'104320150101100002',
//        'sogou1'=>'104320150101100003',
//        'sogou2'=>'104320150101100004',
//        'sogou3'=>'104320150101100005',
//        'sogou4'=>'104320150101100006',
//        'sogou5'=>'104320150101100007',
//        'sogou6'=>'104320150101100008',
//        'shouhuhuisuantuiguang_10'=>'102420150101100001',
//        'sohohuisuan'=>'102420150101100002',
//        'sohuhuisuan'=>'102420150101100003',
//        'sinafuyi'=>'102520150101100001',
//        'sinafuyi_sucai1'=>'102520150101100002',
//        'sinafuyi_sucai10'=>'102520150101100003',
//        'sinafuyi_sucai11'=>'102520150101100004',
//        'sinafuyi_sucai12'=>'102520150101100005',
//        'sinafuyi_sucai13'=>'102520150101100006',
//        'sinafuyi_sucai14'=>'102520150101100007',
//        'sinafuyi_sucai15'=>'102520150101100008',
//        'sinafuyi_sucai17'=>'102520150101100009',
//        'sinafuyi_sucai18'=>'102520150101100010',
//        'sinafuyi_sucai2'=>'102520150101100011',
//        'sinafuyi_sucai3'=>'102520150101100012',
//        'sinafuyi_sucai4'=>'102520150101100013',
//        'sinafuyi_sucai5'=>'102520150101100014',
//        'sinafuyi_sucai9'=>'102520150101100015',
//        'sinafuyi01'=>'102520150101100016',
//        'sinafuyi0923_sc1'=>'102520150101100017',
//        'sinafuyi0923_sc2'=>'102520150101100018',
//        'sinafuyi0923_sc3'=>'102520150101100019',
//        'sinafuyi0923_sc4'=>'102520150101100020',
//        'sinafuyiAPP1'=>'102520150101100021',
//        'sinafuyiAPP2'=>'102520150101100022',
//        'sinafuyiAPP3'=>'102520150101100023',
//        'sinafuyiAPP4'=>'102520150101100024',
//        'sinafuyiAPP5'=>'102520150101100025',
//        'sinafuyi-sc1'=>'102520150101100026',
//        'sinafuyi-sc2'=>'102520150101100027',
//        'sinafuyi-sc3'=>'102520150101100028',
//        'sinafuyi-sc4'=>'102520150101100029',
//        'sinafuyi-sc5'=>'102520150101100030',
//        'sinafuyi-sc6'=>'102520150101100031',
//        'sinafuyi-sc7'=>'102520150101100032',
//        'sinafuyiWAP01'=>'102520150101100033',
//        'sinafuyiWAP03'=>'102520150101100034',
//        'sinafuyiWAP1'=>'102520150101100035',
//        'sinafuyiWAP2'=>'102520150101100036',
//        'sinafuyiWAP2-2'=>'102520150101100037',
//        'sinafuyiWAP2-sogou'=>'102520150101100038',
//        'sinafuyiWAP3'=>'102520150101100039',
//        'sinafuyiwap4'=>'102520150101100040',
//        'sinafuyiWAP4'=>'102520150101100040',
//        'sinafuyiwap5'=>'102520150101100041',
//        'sinafuyiWAP5'=>'102520150101100041',
//        'sinafuyiwap7'=>'102520150101100042',
//        'sinafuyiwap8'=>'102520150101100043',
//        'tianyikongjian_25'=>'105420160129200000',
//        'xinlangfuyituiguang_05'=>'102520160129200000',
//        'weibo'=>'104020150101100044',
//        'wandoujia_02'=>'102720150101100001',
//        'wannianli'=>'102820150101100001',
//        'wannianli-1'=>'102820150101100002',
//        'wannianli-2'=>'102820150101100003',
//        'wannianli-3'=>'102820150101100004',
//        'weibotuiguang_08'=>'104020160129200000',
//        'weixin'=>'102920150101100001',
//        'weixintuiguang_07'=>'102920160129200000',
//        'wifiwannengyaoshi_27'=>'105620160129200000',
//        'woshangcheng_24'=>'105320160129200000',
//        'yingyongbao_01'=>'104120150101100001',
//        'yingyongbaoshoufa_01'=>'104120150101100002',
//        'xiaomi_05'=>'103020150101100001',
//        'xinwengao'=>'103120150101100001',
//        'xunfei'=>'103220150101100001',
//        'yidianzixun'=>'103320150101100001',
//        'yingyonghui_18'=>'104720160129200000',
//        'youdao'=>'103520150101100001',
//        'youdao-baidumotuAndroid'=>'103520150101100002',
//        'youdao-baidumotuAndroid-1'=>'103520150101100003',
//        'youdao-baidumotuAndroid-2'=>'103520150101100004',
//        'youdao-baidumotuAndroid-3'=>'103520150101100005',
//        'youdao-baidumotuios'=>'103520150101100006',
//        'youdao-baidumotuios-1'=>'103520150101100007',
//        'youdao-baidumotuios-2'=>'103520150101100008',
//        'youdao-baidumotuios-3'=>'103520150101100009',
//        'youdao-baixingwangandroid'=>'103520150101100010',
//        'youdao-baixingwangandroid-1'=>'103520150101100011',
//        'youdao-baixingwangios'=>'103520150101100012',
//        'youdaobaofeng'=>'103520150101100012',
//        'youdao-baofeng'=>'103520150101100014',
//        'youdaochacha'=>'103520150101100015',
//        'youdaodiannaoios'=>'103520150101100016',
//        'youdaodsp'=>'103520150101100017',
//        'youdao-fenghuo'=>'103520150101100018',
//        'youdaofengyun'=>'103520150101100019',
//        'youdaohaodou'=>'103520150101100020',
//        'youdaohaodou12'=>'103520150101100021',
//        'youdaohaodou1212'=>'103520150101100022',
//        'youdao-haodouandroid'=>'103520150101100023',
//        'youdao-haodouandroid-1'=>'103520150101100024',
//        'youdaohaodouanzhuo'=>'103520150101100025',
//        'youdao-haodoubanner'=>'103520150101100026',
//        'youdaohaodouios'=>'103520150101100027',
//        'youdao-haodouios'=>'103520150101100028',
//        'youdao-haodouios-1'=>'103520150101100029',
//        'youdaojiaxiao'=>'103520150101100030',
//        'youdaokaiping'=>'103520150101100031',
//        'youdao-kaixun-1'=>'103520150101100032',
//        'youdaokum'=>'103520150101100033',
//        'youdaomotuanzhuo'=>'103520150101100034',
//        'youdao-motuanzhuo'=>'103520150101100035',
//        'youdaomotuios'=>'103520150101100036',
//        'youdao-motuios'=>'103520150101100037',
//        'youdaopingan'=>'103520150101100038',
//        'youdaoshouji'=>'103520150101100039',
//        'youdaoshouji1212'=>'103520150101100040',
//        'youdaotaipingyang'=>'103520150101100041',
//        'youdao-taipingyangandroid'=>'103520150101100042',
//        'youdao-taipingyangios'=>'103520150101100043',
//        'youdaotianqi'=>'103520150101100044',
//        'youdaowifi'=>'103520150101100045',
//        'youdaoyixin'=>'103520150101100046',
//        'youdao-yixin'=>'103520150101100047',
//        'youdao-yixin-1'=>'103520150101100048',
//        'youdao-yixin-2'=>'103520150101100049',
//        'youdao-yixin-3'=>'103520150101100050',
//        'youdaoyixinanzhuo'=>'103520150101100051',
//        'youdaoyixinios'=>'103520150101100052',
//        'youdaoyouliao-1'=>'103520150101100053',
//        'youdao-youting'=>'103520150101100054',
//        'youdaoyun'=>'103520150101100055',
//        'youting'=>'103620150101100001',
//        'youyi_21'=>'105020160129200000',
//        'zdby'=>'103720150101100001',
//        'spider'=>'103820150101100001',
//        'baiduzhushou_04'=>'102620150101100001',
//        'tieba'=>'104220150101100002',
//        'tieba_baobao'=>'104220150101100003',
//        'tieba_jingpin'=>'104220150101100004',
//        'tieba_qianbao'=>'104220150101100005',
//        'tiebatuiguang_09'=>'104220160129200000',
//        'baidum_28'=>'105820160205210000',
//    ];

    public static function getTransedCopartners () {
        return \Sooh\Db\Broker::getInstance(\Rpt\Tbname::db_rpt)
                ->getPair(\Rpt\Tbname::tb_copartners_trans, 'copartnerName', 'contractId');
    }

    public static function transContractId ($source) {
        $contracts = self::getTransedCopartners();
        if (isset($contracts[$source])) {
            return $contracts[$source];
        }elseif (strlen($source) == 18 && is_numeric($source)) {
            return $source;
        }
    }

    public static function transCopartnerId ($source) {
        $contractId = self::transContractId($source);
        return substr($contractId, 0, 4);
    }
}