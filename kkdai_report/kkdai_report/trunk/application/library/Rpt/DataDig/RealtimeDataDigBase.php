<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/11/30 0030
 * Time: 下午 12:46
 */
namespace Rpt\DataDig;

class RealtimeDataDigBase {
    protected  $db_produce;
    public function __construct()
    {
        $this->db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);

        // 生产环境的数据库从库
//        $this->db_produce = \Sooh\DB\Broker::getInstance('produce_real');
    }

    /**
     * 获取页面表格头部
     * @param $fieldsMap
     * @return array
     */
    public function getTableHeader($fieldsMap) {
        $header = [];
        foreach ($fieldsMap as $k => $v) {
            if(isset($v[1]) && !empty($v[1])) {
                $header[$v[0]] = $v[1];
            }else {
                $header[$v[0]] = '';
            }
        }
        return $header;
    }
}