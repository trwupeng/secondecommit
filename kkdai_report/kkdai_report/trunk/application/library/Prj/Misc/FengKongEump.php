<?php

namespace Prj\Misc;

/**
 * 风控需求中，枚举类型的管理与解析
 * Class FengKongEnup
 * @package Prj\Misc
 */
class FengKongEump
{
    private static $_instance;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * 获取枚举值
     * @param $name <br>
     *              1.$this->enumfuxifeifangshi;<br>
     *              2.$this->b101;
     * @return mixed
     */
    public function get($name)
    {
        if (preg_match('/^[a-z]{1}\d{3}$/', $name) == 1) {
            $name = $this->numberToNameMap[$name][1];
        }

        $attrName = 'enum' . strtolower($name);

        if (isset($this->$attrName)) {
            $tmpArr = $this->$attrName;
            //加入默认头
            if (!isset($tmpArr[0])) {
                $tmpArr[0] = '未选择';
            }
            ksort($tmpArr);
            return $tmpArr;
        } else {
            error_log('get attribute not found:' . __CLASS__ . '->' . $name);
            return false;
        }
    }
    
    /**
     * 线下本息费账--种类
     * @var array
     */
    protected $enumzhonglei = [
        1 => '利息',
        2 => '费用',
        3 => '息费',
        4 => '本金',
    ];

    /**
     * 融资客户名册--在保情况
     * @var array
     */
    protected $enumzaibaoqingkuang = [
        1 => '是',
        2 => '否',
   
    ];

    /**
     * 融回访方式--回访方式
     * @var array
     */
    protected $enumhuifangfangshi = [
        1 => '上门面谈',
        2 => '公司面谈',
        3 => '电话',
    ];

    /**
     * 电销提奖表--业务类型
     * @var array
     */
    protected $enumyewuleixing = [
        1 => '有抵押',
        2 => '无抵押',
    ];

    /**
     * 电销提奖表--级别
     * @var array
     */
    protected $enumjibie = [
        1 => '经理',
        2 => '主任',
        3 => '业务员',
    ];
 
