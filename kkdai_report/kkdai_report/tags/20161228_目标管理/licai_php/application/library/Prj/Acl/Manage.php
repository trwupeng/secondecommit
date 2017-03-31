<?php
namespace Prj\Acl;
class Manage extends \Sooh\Base\Acl\Ctrl
{
    public function initMenu() {
        error_log('### new menu from db...');
        $menu = new \Prj\Data\Menu();
        $menus = $menu->getMenu();
        $arr = [];
        /*对节点进行排序*/
        foreach ($menus as $key => $m) {
            $arr[$m['mark']][$m['name']] = json_decode($m['value'], TRUE);
        }
        $menus = [];
        foreach ($arr as $key => $values) {
            foreach ($values as $k => $v) {
                $menus[$k] = $v;
            }
        }
        return $menus;

        //示例sql：insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.添加标的模板', '["manage","warestpl","edit",[],[]]');
        //数据库sql：/sqls/169-wuchen.sql

    }

    public function dump()
    {
        var_log($this->rights, 'rights========');
    }
}