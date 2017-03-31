<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;
// use Prj\Data\Copartner;
/**
 * 合作商协议管理
 * 各个字段  限制的测试
 * 
 *  
 * 
 */
class ContractsController extends \Prj\ManagerCtrl
{
    protected $copartners;
    protected $plans;
//     protected $pageSizeEnum = array(20, 50, 100);
    protected $copartnerIds;
    protected $promotionWay = array('cpc'=>'cpc','cpa'=>'cpa','cpm'=>'cpm');
    public function init(){
        parent::init();
        
        $obj = Prj\Data\Copartner::getCopy();
        $rs = $obj->getAllRecords();
        if (!empty($rs)) {
            foreach ($rs as $k => $v){
                $this->copartners[$v['copartnerAbs']] = $v['copartnerName'];
                $this->copartnerIds[$v[copartnerAbs]] = $v['copartnerId'];
            }
            foreach ($this->copartners as $cAbs=>$cname){
                $this->copartners[$cAbs]= $cname.'【'.$this->copartnerIds[$cAbs].'】';
            }
            reset($this->copartners);
        }else {
            $this->copartnerIds=[];
            $this->copartners=[];
        }
        $this->plans= array(0=>'无');
        
        // TODO:待添加具体的分成方案
    }
    public function indexAction ()
    {
       $pageid = $this->_request->get('pageId', 1) - 0;
       $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
       $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
       $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
       $formEdit->addItem('_copartnerAbs_eq', form_def::factory('合作商', '', form_def::select)/*->initMore(new options_def($this->copartners, '不限'), form_def::constval)*/)
           ->addItem('_contractId_eq', form_def::factory('协议号', '', form_def::text))
           ->addItem('pageid', $pageid)
           ->addItem('pagesize', $pager->page_size);
       $formEdit->items['_copartnerAbs_eq']->options = new options_def($this->copartners, '不限');
       
        $formEdit->fillValues();
        $keysStr = $this->_request->get('ids');
// var_log($keysStr, 'keyStr>>>>>>>>>>>>>>');     
        $keys = is_array($keysStr)?$keysStr:explode(',', $keysStr);
        if ($formEdit->flgIsThisForm){
            $where = $formEdit->getWhere();
        }else {
            $where = array();
        }
        
        if (!empty($keysStr)){
            foreach ($keys as $k => $v){
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['contractId'];
            }
            $where = array ('contractId'=>$keys);
        }
// var_log($where , 'where>>>>>>>>>>>>');
       $obj = contract_kobj::getCopy();
       $pager->init($obj->getAccountNum($where), $pageid);
       $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
       $fields= array('contractId', 'copartnerAbs','remarks','promotionWay', 'ymdStart', 'ymdEnd', 'profitsPlan', 'profitsFix',  'notes',);
       if ($isDownloadExcel) {
           $records = $obj->db()->getRecords($obj->tbname(), $fields, $where);
       }else {
           $records = $obj->db()->getRecords($obj->tbname(), $fields, $where, '', $pager->page_size, $pager->rsFrom());
       }

       if (!empty($records))
       {
           foreach ($records as $k => $r)
           {
               $_pkey_val_ = \Prj\Misc\View::encodePkey(array('contractId'=>$r['contractId']));
               if (!$isDownloadExcel){
                   $r['_pkey_val_'] = $_pkey_val_;
               }
               $r['profitsPlan'] = $this->plans[$r['profitsPlan']];
               $tmp = array('contractId'=>$r['contractId'], 'copartnerName'=>$this->copartners[$r['copartnerAbs']]);
               unset($r['contractId']);
               $r = array_merge($tmp, $r);
               $records[$k] = $r;                
           }
       }
       $headers= array('协议Id'=>80, '渠道名称'=>70, '渠道简称'=>80, '协议名称'=>70, '推广方式'=>40, '开始日期'=>70, '结束日期'=>70, '分成方案'=>40, '分成修正'=>40,  '备注'=>70);
       
       
       if ($isDownloadExcel){
           return $this->downExcel($records, array_keys($headers));
       }else {
           $this->_view->assign('headers', $headers);
           $this->_view->assign('records', $records);
           $this->_view->assign('pager', $pager);
       }
    }
    
