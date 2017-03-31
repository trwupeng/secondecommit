<?php
/**
 * 放款提江表
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/20 0014
 * Time: 上午 11:40
 */
namespace Prj\Data;
class FKFangKuanTiJiangBiao extends \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_fangkuantijiangbiao';
    }

    public static $logicFields = [
        'zhanqi'=>[1=>'是', 2=>'否',],
        'diya'=>[1=>'是', 2=>'否'],
        'huanfanglei'=>[1=>'是', 2=>'否'],
    ];

    public static $formatMoneyType = [
        'tijiangjineyuan'       =>1,
        'gerentijiangyuan'      =>1,
        'gerenyuliuyuan'        =>1,
        'gerenfafangyuan'       =>1,
        'jinglitijiang'         =>1,
        'jingliyuliu'           =>1,
        'jinglifafang'          =>1,
        'zongjiantijiang'       =>1,
        'zongjianyuliu'         =>1,
        'zongjianfafang'        =>1,
        'fengkongtijiang'       =>1,
        'fangkuanrentijiang'    =>1,
    ];
    public static $formatDateType = [
    ];
    public static $formatIntType = [
        'tijiangjineyuan',
        'gerentijiangyuan',
        'gerenyuliuyuan',
        'gerenfafangyuan',
        'jinglitijiang',
        'jingliyuliu',
        'jinglifafang',
        'zongjiantijiang',
        'zongjianyuliu',
        'zongjianfafang',
        'fengkongtijiang',
        'fangkuanrentijiang',
    ];

    public static $formatEnumAttr = [
        'zhanqi' => [
            1 => ['style' => 'color:red']
        ],
        'diya' => [
            1 => ['style' => 'color:red']
        ],
        'huanfanglei' => [
            1 => ['style' => 'color:red']
        ],
    ];
    public static $formatPercentageType = [
        'anyuebiliyue','tijiangbiliyue','yuliubili','jinglitijiangbili','zongjiantijiangbili',
    ];

    public static $formatSelectsAttr = [
        'fengkongjingli'
    ];
    /**
     * @param $pager
     * @param array $where
     * @param string $order
     * @return mixed
     */
    public static function paged($pager, $where=[], $order='') {
        $m = self::getCopy();
        $db = $m->db();
        $tb = $m->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb, '*', $where, $order, $pager->page_size, $pager->rsFrom());
    }

    public static function kehujingli () {
        $model = self::getCopy();
        $db = $model->db();
        $tb = $model->tbname();
        return $db->getPair($tb, 'id', 'xingming');
    }

    public static function getFieldForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);
        return $ret;
    }

    public static function getBianHaoForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);

        if (empty($ret)) {
            return [];
        }

        //从父表中取得真实名
        $tmpModel = FKRongZiXiangMuBiao::getCopy('');
        $tmpRet = $tmpModel->db()->getRecords($tmpModel->tbname(), 'id,rongzihetongbianhao', ['id' => $ret]);
        if (empty($tmpRet)) {
            return [];
        } else {
            $arrKeys = [];
            foreach ($tmpRet as $k => $v) {
                if (in_array($v['id'], $ret)) {
                    $arrKeys[$v['id']] = $v['rongzihetongbianhao'];
                }
            }

            if (!empty($arrKeys)) {
                foreach ($ret as $k => $v) {
                    $ret[$k] = $arrKeys[$v];
                }
            }
        }

        return $ret;
    }
}