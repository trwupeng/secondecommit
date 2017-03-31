<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class MBDepartment  extends BaseFK {

    protected static $_pk = 'id'; //主键

    protected static $_tbname = 'mb_department'; //表名

    protected static $_host = 'manage'; //配置名

    /**
     * 从OA批量导入部门数据
     */
    public static function updateDataFromApi(){
        $data = \Prj\Misc\MBM::getInstance()->getOADepartmentsHttp();
        if($data){
            foreach ($data as $v){
                $tmp = self::getCopy($v['id']);
                $tmp->load();
                $tmp->setField('name',$v['name']);
                $tmp->setField('oa_sort',$v['sortId']);
                $tmp->setField('updateTime',date('YmdHis'));
                $tmp->setField('supId',$v['superior']);
                try{
                    $tmp->update();
                    error_log('Department id:'.$v['id'].'#update success...');
                }catch (\ErrorException $e){
                    error_log('Department id:'.$v['id'].'#'.$e->getMessage());
                }
            }
        }
    }

    /**
     * 根据记录集组成部门树
     * @param null $rs
     * @return array
     */
    public static function getDepartTree($rs = null){
        $rs = $rs ? $rs : self::getRecords();
        $topData = self::getNext(\Prj\Misc\MBM::OA_orgId_kkd , $rs);
        return self::createTree($topData , $rs);
    }

    /**
     * 根据部门号获取所有的上级部门集合
     */
    public static function getAllDepartsByDepartIds($rs){
        $all = self::getRecords();
        $result = [];
        foreach ($rs as $v){
            self::gatherDepartsFromPre($v , $result , $all);
        }
        return $result;
    }

    protected static function gatherDepartsFromPre($record , &$result , $all){
        //var_log($result ,'xxxxxxxxxxxxxx');
        if(array_key_exists($record['id'] , $result))return true;
        $result[$record['id']] = $record;
        $supId = $record['supId'];
        if(!$supId)return true;
        $supArr = array_filter($all , function($v)use($supId){
            return $v['id'] == $supId ? true : false;
        });
        $tmp = current($supArr);
        if(!$tmp){
            return true;
        }else{
            self::gatherDepartsFromPre($tmp , $result , $all);
        }
    }

    /**
     * 组织树结构格式化
     * zTree格式
     * @param $data
     * @param $users
     * @return array
     */
    public static function treeFormat($data , $users){
        $arr = [];
        foreach ($data as $v){
            $tmp = [
                'id' => $v['id'],
                'pid' => $v['supId'],
                'name' => $v['name'],
                'faicon' => 'folder-open-o',
                'faiconClose' => 'folder-o'
            ];
            if($v['children'])$tmp['children'] = self::treeFormat($v['children'] , $users);
            $thisUsers = array_filter($users , function ($val)use($v){
                return $val['dept'] == $v['id'] ? true : false;
            });
            if($thisUsers){
                foreach ($thisUsers as $v){
                    $tmpp = [
                        'id' => $v['loginName'],
                        'type' => 'user',
                        'pid' => $v['dept'],
                        'name' => $v['nickname'].'--'.$v['postName'],
                        'nickname' => $v['nickname'],
                        'faicon' => 'user',
                    ];
                    $tmp['children'][] = $tmpp;
                }
            }
            $arr[] = $tmp;
        }
        return $arr;
    }

    /**
     * 寻找下级集合
     * @param $id
     * @param $arr 数据全集
     * @return array
     */
    protected static function getNext($id , $arr){
        $tmp = array_filter($arr , function ($v)use($id){
            return $v['supId'] == $id ? true : false;
        });
        return $tmp;
    }

    protected static $num = 0; //死循环边界
    /**
     * 递归生成树结构
     * @param $topData
     * @param $rs
     * @return array
     */
    protected static function createTree($topData , $rs){
        self::$num++;
        if(self::$num > 10)throw new \ErrorException('死循环');
        $data = [];
        foreach ($topData as $v){
            $tmp = self::getNext($v['id'] , $rs);
            $data[$v['id']] = $v;
            if($tmp){
                $data[$v['id']]['children'] = self::createTree($tmp , $rs);
            }
        }
        self::$num = 0;
        return $data;
    }
}