    public function addnewAction() {
        if (empty($this->copartners)) {
            $this->returnError('没有渠道，请先添加渠道!');
            return;
        }
        
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        
        $firstKey = key($this->copartners);
        $formEdit->addItem('contractId', form_def::factory('协议Id', '', form_def::text)/*->verifyInteger(1, 100000000000000000, 999999999999999999)*/)
                ->addItem('copartnerAbs', form_def::factory('合作商', $firstKey, form_def::select)/*->initMore(new options_def($this->copartners), form_def::constval)*/)
                ->addItem('remarks', form_def::factory('协议名称', '', form_def::text))
                ->addItem('promotionWay', form_def::factory('推广方式', 'cpc', form_def::select))
                ->addItem('ymdStart', form_def::factory('开始日期', date('Ymd'), form_def::date))
                ->addItem('ymdEnd', form_def::factory('结束日期', 20500101, form_def::date))
                ->addItem('profitsPlan', form_def::factory('分成方案', '0', form_def::select)/*->initMore(new options_def($this->plans), form_def::constval)*/)
                ->addItem('profitsFix', form_def::factory('分成修正(0.5代表50%)', 1, form_def::text))
                ->addItem('notes', form_def::factory('说明', '', form_def::text));
        $formEdit->item('copartnerAbs')->options =  new options_def($this->copartners);
        $formEdit->item('profitsPlan')->options =  new options_def($this->plans);
        $formEdit->item('promotionWay')->options = new options_def($this->promotionWay);

        $this->_view->assign('FormOp', $op="添加");
        $formEdit->fillValues();
        if ($formEdit->flgIsThisForm){
            try{
                $fields = $formEdit->getFields();
                $contractId = $fields['contractId'];
                if(strlen($contractId)!=18){
                    $this->returnError($op.'失败：协议号是18位数字,前4位为渠道号');
                    return;
                }
                $obj = contract_kobj::getCopy(array('contractId'=>$contractId));
                $pkey = $obj->load();
                if ($pkey !==null){
                    $this->returnError($op.'失败：协议号已经存在');
                    return;
                }
                if (substr($contractId, 0, 4) != $this->copartnerIds[$fields['copartnerAbs']]){
                    $this->returnError('协议号和渠道号不匹配，应该以渠道号开头');
                    return;
                }
                foreach($fields as $k=>$v){
                    $obj->setField($k, $v);
                }
                $obj->update();
                $this->closeAndReloadPage($this->tabname('index'));
                $this->returnOK($op.'成功');
            }catch(\ErrorException $e){
//                 $errCode = $e->getCode() - 0;
//                 $this->returnError($op.'失败'.$e->getMessage());
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    $this->returnError($op.'失败：冲突，相关记录已经存在？');
                }else{
                    $this->returnError($op.'失败：'.$e->getMessage());
                }
            }
        }
    }

    public function updAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
                    ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);
        $formEdit->addItem('contractId', form_def::factory('协议Id', '', form_def::constval)/*->verifyInteger(1, 100000000000000000, 999999999999999999)*/)
                ->addItem('copartnerAbs', form_def::factory('合作商', '', form_def::select)/*->initMore(new options_def($this->copartners), form_def::constval)*/)
                ->addItem('remarks', form_def::factory('协议名称', '', form_def::text))
                ->addItem('promotionWay', form_def::factory('推广方式', 'cpc', form_def::select))
                ->addItem('ymdStart', form_def::factory('开始日期', date('Ymd'), form_def::date))
                ->addItem('ymdEnd', form_def::factory('结束日期', 20500101, form_def::date))
                ->addItem('profitsPlan', form_def::factory('分成方案', '0', form_def::select)/*->initMore(new options_def($this->plans), form_def::constval)*/)
                ->addItem('profitsFix', form_def::factory('分成修正(0.5代表50%)', 1, form_def::text))
                ->addItem('notes', form_def::factory('说明', '', form_def::text));
        $formEdit->items['copartnerAbs']->options =  new options_def($this->copartners);
        $formEdit->items['profitsPlan']->options =  new options_def($this->plans);  
        $formEdit->item('promotionWay')->options = new options_def($this->promotionWay);
        $this->_view->assign('FormOp', $op='更新');
        $formEdit->fillValues();
        
        if ($formEdit->flgIsThisForm){
            try{
                $fields = $formEdit->getFields();
                if($fields['subjectTo']==='0'){
                    $fields['subjectTo']='';
                }
// var_log($fields, 'fields>>>>>');
                $obj= \Prj\Data\Contract::getCopy(array('contractId'=>$fields['contractId']));
                $pkey = $obj->load();
                if ($pkey===null){
                    $this->returnError('不能更新，无相关记录');
                    return;
                }
                foreach ($fields as $k => $v){
                    $obj->setField($k, $v);
                }
                $obj->update();
                $this->returnOK($op.'成功');
                $this->closeAndReloadPage($this->tabname('index'));
                
            }catch(\ErrorException $e){
                $errCode = $e->getCode() - 0;
                $this->returnError($op.'失败：'.$e->getMessage());
            }
        }else {
            $obj = contract_kobj::getCopy($where);
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


    protected function getDB () {
        $db = \Sooh\DB\Broker::getInstance('default');
        return $db;
    }
}