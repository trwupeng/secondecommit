<?php
use \Rpt\EvtDaily\Base as prjlib_evtdaily;
/**
 * 日报等处的权限设置
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */

class RptconfController extends \Prj\ManagerCtrl {
	public function confAction()
	{
		$db = \Sooh\DB\Broker::getInstance('dbForRpt');
		$changeUser=$this->_request->get('loginName');
		if($changeUser){
			$scope = $this->_request->get('id');
			$r = $db->getOne('tb_managers_rights','rptRights',['loginName'=>$changeUser,'rightsType'=>'rpt']);
			$enable = $this->_request->get('enable')-0;
			if($enable){
				if(empty($r)){
					$db->addRecord('tb_managers_rights',['loginName'=>$changeUser,'rightsType'=>'rpt','rptRights'=>$scope]);
				}elseif($r!='*'){
					$r = explode(',', $r);
					$r[]=$scope;
					$r=array_unique($r);
					$r=  implode(',', $r);
					$db->updRecords('tb_managers_rights',['rptRights'=>$r],['loginName'=>$changeUser,'rightsType'=>'rpt']);
				}
			}else{
				if($r=='*'){
					$r=[];
					foreach(\Prj\Acl\RptLimit::$map as $id=>$capt){
						if($id!=$scope){
							$r[]=$id;
						}
					}
					$r=  implode(',', $r);
				}else{
					$r = explode(',', $r);
					foreach($r as $capt=>$id){
						if($id==$scope){
							unset($r[$capt]);
						}
					}
					$r=  implode(',', $r);
				}
				$db->updRecords('tb_managers_rights',['rptRights'=>$r],['loginName'=>$changeUser,'rightsType'=>'rpt']);
			}
			
			return $this->returnOK();
		}
		//$acl = \Prj\Acl\RptLimit::initWho($this->manager);
		
		$usr = array_merge(
				$db->getCol('tb_managers_0','loginName',['rights'=>'*.*']),
				$db->getCol('tb_managers_0','loginName',['rights*'=>'%report.%'])
				);
		$nicknames=$db->getPair('tb_managers_0', 'loginName','nickname',['loginName'=>$usr]);
		$rs = $db->getPair('tb_managers_rights', 'loginName','rptRights',['loginName'=>$usr,'rightsType'=>'rpt']);

		foreach($rs as $loginName=>$r){
			$rs[$loginName]=array('loginName'=>$loginName,'nickname'=>$nicknames[$loginName],);
			unset($nicknames[$loginName]);
			foreach(\Prj\Acl\RptLimit::$map as $id=>$capt){
				if($r=='*' || strpos($r, $id)!==false){
					$rs[$loginName][$id]=1;
				}else{
					$rs[$loginName][$id]=0;
				}
			}
		}
		foreach($nicknames as $loginName=>$nickname){
			$rs[$loginName]=array('loginName'=>$loginName,'nickname'=>$nickname,);
			foreach(\Prj\Acl\RptLimit::$map as $id=>$capt){
				$rs[$loginName][$id]=0;
			}
		}

		$this->_view->assign('RptAclList',$rs);
		
	}
}