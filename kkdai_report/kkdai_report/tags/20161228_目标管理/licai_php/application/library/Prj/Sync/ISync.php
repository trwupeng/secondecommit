<?php

namespace Prj\Sync;

/**
 * 同步其他系统时，需要继承的接口
 * @package Prj\Sync
 * @author lingtm <lingtima@gmail.com>
 */
interface ISync
{
    /**
     * 获取所有用户的列表
     * @return \Prj\Sync\SyncList
     */
    public function getAllUsers();

    /**
     * 获取用户详细信息
     * @param string $userId 用户ID
     * @param boolean $withOrg 是否附带所属单位ID
     * @return \Prj\Sync\SyncUser
     */
    public function getUserInfo($userId, $withOrg = false);

    /**
     * 获取指定单位的所有部门(不包含停用)
     * @param string $accountId 单位ID
     * @return array
     */
    public function getAllDepartments($accountId);

    /**
     * 按部门ID取部门信息
     * @param string $id 部门ID
     * @return array
     */
    public function getDepartmentInfo($id);

    /**
     * 获取指定单位的所有岗位(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgPosts($accountId);

    /**
     * 按岗位Id取岗位信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgPostInfo($id);

    /**
     * 获取指定单位的所有职务级别(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgLevels($accountId);

    /**
     * 按职务级别Id取职务级别信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgLevelInfo($id);
}