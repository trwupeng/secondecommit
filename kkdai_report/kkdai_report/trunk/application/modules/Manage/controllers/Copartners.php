<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Copartner as copartner_kobj;
/**
 * 渠道管理
 * 
 */
class CopartnersController extends \Prj\ManagerCtrl
{
//     protected $pageSizeEnum = array(20, 50, 100);
    public function indexAction ()
    {
       $pageid = $this->_request->get('pageId', 1) - 0;
       $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
       $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
       $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
       $formEdit->addItem('_copartnerId_eq', form_def::factory('ID', '', form_def::text))
           ->addItem('_copartnerName_lk', form_def::factory('名称关键词', '', form_def::text))
           ->addItem('pageid', $pageid)
           ->addItem('pagesize', $pager->page_size);
        $formEdit->fillValues();
        $keysStr = $this->_request->get('ids');
        $keys = is_array($keysStr)?$keysStr:explode(',', $keysStr);
        if ($formEdit->flgIsThisForm){
            $where = $formEdit->getWhere();
        }else {
            $where = array();
        }
        
        if (!empty($keysStr)){
            foreach ($keys as $k => $v){
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['copartnerId'];
            }
            $where = array ('copartnerId'=>$keys);
        }
        
       $obj = new copartner_kobj();
       $pager->init($obj->getAccountNum($where), $pageid);
       $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
       $fields= array('copartnerId', 'copartnerName', 'copartnerAbs', 'contractorBiz', 'contractorDev', 'flgDisable');
       if ($isDownloadExcel) {
           $records = $obj->db()->getRecords($obj->tbname(), $fields, $where, 'sort copartnerId');
       }else {
           $records = $obj->db()->getRecords($obj->tbname(), $fields, $where, 'sort copartnerId', $pager->page_size, $pager->rsFrom());
       }

       if (!empty($records))
       {
           foreach ($records as $k => $r)
           {
               $_pkey_val_ = \Prj\Misc\View::encodePkey(array('copartnerId'=>$r['copartnerId']));
               unset($r['flgDisable']);
               if (!$isDownloadExcel){
                   $r['_pkey_val_'] = $_pkey_val_;
               }
               $records[$k] = $r;                
           }
       }
       $headers= array('渠道Id'=>70, '渠道名称'=>70, '渠道简称'=>80, '联系人(业务)'=>70, '联系人(技术)'=>70);
       
       if ($isDownloadExcel){
           return $this->downExcel($records, array_keys($headers));
       }else {
           $this->_view->assign('headers', $headers);
           $this->_view->assign('records', $records);
           $this->_view->assign('pager', $pager);
       }
    }
    
    public function addnewAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
         $formEdit->addItem('copartnerId', form_def::factory('渠道ID', '', form_def::text)/*->verifyInteger(1, 1000, 9999)*/)
                  ->addItem('copartnerName', form_def::factory('渠道名称', '', form_def::text))
                  // 渠道简称要以字母开头， 这个方便在协议管理中使用
                  ->addItem('copartnerAbs', form_def::factory('渠道简称', '', form_def::text))
				  ->addItem('authCode', form_def::factory('授权码', '', form_def::text))
                  ->addItem('contractorBiz', form_def::factory('联系人1(业务)', '', form_def::text))
                  ->addItem('contractorDev', form_def::factory('联系人2(技术)', '', form_def::text));

        $this->_view->assign('FormOp', $op='添加');

        $formEdit->fillValues();
        if ($formEdit->flgIsThisForm){ // 添加
            try {
                $fields = $formEdit->getFields();
                if(ctype_lower((string)substr($fields['copartnerAbs'], 0, 1)) || ctype_upper((string)substr($fields['copartnerAbs'], 0, 1))){
                }else{
                    $this->returnError($op.'失败：渠道简称第一位必须是字母');
                    return;
                }
              
                $obj = \Prj\Data\Copartner::getCopy(array('copartnerId'=>$fields['copartnerId']));
                $pkey = $obj->load();
                if ($pkey!==null){
                    $this->returnError($op.'失败：相关记录已经存在');
                    return;
                }
                foreach ($fields as $k => $v){
                    $obj->setField($k, $v);
                }
                $obj->update();
                $this->closeAndReloadPage($this->tabname('index'));
                $this->returnOK($op.'成功');
                return;
            }catch (\ErrorException $e){
//                 $errCode = $e->getCode() - 0;
//                 $this->returnError($op.'失败：'.$e->getMessage());
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    $this->returnError($op.'失败：冲突，相关记录已经存在？');
                }else{
                    $this->returnError($op.'失败：'.$e->getMessage());
                }
            }
        }else { // 显示表单
        }
    }
    
    public function updAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
// var_log($where , __FUNCTION__.'>>>$where>>>>>>>');
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                 ->init(\Sooh\Base\Tools::uri(),'get',  \Sooh\Base\Form\Broker::type_u);
        $formEdit->addItem('copartnerId', form_def::factory('渠道ID', '', form_def::constval)/* ->verifyInteger(1, 1000, 9999) */)
        ->addItem('copartnerName', form_def::factory('渠道名称', '', form_def::text)/* ->verifyString(1, 1, 99999) */)
        ->addItem('copartnerAbs', form_def::factory('渠道简称', '', form_def::constval)/* ->verifyIdentifier(1, 3, 16) */) 
		->addItem('authCode', form_def::factory('授权码', '', form_def::text))
        ->addItem('contractorBiz', form_def::factory('联系人1(业务)', '', form_def::text))
        ->addItem('contractorDev', form_def::factory('联系人2(技术)', '', form_def::text));
        $this->_view->assign('FormOp', $op='更新');       
        $formEdit->fillValues();
// var_log($_REQUEST, '_REQUEST>>>>>');
        
        if ($formEdit->flgIsThisForm){
            try {
                
                $fields = $formEdit->getFields();
                
                if(ctype_lower((string)substr($fields['copartnerAbs'], 0, 1)) || ctype_upper((string)substr($fields['copartnerAbs'], 0, 1))){
                }else{
                    $this->returnError($op.'失败：渠道简称第一位必须是字母');
                    return;
                }
                
                $obj = \Prj\Data\Copartner::getCopy(array('copartnerId'=>$fields['copartnerId']));
                $pkey = $obj->load();
                if ($pkey===null) {
                    $this->returnError('不能更新，无相关记录');
                    return;
                }
                foreach ($fields as $k => $v){
                    $obj->setField($k, $v);
                }
                $obj->update();
                $this->returnOK($op.'成功');
                $this->closeAndReloadPage($this->tabname('index'));
            }catch (\ErrorException $e){
//                 $errCode = $e->getCode() - 0;
//                 $this->returnError($op.'失败：'.$e->getMessage());
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    $this->returnError($op.'失败：冲突，相关记录已经存在？');
                }else{
                    $this->returnError($op.'失败：'.$e->getMessage());
                }
            }
        }else {
            $obj = copartner_kobj::getCopy($where);
            $pkey = $obj->load();
            if ($pkey===null){
                $this->returnError('记录找不到');
            }else {
                $ks = array_keys($formEdit->items);
                foreach ($ks as $k){
                    if ($obj->exists($k)){
                        $formEdit->item($k)->value=$obj->getField($k);
                    }
                }        
            }
        }
        
    }
    
}