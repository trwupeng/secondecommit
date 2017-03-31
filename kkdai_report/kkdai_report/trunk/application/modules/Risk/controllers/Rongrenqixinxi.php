<?php
/**
 * 风控经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/18 0014
 * Time: 下午 14:01
 */
use \Prj\Data\FKRongRenQiXinxi as FKRongRenQiXinxi;
use Sooh\Base\Form\Item as form_def;

class RongrenqixinxiController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();
    }

    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];
    protected $categroy;
    protected $fieldsMap = [
        'kehubianhao'=>['kehubianhao','客户编号','b301'],
        'kehu'=>['kehu','客户','b302'],
        'leixing'=>['leixing','类型','b303','select',[], 50],
        'ming'=>['ming','名','b304'],
        'guanxi'=>['guanxi','关系','b305'],
        'zhengjianhaoma'=>['zhengjianhaoma','证件号码','b306','text',[],160],
        'xingbie'=>['xingbie','性别','b307','text',[],50],
        'nianling'=>['nianling','年龄','b308','text',[],50],
        'hunyinzhuangkuang'=>['hunyinzhuangkuang','婚姻状况','b309'],
        'lianxidianhua'=>['lianxidianhua','联系电话','b310'],
        'xianzhuzhi'=>['xianzhuzhi','现住址','b311','text',[],300],
        'hujidi'=>['hujidi','户籍地','b312','text',[],200],
        'gongzuodanwei'=>['gongzuodanwei','工作单位','b313','text',[],300],
        'danweidizhi'=>['danweidizhi','单位地址','b314','text',[],300],
        'fadingdaibiaoren'=>['fadingdaibiaoren','法定代表人','b315','text',[],70],
        'shijikongzhiren'=>['shijikongzhiren','实际控制人','b316','text',[],70],
        'guquanjiegou'=>['guquanjiegou','股权结构','b317','text',[],300],
        'bangongdizhi'=>['bangongdizhi','办公地址','b318','text',[],300],
        'beizhixingchaxunshijian'=>['beizhixingchaxunshijian','被执行查询时间','b319','datepicker',[],100],
        'zhengxinchaxunshijian'=>['zhengxinchaxunshijian','征信查询时间','b320','datepicker',[],100],
        'beizhu'=>['beizhu','备注','b321','text',[],300],
    ];

    protected $dtFormatFields = [
        'beizhixingchaxunshijian','zhengxinchaxunshijian'
    ];
    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');
        $this->categroy = ['全部类型']+FKRongRenQiXinxi::$logicFields['leixing'];
        $this->_view->assign('category', $this->categroy);
        $this->fieldsMap['leixing'][4] = FKRongRenQiXinxi::$logicFields['leixing'];
        $this->_viewFk = new \Prj\Misc\ViewFK();
        foreach($this->fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }

        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKRongRenQiXinxi::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($this->fieldsMap), $where, 'sort createTime');

            foreach($records as $k => $v){
                foreach($v as $key => $value) {
                    $records[$k][$key] = FKRongRenQiXinxi::parseFieldToString($key, $value);
                }

                if($v['leixing'] == 1){
                    if(!empty($records[$k]['zhengjianhaoma'])){
                        $records[$k]['nianling'] = date('Y') - substr($records[$k]['zhengjianhaoma'], -12, 4);
                    } else {
                        $records[$k]['nianling'] = '';
                    }
                }
                if ($v['leixing'] == 2) {
                    $records[$k]['xingbie'] = '';
                    $records[$k]['nianling'] = '';
                }
            }
            $this->_viewFk->setPk('id',false)->setData($records);
            $excel = $this->_viewFk->toExcel($records);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum,false);
        $pager->init(-1, $pageid);
        $kehubianhao = $this->_request->get('kehubianhao');
        $kehu = trim($this->_request->get('kehu'));
        $selectedCategory = $this->_request->get('selectedCategory');
        $this->_view->assign('selectedCategory', $selectedCategory);
        $this->_view->assign('kehubianhao', $kehubianhao);
        $where = [];

        !empty($selectedCategory) && $where['leixing']=$selectedCategory;
        !empty($kehubianhao) && $where['kehubianhao']= $kehubianhao;
            !empty($kehu) && $where=['kehu*'=>'%'.$kehu.'%'];

        $records = FKRongRenQiXinxi::paged($pager, $where, 'sort createTime');
        foreach($records as $k => $v) {
            foreach($v as $key => $value){
                $records[$k][$key] = FKRongRenQiXinxi::parseFieldToString($key, $value);
            }

            if($v['leixing'] == 1){
                if(!empty($records[$k]['zhengjianhaoma'])){
                    $records[$k]['nianling'] = date('Y') - substr($records[$k]['zhengjianhaoma'], -12, 4);
                } else {
                    $records[$k]['nianling'] = '';
                }
            }
            if ($v['leixing'] == 2) {
                $records[$k]['xingbie'] = '';
                $records[$k]['nianling'] = '';
            }
        }
        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'del'));
        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('kehu', $kehu);
        $this->_view->assign('where', urlencode(json_encode($where)));
    }


    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        if(empty($record['kehu'])){
            return $this->returnError('客户不能为空');
        }
        unset($record['nianling']);
        if($record['leixing'] == 1 && !empty($record['zhengjianhaoma'])) {
            $record['xingbie'] = trim($record['xingbie']);
            if($record['xingbie']!='男' && $record['xingbie'] !='女'){
                $record['xingbie'] = '-';
            }
        }else {
            $record['xingbie'] = '';
        }

        foreach(FKRongRenQiXinxi::$formatDateType as $fieldName => $v) {
            if(!empty($record[$fieldName])){
                if(!Rpt\Funcs::check_date($record[$fieldName])){
                    return $this->returnError('日期格式错误, 格式如:2016-08-08');
                }else {
                    $record[$fieldName] = date('Ymd', strtotime($record[$fieldName]));
                }
            }else{
                $record[$fieldName] = 0;
            }
        }

        $record['updateTime']=time();
        // 新增记录
        if(empty($id)){
            $model = FKRongRenQiXinxi::getCopy();
            $tb = $model->tbname();
            $db = $model->db();
            $record['createTime']=time();
            $record['iRecordVerID'] = 1;

            try {
                $id = $db->addRecord($tb, $record);
                $this->_view->assign('_id', $id);
            }catch(\ErrorException $e) {
                return $this->returnError('添加失败');
            }
            return $this->returnOK('添加成功');

        }else{
            // 更新记录
            $model = FKRongRenQiXinxi::getCopy($id);
            $model->load();
            if(!$model->exists()){
                return $this->returnError('未查找到要修改记录');
            }
            foreach($record as $k => $v) {
                $model->setField($k, $v);
            }

            try {
                $model->update();
                return $this->returnOK('更新成功');
            }catch (\ErrorException $e) {
                return $this->returnError('更新失败');
            }
        }
    }

    public function delAction () {
        $id = $this->_request->get('_id');
        $model=FKRongRenQiXinxi::getCopy($id);
        $model->load();
        if($model->exists()){
            $model->delete();
            return $this->returnOk('删除成功');
        }else {
            return $this->returnError('要删除的记录不存在');
        }
    }
    
    /**
     * 导入功能开发
     **/
    protected $location;
    protected $title=['kehubianhao','kehu','leixing','ming','guanxi','zhengjianhaoma',
                       'xingbie','nianling','hunyinzhuangkuang','lianxidianhua','xianzhuzhi',
                       'hujidi','gongzuodanwei','danweidizhi','fadingdaibiaoren','shijikongzhiren',
                       'guquanjiegou','bangongdizhi','beizhixingchaxunshijian','zhengxinchaxunshijian',
                       'beizhu'];   
                    
    public function importAction(){
    
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $formEdit->addItem('import', form_def::factory('导入数据', '', form_def::mulit));
    
        $formEdit->fillValues();
        if ($formEdit->flgIsThisForm){
    
            $fields = $formEdit->getFields();
            $rs=$fields['import'];
    
            $rem=\Prj\Misc\FengKongImport::exceltoarry($rs);
            $this->location=current($rem);
            unset($rem[0]);
            foreach ($rem as $v){
                $arr=preg_split("/[\t]/",$v);
                $arr[18]=\Prj\Misc\FengKongImport::checktime($arr[18]);
                $arr[19]=\Prj\Misc\FengKongImport::checktime($arr[19]);
                
                
                $pipei=FKRongRenQiXinxi::$logicFields;
                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],$pipei['leixing']);
    
                $result[]=[
                    'kehubianhao'=>$arr[0],
                    'kehu'=>$arr[1],
                    'leixing'=>$arr[2],
                    'ming'=>$arr[3],
                    'guanxi'=>$arr[4],
                    'zhengjianhaoma'=>$arr[5],
                    'xingbie'=>$arr[6],
                    'nianling'=>$arr[7],
                    'hunyinzhuangkuang'=>$arr[8],
                    'lianxidianhua'=>$arr[9],
                    'xianzhuzhi'=>$arr[10],
                    'hujidi'=>$arr[11],
                    'gongzuodanwei'=>$arr[12],
                    'danweidizhi'=>$arr[13],
                    'fadingdaibiaoren'=>$arr[14],
                    'shijikongzhiren'=>$arr[15],
                    'guquanjiegou'=>$arr[16],
                    'bangongdizhi'=>$arr[17],
                    'beizhixingchaxunshijian'=>$arr[18],
                    'zhengxinchaxunshijian'=>$arr[19],
                    'beizhu'=>$arr[20],
                ];
    
            }
            
            foreach ($result as $data){
                $data=$this->formatFields($data);
                if(empty($data['kehu'])){
                    return $this->returnError('客户不能为空');
                }
                unset($data['nianling']);
                if($data['leixing'] == 1 && !empty($data['zhengjianhaoma'])) {
                    $data['xingbie'] = trim($data['xingbie']);
                    if($data['xingbie']!='男' && $data['xingbie'] !='女'){
                        $data['xingbie'] = '-';
                    }
                }else {
                    $data['xingbie'] = '';
                }
                
                foreach(FKRongRenQiXinxi::$formatDateType as $fieldName => $v) {
                    if(!empty($data[$fieldName])){
                            $data[$fieldName] = date('Ymd', strtotime($data[$fieldName]));
                    }else{
                        $data[$fieldName] = 0;
                    }
                }
                
                    $data['updateTime']=time();
            
                    $model = FKRongRenQiXinxi::getCopy('');
                    $tb = $model->tbname();
                    $db = $model->db();
                    $data['createTime']=time();
                    $data['iRecordVerID'] = 1;
        
                    try {
                        $id = $db->addRecord($tb, $data);
                        $this->_view->assign('_id', $id);
                    }catch(\ErrorException $e) {
                        return $this->returnError('导入失败');
                    }

            }
             
                    $this->closeAndReloadPage($this->tabname('index'));
                    $this->returnOK('导入成功');
                    return;
        }else{
    
        }
    }
    
    
    public  function formatFields($data){
        $ret = [];
        $tmpDaoQiRiQi = 0;
        foreach ($data as $k => $v) {
            switch ($k) {
                case 'beizhixingchaxunshijian':
                    if(!empty($v)){
                        $datetime=explode('-',$v);
                        if(empty($datetime[2])){
                            $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                            throw new \Exception($name.'日期格式不正确', 90003);
                        }
                    }
                    $ret[$k]=$v;
                    break;
                case 'zhengxinchaxunshijian':
                    if(!empty($v)){
                        $datetime=explode('-',$v);
                        if(empty($datetime[2])){
                            $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                            throw new \Exception($name.'日期格式不正确', 90003);
                        }
                    }
                    $ret[$k]=$v;
                    break;
                case 'leixing':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKRongRenQiXinxi::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
    
}