    /**
     * 表格编号的映射关系
     * @var array
     */
    protected $numberToNameMap = [
        'd101' => [
            0 => 'liuChengDan',
            1 => 'hetongbianhao',
            2 => '合同编号',
        ],
        'd102' => [
            0 => 'liuChengDan',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'd103' => [
            0 => 'liuChengDan',
            1 => 'pipeizijinqingkuang',
            2 => '匹配资金情况',
        ],
        'd104' => [
            0 => 'liuChengDan',
            1 => 'fangkuanshijian',
            2 => '放款时间',
        ],
        'd105' => [
            0 => 'liuChengDan',
            1 => 'xianxiayifangkuan',
            2 => '线下已放款',
        ],
        'd106' => [
            0 => 'liuChengDan',
            1 => 'fangkuanshijian8',
            2 => '放款时间8',
        ],
        'd107' => [
            0 => 'liuChengDan',
            1 => 'xianshangyifangkuan',
            2 => '线上已放款',
        ],
        'd108' => [
            0 => 'liuChengDan',
            1 => 'fangkuanshijian10',
            2 => '放款时间10',
        ],
        'd109' => [
            0 => 'liuChengDan',
            1 => 'jibenxinxiyilu',
            2 => '基本信息已录',
        ],
        'd110' => [
            0 => 'liuChengDan',
            1 => 'chulishijian',
            2 => '处理时间',
        ],
        'd111' => [
            0 => 'liuChengDan',
            1 => 'xiangxixinxiyilu',
            2 => '详细信息已录',
        ],
        'd112' => [
            0 => 'liuChengDan',
            1 => 'chulishijian14',
            2 => '处理时间14',
        ],
        'd113' => [
            0 => 'liuChengDan',
            1 => 'tijiangyihesuan',
            2 => '提奖已核算',
        ],
        'd114' => [
            0 => 'liuChengDan',
            1 => 'chulishijian16',
            2 => '处理时间16',
        ],
        'd115' => [
            0 => 'liuChengDan',
            1 => 'xiangmuyiguidang',
            2 => '项目已归档',
        ],
        'd116' => [
            0 => 'liuChengDan',
            1 => 'chulishijian18',
            2 => '处理时间18',
        ],
        'd117' => [
            0 => 'liuChengDan',
            1 => 'yishenhe',
            2 => '已审核',
        ],
        'd118' => [
            0 => 'liuChengDan',
            1 => 'chulishijian20',
            2 => '处理时间20',
        ],
        'b101' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'b102' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'b103' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'diyaren',
            2 => '抵押人',
        ],
        'b104' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'baozhengren',
            2 => '保证人',
        ],
        'b105' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'lianxidianhua',
            2 => '联系电话',
        ],
        'b106' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'bianhao',
            2 => '编号',
        ],
        'b107' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'jiekuanewanyuan',
            2 => '借款额[万元]',
        ],
        'b108' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'qishiriqi',
            2 => '起始日期',
        ],
        'b109' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'yue',
            2 => '月',
        ],
        'b110' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'tian',
            2 => '天',
        ],
        'b111' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'daoqiriqi',
            2 => '到期日期',
        ],
        'b112' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuxifeifangshi',
            2 => '付息费方式',
        ],
        'b113' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuxiri',
            2 => '付息日',
        ],
        'b114' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'yewuleixing',
            2 => '业务类型',
        ],
        'b115' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'kehuleixing',
            2 => '客户类型',
        ],
        'b116' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'kehulaiyuan',
            2 => '客户来源',
        ],
        'b117' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'lixiyue',
            2 => '利息_月[%]',
        ],
        'b118' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'lixiyingshou',
            2 => '利息_应收[%]',
        ],
        'b119' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'lixiyingshouyuan',
            2 => '利息_应收[元]',
        ],
        'b120' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'lixishishouyuan',
            2 => '利息_实收[元]',
        ],
        'b121' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuwufeianyue',
            2 => '服务费_按月[%]',
        ],
        'b122' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuwufeiyicixingyue',
            2 => '服务费_一次性[%/月]',
        ],
        'b123' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuwufeiyingshouyuan',
            2 => '服务费_应收[元]',
        ],
        'b124' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fuwufeishishouyuan',
            2 => '服务费_实收[元]',
        ],
        'b125' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'dianzifeijineyuan',
            2 => '垫资费_金额[元]',
        ],
        'b126' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'dianzifeibilv',
            2 => '垫资费_比率[%]',
        ],
        'b127' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'zhongjiefeiyue',
            2 => '中介费_月[%]',
        ],
        'b128' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'zhongjiefeizong',
            2 => '中介费_总[%]',
        ],
        'b129' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'zhongjiefeiyingshouyuan',
            2 => '中介费_应收[元]',
        ],
        'b130' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'zhongjiefeishishouyuan',
            2 => '中介费_实收[元]',
        ],
        'b131' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'zongheyue',
            2 => '综合[%/月]',
        ],
        'b132' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'yuexifeilv',
            2 => '月息费率[%]',
        ],
        'b133' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'baozhengjin',
            2 => '保证金[%]',
        ],
        'b134' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'baozhengjinyuan',
            2 => '保证金[元]',
        ],
        'b135' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fangchanleixing',
            2 => '房产类型',
        ],
        'b136' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fangchanquyu',
            2 => '房产区域',
        ],
        'b137' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fangchanweizhi',
            2 => '房产位置',
        ],
        'b138' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'gongzheng',
            2 => '公证',
        ],
        'b139' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'quanweidaoqiri',
            2 => '全委[到期日]',
        ],
        'b140' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fangkuanrenyinhang',
            2 => '放款人[银行]',
        ],
        'b141' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'kehujingli',
            2 => '客户经理',
        ],
        'b142' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'tuandui',
            2 => '团队',
        ],
        'b143' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'fengkongjingli',
            2 => '风控经理',
        ],
        'b144' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'jieqingqingkuang',
            2 => '结清情况',
        ],
        'b145' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'jieqingriqi',
            2 => '结清日期',
        ],
        'b146' => [
            0 => 'rongZiXiangMuBiao',
            1 => 'beizhu',
            2 => '备注',
        ],
        'z101' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'z102' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'z103' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'qishu',
            2 => '期数',
        ],
        'z104' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'zhonglei',
            2 => '种类',
        ],
        'z105' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yingfujineyuan',
            2 => '应付金额[元]',
        ],
        'z106' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yingfushijian',
            2 => '应付时间',
        ],
        'z107' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yifujineyuan',
            2 => '已付金额[元]',
        ],
        'z108' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'qianfuyincang',
            2 => '欠付[隐藏]',
        ],
        'z109' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yuqitianshu',
            2 => '逾期天数',
        ],
        'z110' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yuqililv',
            2 => '逾期利率[%]',
        ],
        'z111' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'yuqifeiyuan',
            2 => '逾期费[元]',
        ],
        'z112' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'qianfujineyuan',
            2 => '欠付金额[元]',
        ],
        'z113' => [
            0 => 'xianXiaBenXiFeiZhang',
            1 => 'beizhu',
            2 => '备注',
        ],
        'z201' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'riqi',
            2 => '日期',
        ],
        'z202' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'zhonglei',
            2 => '种类',
        ],
        'z203' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'hetongbianhao',
            2 => '合同编号',
        ],
        'z204' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'kehu',
            2 => '客户',
        ],
        'z205' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'jineyuan',
            2 => '金额[元]',
        ],
        'z206' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'zhanghu',
            2 => '账户',
        ],
        'z207' => [
            0 => 'xianXiaRiJiZhang',
            1 => 'beizhu',
            2 => '备注',
        ],
        'b201' => [
            0 => 'rongZiDangAn',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'b202' => [
            0 => 'rongZiDangAn',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'b203' => [
            0 => 'rongZiDangAn',
            1 => 'jiekuanjinewanyuan',
            2 => '借款金额[万元]',
        ],
        'b204' => [
            0 => 'rongZiDangAn',
            1 => 'yewuleixing',
            2 => '业务类型',
        ],
        'b205' => [
            0 => 'rongZiDangAn',
            1 => 'danganbianhao',
            2 => '档案编号',
        ],
        'b206' => [
            0 => 'rongZiDangAn',
            1 => 'fangchanzhengliuzhi',
            2 => '房产证留置',
        ],
        'b207' => [
            0 => 'rongZiDangAn',
            1 => 'qitaliuzhiwu',
            2 => '其他留置物',
        ],
        'b208' => [
            0 => 'rongZiDangAn',
            1 => 'chanquanren',
            2 => '产权人',
        ],
        'b209' => [
            0 => 'rongZiDangAn',
            1 => 'fangchandizhi',
            2 => '房产地址',
        ],
        'b210' => [
            0 => 'rongZiDangAn',
            1 => 'fangchanzhengbianhao',
            2 => '房产证编号',
        ],
        'b211' => [
            0 => 'rongZiDangAn',
            1 => 'taxiangquanzhengbianhao',
            2 => '他项权证编号',
        ],
        'b212' => [
            0 => 'rongZiDangAn',
            1 => 'tazhengqishiri',
            2 => '他证起始日',
        ],
        'b213' => [
            0 => 'rongZiDangAn',
            1 => 'tazhengdaoqiri',
            2 => '他证到期日',
        ],
        'b214' => [
            0 => 'rongZiDangAn',
            1 => 'jiekuanhetong',
            2 => '借款合同',
        ],
        'b215' => [
            0 => 'rongZiDangAn',
            1 => 'buchongxieyi',
            2 => '补充协议',
        ],
        'b216' => [
            0 => 'rongZiDangAn',
            1 => 'zhuanzhangpingtiao',
            2 => '转账凭条',
        ],
        'b217' => [
            0 => 'rongZiDangAn',
            1 => 'qitaziliao',
            2 => '其他资料',
        ],
        'b218' => [
            0 => 'rongZiDangAn',
            1 => 'tazhengzhuxiaoshijian',
            2 => '他证注销时间',
        ],
        'b219' => [
            0 => 'rongZiDangAn',
            1 => 'beizhu',
            2 => '备注',
        ],
        'm101' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'bianhao',
            2 => '编号',
        ],
        'm102' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'xingming',
            2 => '姓名',
        ],
        'm103' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'weihuren',
            2 => '维护人',
        ],
        'm104' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'guishuren',
            2 => '归属人',
        ],
        'm105' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'yuanguishu',
            2 => '原归属',
        ],
        'm106' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'jieshaoren',
            2 => '介绍人',
        ],
        'm107' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'zaibaoqingkuang',
            2 => '在保情况',
        ],
        'm108' => [
            0 => 'rongZiKeHuMingCe',
            1 => 'jieqingriqi',
            2 => '结清日期',
        ],
        'b301' => [
            0 => 'rongRenQiXinXi',
            1 => 'kehubianhao',
            2 => '客户编号',
        ],
        'b302' => [
            0 => 'rongRenQiXinXi',
            1 => 'kehu',
            2 => '客户',
        ],
        'b303' => [
            0 => 'rongRenQiXinXi',
            1 => 'leixing',
            2 => '类型',
        ],
        'b304' => [
            0 => 'rongRenQiXinXi',
            1 => 'ming',
            2 => '名',
        ],
        'b305' => [
            0 => 'rongRenQiXinXi',
            1 => 'guanxi',
            2 => '关系',
        ],
        'b306' => [
            0 => 'rongRenQiXinXi',
            1 => 'zhengjianhaoma',
            2 => '证件号码',
        ],
        'b307' => [
            0 => 'rongRenQiXinXi',
            1 => 'xingbie',
            2 => '性别',
        ],
        'b308' => [
            0 => 'rongRenQiXinXi',
            1 => 'nianling',
            2 => '年龄',
        ],
        'b309' => [
            0 => 'rongRenQiXinXi',
            1 => 'hunyinzhuangkuang',
            2 => '婚姻状况',
        ],
        'b310' => [
            0 => 'rongRenQiXinXi',
            1 => 'lianxidianhua',
            2 => '联系电话',
        ],
        'b311' => [
            0 => 'rongRenQiXinXi',
            1 => 'xianzhuzhi',
            2 => '现住址',
        ],
        'b312' => [
            0 => 'rongRenQiXinXi',
            1 => 'hujidi',
            2 => '户籍地',
        ],
        'b313' => [
            0 => 'rongRenQiXinXi',
            1 => 'gongzuodanwei',
            2 => '工作单位',
        ],
        'b314' => [
            0 => 'rongRenQiXinXi',
            1 => 'danweidizhi',
            2 => '单位地址',
        ],
        'b315' => [
            0 => 'rongRenQiXinXi',
            1 => 'fadingdaibiaoren',
            2 => '法定代表人',
        ],
        'b316' => [
            0 => 'rongRenQiXinXi',
            1 => 'shijikongzhiren',
            2 => '实际控制人',
        ],
        'b317' => [
            0 => 'rongRenQiXinXi',
            1 => 'guquanjiegou',
            2 => '股权结构',
        ],
        'b318' => [
            0 => 'rongRenQiXinXi',
            1 => 'bangongdizhi',
            2 => '办公地址',
        ],
        'b319' => [
            0 => 'rongRenQiXinXi',
            1 => 'beizhixingchaxunshijian',
            2 => '被执行查询时间',
        ],
        'b320' => [
            0 => 'rongRenQiXinXi',
            1 => 'zhengxinchaxunshijian',
            2 => '征信查询时间',
        ],
        'b321' => [
            0 => 'rongRenQiXinXi',
            1 => 'beizhu',
            2 => '备注',
        ],
        'b401' => [
            0 => 'rongFangChanXinXi',
            1 => 'kehubianhao',
            2 => '客户编号',
        ],
        'b402' => [
            0 => 'rongFangChanXinXi',
            1 => 'kehu',
            2 => '客户',
        ],
        'b403' => [
            0 => 'rongFangChanXinXi',
            1 => 'fangchanquyu',
            2 => '房产区域',
        ],
        'b404' => [
            0 => 'rongFangChanXinXi',
            1 => 'fangchandizhi',
            2 => '房产地址',
        ],
        'b405' => [
            0 => 'rongFangChanXinXi',
            1 => 'chanquanren',
            2 => '产权人',
        ],
        'b406' => [
            0 => 'rongFangChanXinXi',
            1 => 'mianji',
            2 => '面积[㎡]',
        ],
        'b407' => [
            0 => 'rongFangChanXinXi',
            1 => 'fangchanleixing',
            2 => '房产类型',
        ],
        'b408' => [
            0 => 'rongFangChanXinXi',
            1 => 'shifouxuweihu',
            2 => '是否需维护',
        ],
        'b409' => [
            0 => 'rongFangChanXinXi',
            1 => 'shifoudiya',
            2 => '是否抵押',
        ],
        'b410' => [
            0 => 'rongFangChanXinXi',
            1 => 'pingguzhiwanyuan',
            2 => '评估值[万元]',
        ],
        'b411' => [
            0 => 'rongFangChanXinXi',
            1 => 'pinggushijian',
            2 => '评估时间',
        ],
        'b412' => [
            0 => 'rongFangChanXinXi',
            1 => 'yinhangdiyae',
            2 => '银行抵押额',
        ],
        'b413' => [
            0 => 'rongFangChanXinXi',
            1 => 'yinhangshengyue',
            2 => '银行剩余额',
        ],
        'b414' => [
            0 => 'rongFangChanXinXi',
            1 => 'jiekuane',
            2 => '借款额',
        ],
        'b415' => [
            0 => 'rongFangChanXinXi',
            1 => 'diyalv',
            2 => '抵押率[%]',
        ],
        'b416' => [
            0 => 'rongFangChanXinXi',
            1 => 'jiekuandaoqishijian',
            2 => '借款到期时间',
        ],
        'b417' => [
            0 => 'rongFangChanXinXi',
            1 => 'chandiaochaxunshijian',
            2 => '产调查询时间',
        ],
        'b418' => [
            0 => 'rongFangChanXinXi',
            1 => 'xiacichaxunshijian',
            2 => '下次查询时间',
        ],
        'b419' => [
            0 => 'rongFangChanXinXi',
            1 => 'beizhu',
            2 => '备注',
        ],
        'b501' => [
            0 => 'rongHuiFangJiLu',
            1 => 'kehubianhao',
            2 => '客户编号',
        ],
        'b502' => [
            0 => 'rongHuiFangJiLu',
            1 => 'kehu',
            2 => '客户',
        ],
        'b503' => [
            0 => 'rongHuiFangJiLu',
            1 => 'huifangshijian',
            2 => '回访时间',
        ],
        'b504' => [
            0 => 'rongHuiFangJiLu',
            1 => 'huifangfangshi',
            2 => '回访方式',
        ],
        'b505' => [
            0 => 'rongHuiFangJiLu',
            1 => 'huifangrenyuan',
            2 => '回访人员',
        ],
        'b506' => [
            0 => 'rongHuiFangJiLu',
            1 => 'huifangqingkuang',
            2 => '回访情况',
        ],
        'b601' => [
            0 => 'touZiXiangMuBiao',
            1 => 'touzihetongbianhao',
            2 => '投资合同编号',
        ],
        'b602' => [
            0 => 'touZiXiangMuBiao',
            1 => 'touziren',
            2 => '投资人',
        ],
        'b603' => [
            0 => 'touZiXiangMuBiao',
            1 => 'yinhangzhanghao',
            2 => '银行账号',
        ],
        'b604' => [
            0 => 'touZiXiangMuBiao',
            1 => 'kaihuxingxinxi',
            2 => '开户行信息',
        ],
        'b605' => [
            0 => 'touZiXiangMuBiao',
            1 => 'touziewanyuan',
            2 => '投资额[万元]',
        ],
        'b606' => [
            0 => 'touZiXiangMuBiao',
            1 => 'qishiriqi',
            2 => '起始日期',
        ],
        'b607' => [
            0 => 'touZiXiangMuBiao',
            1 => 'yue',
            2 => '月',
        ],
        'b608' => [
            0 => 'touZiXiangMuBiao',
            1 => 'tian',
            2 => '天',
        ],
        'b609' => [
            0 => 'touZiXiangMuBiao',
            1 => 'daoqiriqi',
            2 => '到期日期',
        ],
        'b610' => [
            0 => 'touZiXiangMuBiao',
            1 => 'fuxiri',
            2 => '付息日',
        ],
        'b611' => [
            0 => 'touZiXiangMuBiao',
            1 => 'fuxifangshi',
            2 => '付息方式',
        ],
        'b612' => [
            0 => 'touZiXiangMuBiao',
            1 => 'yuexi',
            2 => '月息[%]',
        ],
        'b613' => [
            0 => 'touZiXiangMuBiao',
            1 => 'yingfuyuan',
            2 => '应付[元]',
        ],
        'b614' => [
            0 => 'touZiXiangMuBiao',
            1 => 'zongeyuan',
            2 => '总额[元]',
        ],
        'b615' => [
            0 => 'touZiXiangMuBiao',
            1 => 'kehujingli',
            2 => '客户经理',
        ],
        'b616' => [
            0 => 'touZiXiangMuBiao',
            1 => 'ticheng',
            2 => '提成[%]',
        ],
        'b617' => [
            0 => 'touZiXiangMuBiao',
            1 => 'tichengyuan',
            2 => '提成[元]',
        ],
        'b618' => [
            0 => 'touZiXiangMuBiao',
            1 => 'tichengzongeyuan',
            2 => '提成总额[元]',
        ],
        'b619' => [
            0 => 'touZiXiangMuBiao',
            1 => 'fangkuanrenyinhang',
            2 => '放款人[银行]',
        ],
        'b620' => [
            0 => 'touZiXiangMuBiao',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'b621' => [
            0 => 'touZiXiangMuBiao',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'b622' => [
            0 => 'touZiXiangMuBiao',
            1 => 'jieqingqingkuang',
            2 => '结清情况',
        ],
        'b623' => [
            0 => 'touZiXiangMuBiao',
            1 => 'beizhu',
            2 => '备注',
        ],
        'b701' => [
            0 => 'touZiDangAn',
            1 => 'touzihetongbianhao',
            2 => '投资合同编号',
        ],
        'b702' => [
            0 => 'touZiDangAn',
            1 => 'touziren',
            2 => '投资人',
        ],
        'b703' => [
            0 => 'touZiDangAn',
            1 => 'touziewanyuan',
            2 => '投资额[万元]',
        ],
        'b704' => [
            0 => 'touZiDangAn',
            1 => 'qishiriqi',
            2 => '起始日期',
        ],
        'b705' => [
            0 => 'touZiDangAn',
            1 => 'yue',
            2 => '月',
        ],
        'b706' => [
            0 => 'touZiDangAn',
            1 => 'tian',
            2 => '天',
        ],
        'b707' => [
            0 => 'touZiDangAn',
            1 => 'daoqiriqi',
            2 => '到期日期',
        ],
        'b708' => [
            0 => 'touZiDangAn',
            1 => 'fangkuanrenyinhang',
            2 => '放款人[银行]',
        ],
        'b709' => [
            0 => 'touZiDangAn',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'b710' => [
            0 => 'touZiDangAn',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'b711' => [
            0 => 'touZiDangAn',
            1 => 'yuexi',
            2 => '月息[%]',
        ],
        'b712' => [
            0 => 'touZiDangAn',
            1 => 'touzihetong',
            2 => '投资合同',
        ],
        'b713' => [
            0 => 'touZiDangAn',
            1 => 'haikuanmingxibiao',
            2 => '还款明细表',
        ],
        'b714' => [
            0 => 'touZiDangAn',
            1 => 'zhuanzhangpingtiao',
            2 => '转账凭条',
        ],
        'b715' => [
            0 => 'touZiDangAn',
            1 => 'haikuanzhuanzhangpingzheng',
            2 => '还款转账凭证',
        ],
        'b716' => [
            0 => 'touZiDangAn',
            1 => 'taxiangquanzheng',
            2 => '他项权证',
        ],
        'b717' => [
            0 => 'touZiDangAn',
            1 => 'beizhu',
            2 => '备注',
        ],
        'm201' => [
            0 => 'touZiKeHuMingCe',
            1 => 'xingming',
            2 => '姓名',
        ],
        'm202' => [
            0 => 'touZiKeHuMingCe',
            1 => 'zhengjianhaoma',
            2 => '证件号码',
        ],
        'm203' => [
            0 => 'touZiKeHuMingCe',
            1 => 'lianxidianhua',
            2 => '联系电话',
        ],
        'm204' => [
            0 => 'touZiKeHuMingCe',
            1 => 'yinhangzhanghao',
            2 => '银行账号',
        ],
        'm205' => [
            0 => 'touZiKeHuMingCe',
            1 => 'kaihuxingxinxi',
            2 => '开户行信息',
        ],
        'm206' => [
            0 => 'touZiKeHuMingCe',
            1 => 'jiatingzhuzhi',
            2 => '家庭住址',
        ],
        'b801' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'b802' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'b803' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'fangkuanshijian',
            2 => '放款时间',
        ],
        'b804' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jiekuanewanyuan',
            2 => '借款额[万元]',
        ],
        'b805' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'qixianyue',
            2 => '期限[月]',
        ],
        'b806' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'anyuebiliyue',
            2 => '按月比例[%/月]',
        ],
        'b807' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zhanqi',
            2 => '展期',
        ],
        'b808' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'diya',
            2 => '抵押',
        ],
        'b809' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'huanfanglei',
            2 => '换房类',
        ],
        'b810' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'tijiangbiliyue',
            2 => '提奖比例[%/月]',
        ],
        'b811' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'tijiangjineyuan',
            2 => '提奖金额[元]',
        ],
        'b812' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'yuliubili',
            2 => '预留比例[%]',
        ],
        'b813' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'kehujingli',
            2 => '客户经理',
        ],
        'b814' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'gerentijiangyuan',
            2 => '个人提奖[元]',
        ],
        'b815' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'gerenyuliuyuan',
            2 => '个人预留[元]',
        ],
        'b816' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'gerenfafangyuan',
            2 => '个人发放[元]',
        ],
        'b817' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jinglixingming',
            2 => '经理姓名',
        ],
        'b818' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jinglitijiangbili',
            2 => '经理提奖比例',
        ],
        'b819' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jinglitijiang',
            2 => '经理提奖',
        ],
        'b820' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jingliyuliu',
            2 => '经理预留',
        ],
        'b821' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'jinglifafang',
            2 => '经理发放',
        ],
        'b822' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zongjianxingming',
            2 => '总监姓名',
        ],
        'b823' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zongjiantijiangbili',
            2 => '总监提奖比例',
        ],
        'b824' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zongjiantijiang',
            2 => '总监提奖',
        ],
        'b825' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zongjianyuliu',
            2 => '总监预留',
        ],
        'b826' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'zongjianfafang',
            2 => '总监发放',
        ],
        'b827' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'fengkongjingli',
            2 => '风控经理',
        ],
        'b828' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'fengkongtijiang',
            2 => '风控提奖',
        ],
        'b829' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'fangkuanren',
            2 => '放款人',
        ],
        'b830' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'fangkuanrentijiang',
            2 => '放款人提奖',
        ],
        'b831' => [
            0 => 'fangKuanTiJiangBiao',
            1 => 'beizhu',
            2 => '备注',
        ],
        'z301' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'rongzihetongbianhao',
            2 => '融资合同编号',
        ],
        'z302' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'z303' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'kehujingli',
            2 => '客户经理',
        ],
        'z304' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'gerenyuliu',
            2 => '个人预留',
        ],
        'z305' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'meiqifafang',
            2 => '每期发放',
        ],
        'z306' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'jinglixingming',
            2 => '经理姓名',
        ],
        'z307' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'jingliyuliu',
            2 => '经理预留',
        ],
        'z308' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'meiqifafang258',
            2 => '每期发放258',
        ],
        'z309' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'zongjianxingming',
            2 => '总监姓名',
        ],
        'z310' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'zongjianyuliu',
            2 => '总监预留',
        ],
        'z311' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'meiqifafang261',
            2 => '每期发放261',
        ],
        'z312' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'fangkuanshijian',
            2 => '放款时间',
        ],
        'z313' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'qixianyue',
            2 => '期限[月]',
        ],
        'z314' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'yingfaqishu',
            2 => '应发期数',
        ],
        'z315' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'yifaqishu',
            2 => '已发期数',
        ],
        'z316' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'xiaciyingfayuefen',
            2 => '下次应发月份',
        ],
        'z317' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'fafangzhuangtai',
            2 => '发放状态',
        ],
        'z318' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'beizhu',
            2 => '备注',
        ],
        'z319' => [
            0 => 'yuLiuFanHuanZhang',
            1 => 'cunqianguanru',
            2 => '存钱罐[入]',
        ],
        'b901' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'yewubianhao',
            2 => '业务编号',
        ],
        'b902' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'kehuxingming',
            2 => '客户姓名',
        ],
        'b903' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'rongzijinewanyuan',
            2 => '融资金额[万元]',
        ],
        'b904' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'hezuoyinhang',
            2 => '合作银行',
        ],
        'b905' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'yewuleixing',
            2 => '业务类型',
        ],
        'b906' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'shoufeijinewanyuan',
            2 => '收费金额[万元]',
        ],
        'b907' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'shoufeiriqi',
            2 => '收费日期',
        ],
        'b908' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'jiedanren',
            2 => '接单人',
        ],
        'b909' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'jibie',
            2 => '级别',
        ],
        'b910' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'dangyueheji',
            2 => '当月合计',
        ],
        'b911' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangbili',
            2 => '提奖比例',
        ],
        'b912' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangjine',
            2 => '提奖金额',
        ],
        'b913' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tandanren',
            2 => '谈单人',
        ],
        'b914' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangbili284',
            2 => '提奖比例284',
        ],
        'b915' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangjine285',
            2 => '提奖金额285',
        ],
        'b916' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'gendanren',
            2 => '跟单人',
        ],
        'b917' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangbili287',
            2 => '提奖比例287',
        ],
        'b918' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangjine288',
            2 => '提奖金额288',
        ],
        'b919' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'zuodanren',
            2 => '做单人',
        ],
        'b920' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangbili290',
            2 => '提奖比例290',
        ],
        'b921' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangjine291',
            2 => '提奖金额291',
        ],
        'b922' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'bumenjingli',
            2 => '部门经理',
        ],
        'b923' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangbili293',
            2 => '提奖比例293',
        ],
        'b924' => [
            0 => 'dianXiaoTiJiangBiao',
            1 => 'tijiangjine294',
            2 => '提奖金额294',
        ],
        'm301' => [
            0 => 'keHuJingLiMingCe',
            1 => 'xingming',
            2 => '姓名',
        ],
        'm302' => [
            0 => 'keHuJingLiMingCe',
            1 => 'jibie',
            2 => '级别',
        ],
        'm303' => [
            0 => 'keHuJingLiMingCe',
            1 => 'shangjijingli',
            2 => '上级经理',
        ],
        'm304' => [
            0 => 'keHuJingLiMingCe',
            1 => 'shangjizongjian',
            2 => '上级总监',
        ],
        'm305' => [
            0 => 'keHuJingLiMingCe',
            1 => 'zaizhiqingkuang',
            2 => '在职情况',
        ],
        'm401' => [
            0 => 'fengKongJingLiMingCe',
            1 => 'xingming',
            2 => '姓名',
        ],
        'm402' => [
            0 => 'fengKongJingLiMingCe',
            1 => 'jibie',
            2 => '级别',
        ],
        'm403' => [
            0 => 'fengKongJingLiMingCe',
            1 => 'ruzhishijian',
            2 => '入职时间',
        ],
        'm404' => [
            0 => 'fengKongJingLiMingCe',
            1 => 'zaizhiqingkuang',
            2 => '在职情况',
        ],
        'm501' => [
            0 => 'fangKuanRenMingCe',
            1 => 'fangkuanren',
            2 => '放款人',
        ],
        'm502' => [
            0 => 'fangKuanRenMingCe',
            1 => 'fangkuanrenyinhang',
            2 => '放款人[银行]',
        ],
        'm503' => [
            0 => 'fangKuanRenMingCe',
            1 => 'yinhangzhanghao',
            2 => '银行账号',
        ],
        'm504' => [
            0 => 'fangKuanRenMingCe',
            1 => 'kaihuxingxinxi',
            2 => '开户行信息',
        ],
        'm505' => [
            0 => 'fangKuanRenMingCe',
            1 => 'xianshangnicheng',
            2 => '线上昵称',
        ],
        'x101' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'shangbiaoriqi',
            2 => '上标日期',
        ],
        'x102' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'biaodimingcheng',
            2 => '标的名称',
        ],
        'x103' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'x104' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'nicheng',
            2 => '昵称',
        ],
        'x105' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'fangkuanriqi',
            2 => '放款日期',
        ],
        'x106' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'biaodijineyuan',
            2 => '标的金额[元]',
        ],
        'x107' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'nianlilv',
            2 => '年利率[%]',
        ],
        'x108' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'yue',
            2 => '月',
        ],
        'x109' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'tian',
            2 => '天',
        ],
        'x110' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'hao',
            2 => '号',
        ],
        'x111' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'fuwufei',
            2 => '服务费[%]',
        ],
        'x112' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'fuwufeiyuan',
            2 => '服务费[元]',
        ],
        'x113' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'shouquriqi',
            2 => '收取日期',
        ],
        'x114' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'shijidaozhangyuan',
            2 => '实际到账[元]',
        ],
        'x115' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'toubiaojineyuan',
            2 => '投标金额[元]',
        ],
        'x116' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'kehutoubiaojineyuan',
            2 => '客户投标金额[元]',
        ],
        'x118' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'jieqingqingkuang',
            2 => '结清情况',
        ],
        'x119' => [
            0 => 'xianShangXiangMuBiao',
            1 => 'beizhu',
            2 => '备注',
        ],
        'x201' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'biaodimingcheng',
            2 => '标的名称',
        ],
        'x202' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'jiekuanren',
            2 => '借款人',
        ],
        'x203' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'qishu',
            2 => '期数',
        ],
        'x204' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'zhifushijian',
            2 => '支付时间',
        ],
        'x205' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'lixiyuan',
            2 => '利息[元]',
        ],
        'x206' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'feiyongyuanguanlifei',
            2 => '费用[元]_管理费',
        ],
        'x207' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'feiyongyuanzhongjiefei',
            2 => '费用[元]_中介费',
        ],
        'x208' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'feiyongyuanfuwufei',
            2 => '费用[元]_服务费',
        ],
        'x209' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'feiyongyuanqita',
            2 => '费用[元]_其他',
        ],
        'x210' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'hejiyuan',
            2 => '合计[元]',
        ],
        'x211' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'haikuanqingkuang',
            2 => '还款情况',
        ],
        'x212' => [
            0 => 'xianShangBenXiFeiZhang',
            1 => 'beizhu',
            2 => '备注',
        ],
        'x301' => [
            0 => 'xianShangRiJiZhang',
            1 => 'zhonglei',
            2 => '种类',
        ],
        'x302' => [
            0 => 'xianShangRiJiZhang',
            1 => 'zhanghao',
            2 => '账号',
        ],
        'x303' => [
            0 => 'xianShangRiJiZhang',
            1 => 'riqi',
            2 => '日期',
        ],
        'x304' => [
            0 => 'xianShangRiJiZhang',
            1 => 'qichuyueyuan',
            2 => '期初余额[元]',
        ],
        'x305' => [
            0 => 'xianShangRiJiZhang',
            1 => 'cunqianguanru',
            2 => '存钱罐[入]',
        ],
        'x306' => [
            0 => 'xianShangRiJiZhang',
            1 => 'xianxiachongzhiru',
            2 => '线下充值[入]',
        ],
        'x307' => [
            0 => 'xianShangRiJiZhang',
            1 => 'qiyehuchongzhiru',
            2 => '企业户充值[入]',
        ],
        'x308' => [
            0 => 'xianShangRiJiZhang',
            1 => 'haoyoufanxianru',
            2 => '好友返现[入]',
        ],
        'x309' => [
            0 => 'xianShangRiJiZhang',
            1 => 'shoudaofangkuaneru',
            2 => '收到放款额[入]',
        ],
        'x310' => [
            0 => 'xianShangRiJiZhang',
            1 => 'daoqibenjinru',
            2 => '到期本金[入]',
        ],
        'x311' => [
            0 => 'xianShangRiJiZhang',
            1 => 'daoqilixiru',
            2 => '到期利息[入]',
        ],
        'x312' => [
            0 => 'xianShangRiJiZhang',
            1 => 'diaopeizijinru',
            2 => '调配资金[入]',
        ],
        'x313' => [
            0 => 'xianShangRiJiZhang',
            1 => 'jiedongzijinru',
            2 => '解冻资金[入]',
        ],
        'x314' => [
            0 => 'xianShangRiJiZhang',
            1 => 'tixianchu',
            2 => '提现[出]',
        ],
        'x315' => [
            0 => 'xianShangRiJiZhang',
            1 => 'shouxufeichu',
            2 => '手续费[出]',
        ],
        'x316' => [
            0 => 'xianShangRiJiZhang',
            1 => 'zhuanzhangzijinchu',
            2 => '转账资金[出]',
        ],
        'x317' => [
            0 => 'xianShangRiJiZhang',
            1 => 'dongjiezijinchu',
            2 => '冻结资金[出]',
        ],
        'x318' => [
            0 => 'xianShangRiJiZhang',
            1 => 'zhifubenxichu',
            2 => '支付本息[出]',
        ],
        'x319' => [
            0 => 'xianShangRiJiZhang',
            1 => 'zhifutoubiaochu',
            2 => '支付投标[出]',
        ],
        'x320' => [
            0 => 'xianShangRiJiZhang',
            1 => 'qimoyueyuan',
            2 => '期末余额[元]',
        ],
        'x321' => [
            0 => 'xianShangRiJiZhang',
            1 => 'beizhu',
            2 => '备注',
        ],
    ];
}
