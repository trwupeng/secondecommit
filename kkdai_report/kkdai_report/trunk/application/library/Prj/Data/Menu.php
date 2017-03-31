<?php

namespace Prj\Data;

/**
 * Description of Menu
 *
 * @author wu.chen
 */
class Menu extends \Sooh\DB\Base\KVObj {

    protected $tbname;
    protected $db;
    protected $orderby;
    protected $selectFields;
    protected $where;
    //index/add/update/delete/import/export
    public static $aliasMap = [
        'index'=>'浏览',
        'add'=>'增加',
        'update'=>'修改',
        'delete'=>'删除',
        'import'=>'导入',
        'export'=>'导出',
    ];

    public function __construct() {
        $this->db = parent::db();
        $this->selectFields = '*';
    }
    
    public function setSelectFields($fields) {
        $this->selectFields = $fields;
        return $this;
    }

    public static function getAllAction(){
        $rs = (new self)->getMenu();
        $actions = [];
        foreach ($rs as $v){
            $tmp = json_decode($v['value'] , true);
            if($tmp[0])$actions[] = strtolower($tmp[0].'_'.$tmp[1].'_'.$tmp[2]);
        }
        return $actions;
    }
    
    public function getMenu() {
       return $this->db->getRecords($this->tbname,  $this->selectFields , ['statusCode'=>0]);
    }

    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_menu';
    }

    public static function paged($where=[],$order='',$pager = null){
        $fin = self::getCopy([]);
        $db = $fin->db();
        $tb = $fin->tbname();
        if($pager){
            $pager->init($db->getRecordCount($tb, $where), -1);
            return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
        }else{
            return $db->getRecords($tb,'*',$where,$order);
        }
    }

    public static function getName($right){
        $arr = explode('_',$right);
        $mark = '"'.$arr[0].'","'.$arr[1].'","'.$arr[2].'"';
        return self::paged(['value*'=>'%'.$mark.'%'])[0]['name'];
    }

}
