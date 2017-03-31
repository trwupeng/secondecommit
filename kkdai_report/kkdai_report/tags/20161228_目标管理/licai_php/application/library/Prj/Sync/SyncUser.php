<?php

namespace Prj\Sync;

/**
 * Class SyncUser
 * @package Prj\Sync
 * @author lingtm <lingtima@gmail.com>
 */
class SyncUser
{
    /**
     * @var string 所属单位标识ID
     */
    public $orgAccountId;

    /**
     * @var string 	Id，唯一标识人员
     */
    public $id;

    /**
     * @var string 姓名
     */
    public $name;

    /**
     * @var string 人员编码
     */
    public $code;

    /**
     * @var string 登录名
     */
    public $loginName;

    /**
     * @var string 单位名称
     */
    public $orgAccountName;

    /**
     * @var string 单位短名称
     */
    public $orgShortName;

    /**
     * @var string 人员职务级别Id
     */
    public $orgLevelId;

    /**
     * @var string 人员岗位Id
     */
    public $orgPostId;

    /**
     * @var string 人员所属部门Id
     */
    public $orgDepartmentId;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->name;
        } else {
            throw new \Exception("can't find element '$name' from class:" . __CLASS__);
            return null;
        }
    }

    /**
     * 批量复制，类似laravel fillable
     * @param $array
     * @return $this
     */
    public function create($array) {
        foreach ($array as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }
}
