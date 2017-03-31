<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Copartner as copartner_kobj;
/**
 * 老的渠道转换成目前的渠道
 * 
 */
class CopartnerstransController extends \Prj\ManagerCtrl
{
     protected $pageSizeEnum = array(200, 300, 400);
    public function indexAction ()
    {
       $pageid = $this->_request->get('pageId', 1) - 0;
       $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
       $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
       $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
       $formEdit->addItem('_copartnerName_lk', form_def::factory('渠道简称关键字', '', form_def::text))
           ->addItem('_contractId_eq', form_def::factory('协议号', '', form_def::text))
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
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['autoid'];
            }
            $where = array ('autoid'=>$keys);
        }

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $fields = 'autoid,copartnerName,contractId';


       $records = $db->getRecords(\Rpt\Tbname::tb_copartners_trans, $fields, $where);
       $pager->init(sizeof($records), $pageid);
       $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
       if (!$isDownloadExcel) {
           $records = $db->getRecords(\Rpt\Tbname::tb_copartners_trans, $fields, $where, null, $pager->page_size, $pager->rsFrom());
       }

       if (!empty($records))
       {
           foreach ($records as $k => $r)
           {
               if (!$isDownloadExcel) {
                   $_pkey_val_ = \Prj\Misc\View::encodePkey(['autoid'=>$r['autoid']]);
                   $records[$k]['_pkey_val_'] = $_pkey_val_;
               }
               unset($records[$k]['autoid']);
           }
       }

       if ($isDownloadExcel){
           return $this->downExcel($records, array_keys($this->header));
       }else {
           $this->_view->assign('headers', $this->header);
           $this->_view->assign('records', $records);
           $this->_view->assign('pager', $pager);
       }
    }
    
    public function addnewAction () {
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
         $formEdit->addItem('copartnerName', form_def::factory('渠道名称简称', '', form_def::text)/*->verifyInteger(1, 1000, 9999)*/)
                  ->addItem('contractId', form_def::factory('协议号', '', form_def::text));

        $this->_view->assign('FormOp', $op='添加');

        $formEdit->fillValues();
        if ($formEdit->flgIsThisForm){ // 添加
            try {
                $fields = $formEdit->getFields();
                if(empty($fields['copartnerName']) || empty($fields['contractId'])){
                    $this->returnError($op.'失败：不能为空');
                    return;
                }
                if (!is_numeric($fields['contractId'])) {
                    $this->returnError($op.'失败：协议号必须是数字');
                    return;
                }
                $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
                $tmp = $db->getRecord(\Rpt\Tbname::tb_copartners_trans, 'copartnerName, contractId', ['copartnerName'=>$fields['copartnerName']]);
                if ($tmp['copartnerName'] == $fields['copartnerName']) {
                    $this->returnError($op.'失败：冲突，相关记录已经存在？');
                    return;
                }

                $db->addRecord(\Rpt\Tbname::tb_copartners_trans, $fields);
                $this->closeAndReloadPage($this->tabname('index'));
                $this->returnOK($op.'成功');
                return;
            }catch (\ErrorException $e){
                    $this->returnError($op.'失败：'.$e->getMessage());
            }
        }else { // 显示表单
        }
    }
    
    public function updAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                 ->init(\Sooh\Base\Tools::uri(),'get',  \Sooh\Base\Form\Broker::type_u);
        $formEdit->addItem('copartnerName', form_def::factory('渠道名称简称', '', form_def::text)/*->verifyInteger(1, 1000, 9999)*/)
            ->addItem('contractId', form_def::factory('协议号', '', form_def::text))
        ->addItem('autoid', form_def::factory('id', '', form_def::hidden));
        $this->_view->assign('FormOp', $op='更新');       
        $formEdit->fillValues();
// var_log($_REQUEST, '_REQUEST>>>>>');
        
        if ($formEdit->flgIsThisForm){
            try {
                
                $fields = $formEdit->getFields();
                if(empty($fields['copartnerName']) || empty($fields['contractId'])){
                    $this->returnError($op.'失败：不能为空');
                    return;
                }
                if (!is_numeric($fields['contractId'])) {
                    $this->returnError($op.'失败：协议号必须是数字');
                    return;
                }

                $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
                $n = $db->getRecordCount(\Rpt\Tbname::tb_copartners_trans, ['autoid'=>$fields['autoid']]);
                if (!$n) {
                    $this->returnError('不能更新，无相关记录');
                    return;
                }

                $n = $db->getRecords(\Rpt\Tbname::tb_copartners_trans, 'autoid, copartnerName, contractId', ['copartnerName'=>$fields['copartnerName']]);
                if(!empty($n)) {
                    foreach ($n as $k => $v) {
                        if($v['autoid'] !== $fields['autoid']) {
                            if ($v['copartnerName'] == $fields['copartnerName']) {
                                $this->returnError('不能更新，此渠道简称已经存在');
                                return;
                            }
                        }
                    }

                }

                $rs = $db->updRecords(\Rpt\Tbname::tb_copartners_trans, $fields, ['autoid'=>$fields['autoid']]);
//var_log($rs, 'rs>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
                $this->returnOK($op.'成功');
                $this->closeAndReloadPage($this->tabname('index'));
            }catch (\ErrorException $e){

                    $this->returnError($op.'失败：'.$e->getMessage());
            }
        }else {
//            var_log($where, 'where>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
            $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
            $record= $db->getRecord(\Rpt\Tbname::tb_copartners_trans, 'autoid,copartnerName,contractId',$where);
            if (empty($record)) {
                $this->returnError('记录找不到');
            }else {
                $ks = array_keys($formEdit->items);
                foreach($ks as $k) {
                    $formEdit->item($k)->value = $record[$k];
                }
            }
        }
        
    }

    public function delAction () {
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        if (empty($where)) {
            $this->returnError('删除失败');
        }
        $n = $db->getRecordCount(\Rpt\Tbname::tb_copartners_trans, $where);
        if (empty($n)) {
            $this->returnError('删除失败：未找到此记录！');
        }
        $n = $db->delRecords(\Rpt\Tbname::tb_copartners_trans, $where);
        if ($n) {
//             $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('删除成功');
        }else {
            $this->returnError('删除失败');
        }



// var_log($where, __FUNCTION__.'>>>>>>>where>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
    }

    protected $header = [
        '渠道简称'=>100,
        '协议Id'=>100,
    ];
    
}