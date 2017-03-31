#风控后台权限重构
----------
###描述
后台权限统一设置,精确到`action`
###部署
1. 执行 `trunk/sql/71-tgh.sql` 
2. 覆盖 `trunk\application` 到 `licai_php\application`
3. 覆盖 `hillstill` 到 `vendor\hillstill`

###coder须知
1. 以后添加`action`执行形如下列的`sql`注册

		INSERT INTO `db_kkrpt`.`tb_menu` 
		(`id`, `mark`, `name`, `value`, `iRecordVerID`, `statusCode`, `alias`) 
		VALUES 
		('66', '系统', '系统.用户组.编辑', 
		'["manage","rightsrole","update",{"form":1},[],"rights"]', '1', '-1', 'index');
|参数|说明
|-|-|
|statusCode|-1不在菜单展示/1在菜单展示
|alias|权限标志 目前只有: index 浏览/add 新增/update 修改/delete 删除/import 导入/export 导出

###说明
*	后台-用户组-新增 新增一个用户组
*	后台-管理员一览-新增 新增一个管理员
*	<red>ec/oa 帐号数据来源尚未完善,后续以更新包方式补充</red>

<style>
red{
color : red;
}
</style>
