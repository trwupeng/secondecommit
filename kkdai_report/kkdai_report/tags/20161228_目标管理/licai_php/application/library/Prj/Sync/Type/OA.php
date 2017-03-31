<?php

namespace Prj\Sync\Type;

use Prj\Sync\ISync;
use Prj\Sync\SyncList;
use Prj\Sync\SyncUser;
use Prj\Tool\Func;

/**
 * 对快快贷OA接口的封装
 * 接口文档地址：http://open.seeyon.com/seeyon/webhelp/APIdoc/content/index.html#平台开发手册
 * @package Prj\Sync\Type
 * @author  lingtm <lingtima@gmail.com>
 */
class OA implements ISync
{
    //    protected $baseUrl = 'http://oa.kuaikuaidai.com/seeyon';
    //    protected $userName = 'restuser';
    //    protected $password = 'rest123456';
    protected $baseUrl;
    protected $userName;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = \Sooh\Base\Ini::getInstance()->get('sync')['baseUrl'];
        $this->userName = \Sooh\Base\Ini::getInstance()->get('sync')['userName'];
        $this->password = \Sooh\Base\Ini::getInstance()->get('sync')['password'];
    }

    /**
     * 获取所有用户的列表
     * @return \Prj\Sync\SyncList
     */
    public function getAllUsers()
    {
        $token = $this->getToken();
        $accountArr = $this->getOrgAccounts($token);
        //        file_put_contents('/var/www/logs/data.txt', json_encode($accountArr) . "\n\r\n\r\n\r\n\r\n\r\t\t\t\t\t\t");

        $SyncList = new SyncList();

        foreach ($accountArr as $account) {
            if ($account['superior'] != -1) {
                continue;
            }
            //            file_put_contents('/var/www/logs/data.txt', $account['id'] . ":\n\r\n\r\n\r\t\t", FILE_APPEND);

            $userArr = $this->getOrgMembers($account['id'], $token);
            if (!empty($userArr)) {
                //                file_put_contents('/var/www/logs/data.txt', json_encode($userArr) . "\n\r\n\r\n\r", FILE_APPEND);
                $childOrgArr = $account['childrenAccounts'];
                foreach ($childOrgArr as $v) {
                    $childAccountArr[$v['orgAccountId']] = $v;
                }

                foreach ($userArr as $k => $v) {
                    $SyncUser = new SyncUser();
                    $data = [
                        'orgAccountId'    => $v['orgAccountId'],
                        'id'              => $v['id'],
                        'name'            => $v['name'],
                        'code'            => $v['code'],
                        'loginName'       => $v['loginName'],
                        'orgAccountName'  => $childAccountArr[$v['orgAccountId']]['name'],
                        'orgShortName'    => $childAccountArr[$v['orgAccountId']]['shortName'],
                        'orgLevelId'      => $v['orgLevelId'],
                        'orgPostId'       => $v['orgPostId'],
                        'orgDepartmentId' => $v['orgDepartmentId'],
                    ];
                    $SyncList->push($SyncUser->create($data));
                    unset($data);
                    unset($SyncUser);
                }
            }
            break;
        }

        return $SyncList;
    }

    /**
     * 获取用户详细信息
     * @param string  $userId  用户ID
     * @param boolean $withOrg 是否附带所属单位ID
     * @return \Prj\Sync\SyncUser
     */
    public function getUserInfo($userId, $withOrg = false)
    {
        $token = $this->getToken();
        $memberRet = $this->getOrgMember($userId, $token);

        if ($withOrg) {
            $orgRet = $this->getOrgAccount($memberRet['orgAccountId'], $token);
            $orgName = $orgRet['name'];
            $orgShortName = $orgRet['shortName'];
        } else {
            $orgName = $orgShortName = '';
        }

        $SyncUser = new SyncUser();
        $SyncUser->id = $memberRet['id'];
        $SyncUser->code = $memberRet['code'];
        $SyncUser->name = $memberRet['name'];
        $SyncUser->loginName = $memberRet['loginName'];
        $SyncUser->orgAccountId = $memberRet['orgAccountId'];
        $SyncUser->orgAccountName = $orgName;
        $SyncUser->orgShortName = $orgShortName;
        $SyncUser->orgLevelId = $memberRet['orgLevelId'];
        $SyncUser->orgPostId = $memberRet['orgPostId'];
        $SyncUser->orgDepartmentId = $memberRet['orgDepartmentId'];

        return $SyncUser;
    }


    /**
     * 获取token
     * @return mixed
     */
    protected function getToken()
    {
        $url = $this->baseUrl . '/rest/token';
        $params = json_encode(['userName' => $this->userName, 'password' => $this->password]);
        $headers = ['Accept: application/json', 'Content-type: application/json'];

        $tokenRet = Func::request($url, $params, 'POST', false, $headers);
        return json_decode($tokenRet, true, 512, JSON_BIGINT_AS_STRING)['id'];
    }

    /**
     * 获取所有单位信息
     * @param string $token token
     * @return mixed
     */
    protected function getOrgAccounts($token)
    {
        $url = $this->baseUrl . '/rest/orgAccounts';

        $orgRet = Func::request($url, ['token' => $token]);
        return json_decode($orgRet, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 获取指定单位的所有人员
     * @param string $orgAccountId 单位ID
     * @param string $token        token
     * @return mixed
     */
    protected function getOrgMembers($orgAccountId, $token)
    {
        $url = $this->baseUrl . '/rest/orgMembers/' . $orgAccountId;

        $ret = Func::request($url, ['token' => $token]);
        return json_decode($ret, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 按ID取人员信息
     * @param string $id    人员ID
     * @param string $token token
     * @return array
     */
    protected function getOrgMember($id, $token)
    {
        $url = $this->baseUrl . '/rest/orgMember/' . $id;
        $memberRet = Func::request($url, ['token' => $token]);
        return json_decode($memberRet, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 按单位Id取单位的信息
     * @param string $id    单位ID
     * @param string $token token
     * @return array
     */
    protected function getOrgAccount($id, $token)
    {
        $url = $this->baseUrl . '/rest/orgAccount/' . $id;
        $orgRet = Func::request($url, ['token' => $token]);
        return json_decode($orgRet, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 获取指定单位的所有部门(不包含停用)
     * @param string $accountId 单位ID
     * @param string $token     token
     * @return array
     */
    protected function getDepartments($accountId, $token)
    {
        $url = $this->baseUrl . '/rest/orgDepartments/' . $accountId;
        $ret = Func::request($url, ['token' => $token]);
        return json_decode($ret, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 获取指定单位的所有部门(不包含停用)
     * @param string $accountId 单位ID
     * @return array
     */
    public function getAllDepartments($accountId)
    {
        $token = $this->getToken();
        $ret = $this->getDepartments($accountId, $token);

        $data = [];
        var_log($ret , 'ret>>>');
        foreach ($ret as $k => $v) {
            $data[] = [
                'orgAccountId' => $v['orgAccountId'],
                'id'           => $v['id'],
                'name'         => $v['name'],
                'code'         => $v['code'],
                'sortId'       => $v['sortId'],
                'shortName'    => $v['shortName'],
                'enabled'      => $v['enabled'],
                'type'         => $v['type'],
                'status'       => $v['status'],
                'isInternal'   => $v['isInternal'],
                'isGroup'      => $v['isGroup'],
                'superior'     => $v['superior'],
                'chiefLeader'  => $v['chiefLeader'], //负责人
            ];
        }

        return $data;
    }

    /**
     * 按部门ID取部门信息
     * @param string $id    部门ID
     * @param string $token token
     * @return mixed
     */
    protected function getOrgDepartment($id, $token)
    {
        $url = $this->baseUrl . '/rest/orgDepartment/' . $id;
        $ret = Func::request($url, ['token' => $token]);
        var_log($ret);
        return json_decode($ret, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 按部门ID取部门信息
     * @param string $id 部门ID
     * @return array
     */
    public function getDepartmentInfo($id)
    {
        $token = $this->getToken();
        $ret = $this->getOrgDepartment($id, $token);

        $data = [];
        foreach ($ret as $k => $v) {
            $data[] = [
                'orgAccountId' => $v['orgAccountId'],
                'id'           => $v['id'],
                'name'         => $v['name'],
                'code'         => $v['code'],
                'sortId'       => $v['sortId'],
                'shortName'    => $v['shortName'],
                'enabled'      => $v['enabled'],
                'type'         => $v['type'],
                'status'       => $v['status'],
                'isInternal'   => $v['isInternal'],
                'isGroup'      => $v['isGroup'],
                'superiorName' => $v['superiorName'],
            ];
        }

        return $data;
    }

    /**
     * 获取指定单位的所有岗位(不包含停用)
     * @param string $accountId accountId
     * @param string $token     token
     * @return mixed
     */
    protected function getOrgPosts($accountId, $token)
    {
        $url = $this->baseUrl . '/rest/orgPosts/' . $accountId;
        $ret = Func::request($url, ['token' => $token]);
        return json_decode($ret, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 获取指定单位的所有岗位(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgPosts($accountId)
    {
        $token = $this->getToken();
        $data = $this->getOrgPosts($accountId, $token);
        return $data;
    }

    /**
     * 按岗位Id取岗位信息
     * @param string $id    id
     * @param string $token token
     * @return mixed
     */
    protected function getOrgPost($id, $token = '')
    {
        empty($token) && $token = $this->getToken();
        $url = $this->baseUrl . '/rest/orgPost/' . $id;
        $ret = Func::request($url, ['token' => $token]);
        return json_decode($ret, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 按岗位Id取岗位信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgPostInfo($id)
    {
        $data = $this->getOrgPost($id);
        return $data;
    }

    /**
     * 获取指定单位的所有职务级别(不包含停用)
     * @param string $accountId account
     * @param string $token     token
     * @return mixed
     */
    protected function getOrgLevels($accountId, $token = '')
    {
        empty($token) && $token = $this->getToken();
        $url = $this->baseUrl . '/rest/orgLevels/' . $accountId;
        $data = Func::request($url, ['token' => $token]);
        return json_decode($data, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 获取指定单位的所有职务级别(不包含停用)
     * @param string $accountId accountId
     * @return mixed
     */
    public function getAllOrgLevels($accountId)
    {
        $data = $this->getOrgLevels($accountId);
        return $data;
    }

    /**
     * 按职务级别Id取职务级别信息
     * @param string $id id
     * @param string $token
     * @return mixed
     */
    protected function getOrgLevel($id, $token = '')
    {
        empty($token) && $token = $this->getToken();
        $url = $this->baseUrl . '/rest/orgLevel/' . $id;
        $data = Func::request($url, ['token' => $token]);
        return json_decode($data, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * 按职务级别Id取职务级别信息
     * @param string $id id
     * @return mixed
     */
    public function getOrgLevelInfo($id)
    {
        $data = $this->getOrgLevel($id);
        return $data;
    }
}
