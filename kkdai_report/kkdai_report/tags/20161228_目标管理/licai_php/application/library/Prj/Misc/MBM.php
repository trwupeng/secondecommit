<?php
namespace Prj\Misc;
/**
 * 目标管理系统中的一些常用的方法
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/12/16
 * Time: 11:34
 */
class MBM {

    protected static $that;

    const OA_orgId_kkd = '670869647114347'; //快快贷公司ID

    public static function getInstance(){
        if(!self::$that)self::$that = new self;
        return self::$that;
    }

    public function getOAUsersHttp(){
        $sync = new \Prj\Sync\SyncFactory('OA');
        $users = $sync->getAllUsers();
        foreach ($users as $v){
            $ret[(string)$v->id] = (array)$v;
        }
        return (array)$ret;
    }

    public function getOADepartmentsHttp(){
        $sync = new \Prj\Sync\SyncFactory('OA');
        $departments = $sync->getAllDepartments(self::OA_orgId_kkd);
        foreach ($departments as $v){
            $ret[$v['id']] = $v;
        }
        return (array)$ret;
    }

    public function getOAOrgPostsHttp(){
        $sync = new \Prj\Sync\SyncFactory('OA');
        $post = $sync->getAllOrgPosts(self::OA_orgId_kkd);
        foreach ($post as $v){
            $ret[$v['id']] = $v;
        }
        return (array)$ret;
    }

    public function getECUsersHttp(){
        $ec = new \Prj\Data\EcData;
        $ec->getAccessTokenByComm();
        $users = $ec->getAllUsersByComm();
        foreach ($users as $k => $v){
            $ret[$v['userId']] = $v;
        }
        return (array)$ret;
    }

    /**
     * 缓存模式下,获取OA系统的人员名单集合
     * @return array|mixed
     */
    public function getOAUsers($refresh = false){
        $cache = \Prj\Misc\CacheFK::getCopy('OAUsers');
        $expireSec = $refresh ? 0 : 3 * 24 * 3600;
        $ret = $cache->cacheData( $expireSec , [$this , 'getOAUsersHttp']);
        return $ret;
    }
    /**
     * 缓存模式下,获取OA系统的部门集合
     * @return array|mixed
     */
    public function getOADepartments($refresh = false){
        $cache = \Prj\Misc\CacheFK::getCopy('OADepartments');
        $expireSec = $refresh ? 0 : 3 * 24 * 3600;
        $ret = $cache->cacheData($expireSec , [$this , 'getOADepartmentsHttp']);
        return $ret;
    }
    /**
     * 缓存模式下,获取OA系统的职位集合
     * @return array|mixed
     */
    public function getOAOrgPosts($refresh = false){
        $cache = \Prj\Misc\CacheFK::getCopy('OAPosts');
        $expireSec = $refresh ? 0 : 3 * 24 * 3600;
        $ret = $cache->cacheData($expireSec , [$this , 'getOAOrgPostsHttp']);
        return $ret;
    }

    /**
     * 获取归属部门
     * @param \Prj\Data\Manager $user
     * @return array
     */
    public function getAllDepartsByUser(\Prj\Data\Manager $user){
        if(!$user->exists())return [];
        $dept = $user->getField('dept');
        $result = [];
        if($dept){
            $rs = \Prj\Data\MBDepartment::getRecords(['id'=>$dept]);
            if($rs){
                $result = \Prj\Data\MBDepartment::getAllDepartsByDepartIds($rs);
            }
        }
        return array_values($result);
    }

    public function getECUsers($refresh = false){
        $cache = \Prj\Misc\CacheFK::getCopy('ECUsers');
        $expireSec = $refresh ? 0 : 3 * 24 * 3600;
        $ret = $cache->cacheData($expireSec , [$this , 'getECUsersHttp']);
        return $ret;
    }

    /**
     * 从OA更新用户信息都本地用户表
     * @return bool
     */
    public function updateUserFromOA(){
        $users = $this->getOAUsers();
        //var_log($users,'users>>>');
        $posts = $this->getOAOrgPosts();
        array_walk($users , function($v , $k)use($posts){
            $user = \Prj\Data\Manager::getCopy($v['loginName']);
            $user->load();
            if(!$user->exists()){
                $user->setField('passwd' , '123456' );
                $user->setField('cameFrom' , 'local' );
                $user->setField('regYmd' , date('Ymd') );
            }
            $user->setField('oa' , $v['id'] );
            $user->setField('nickname' , $v['name'] );
            $user->setField('dept' , $v['orgDepartmentId'] );
            $user->setField('postName' , $posts[$v['orgPostId']]['name'] );
            try{
                $user->update();
                \Prj\Misc\ViewFK::log($v['loginName'].'#update success...');
            }catch (\ErrorException $e){
                \Prj\Misc\ViewFK::log($v['loginName'].'#update failed#'.$e->getMessage());
            }
        });
        return true;
    }

    /**
     * 从EC更新用户的ECID
     * @return bool
     */
    public function updateUserFromEC(){
        $users = \Prj\Misc\MBM::getInstance()->getECUsers();
        foreach ($users as $v){
            $record = \Prj\Data\Manager::loopFindRecords(['nickname' => $v['userName']])[0];
            if($record){
                $tmp = \Prj\Data\Manager::getCopy($record['loginName']);
                $tmp->load();
                if($tmp->getField('ec'))continue;
                $tmp->setField('ec', $v['userId']);
                $tmp->update();
            }
        }
        return true;
    }
}