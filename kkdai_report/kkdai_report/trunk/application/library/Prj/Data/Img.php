<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/29
 * Time: 10:53
 */

namespace Prj\Data;

class Img extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields,$loginName){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $tmp->setField('updateUser',$loginName);
        $tmp->setField('updateTime',$time->ymdhis());
        return $tmp;
    }

    public static function updStatus($id,$status){
        $img = self::getCopy($id);
        $img->load();
        if(!$img->exists())throw new \ErrorException('不存在的图片');
        $img->setField('status',$status);
        $img->update();
        if($status=='-4'){ //删除
            $fileId = $img->getField('fileId');
            try{
                \Prj\Data\File::updateStatus($fileId,0);
            }catch (\ErrorException $e){
                var_log('File 更新失败！>>>');
            }
        }
    }

    public static function getCopy($id) {
        return parent::getCopy(['imgId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_img';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'dbForRpt'.($isCache?'Cache':'');
    }

}
