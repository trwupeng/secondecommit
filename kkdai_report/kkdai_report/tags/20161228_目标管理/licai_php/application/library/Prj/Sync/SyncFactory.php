<?php


namespace Prj\Sync;

/**
 * Class SyncFactory
 * @package Prj\Sync
 * @author lingtm <lingtima@gmail.com>
 */
class SyncFactory implements ISync
{
    /**
     * @var \Prj\Sync\ISync
     */
    public $module;

    public function __construct($module = 'OA')
    {
        $className = 'Prj\\Sync\\Type\\' . $module;
        $this->module = new $className();
        return $this->module;
    }

    /**
     * 获取所有用户的列表
     * @return \Prj\Sync\SyncList
     */
    public function getAllUsers()
    {
        return $this->module->getAllUsers();
    }

    /**
     * 获取用户详细信息
     * @param string  $userId  用户ID
     * @param boolean $withOrg 是否附带所属单位ID
     * @return \Prj\Sync\SyncUser
     */
    public function getUserInfo($userId, $withOrg = false)
    {
        return $this->module->getUserInfo($userId, $withOrg);
    }


    /**
     * 获取指定单位的所有部门(不包含停用)
     * @param $accountId 单位ID
     * @return array
     */
    public function getAllDepartments($accountId)
    {
        return $this->module->getAllDepartments($accountId);
    }

    /**
     * 按部门ID取部门信息
     * @param string $id 部门ID
     * @return array
     */
    public function getDepartmentInfo($id)
    {
        return $this->module->getDepartmentInfo($id);
    }

    /**
     * 获取指定单位的所有岗位(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgPosts($accountId)
    {
        return $this->module->getAllOrgPosts($accountId);
    }

    /**
     * 按岗位Id取岗位信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgPostInfo($id)
    {
        return $this->module->getOrgPostInfo($id);
    }

    /**
     * 获取指定单位的所有职务级别(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgLevels($accountId)
    {
        return $this->module->getAllOrgLevels($accountId);
    }

    /**
     * 按职务级别Id取职务级别信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgLevelInfo($id)
    {
        return $this->module->getOrgLevelInfo($id);
    }


}