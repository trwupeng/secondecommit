<?php
/**
 *
 * 投资项目表
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/20 0014
 * Time: 下午 15:34
 */
use \Prj\Data\FKFangKuanTiJiangBiao as FKFangKuanTiJiangBiao;
use \Prj\Data\FKRongZiXiangMuBiao as FKRongZiXiangMuBiao;
use Sooh\Base\Form\Item as form_def;
class FangkuantijiangbiaoController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();
        $this->_viewFk = new \Prj\Misc\ViewFK();
        $model = FKRongZiXiangMuBiao::getCopy();
        $this->rongzihetongbianhao = $model->db()->getPair($model->tbname(), 'id', 'rongzihetongbianhao');
        $this->rongzihetongbianhao = [0=>'']+$this->rongzihetongbianhao;
        $this->fieldsMap['rongzihetongbianhao'][] = $this->rongzihetongbianhao;

        $tmp = $model->db()->getAssoc($model->tbname(), 'id',
            'jiekuanren,jiekuanewanyuan,qishiriqi,yue');
        $this->auto_fields_value = [];
        foreach($tmp as $k => $v) {
            $this->auto_fields_value[$k] =  [
                'b102'=>$v['jiekuanren'],
                'b107'=>$v['jiekuanewanyuan'],
                'b108'=>$v['qishiriqi'],
                'b109'=>$v['yue'],
            ];
        }
        $this->_view->assign('auto_fields_value', $this->auto_fields_value);

        $model = \Prj\Data\FKKeHuJingLiMingCe::getCopy();
        $tmp = $model->db()->getPair($model->tbname(), 'id', 'xingming');
        $this->kehujingli = [0=>'']+$tmp;
        $this->_view->assign('kehujingli', $this->kehujingli);
        $this->fieldsMap['kehujingli'][] = $this->kehujingli;
        $this->fieldsMap['jinglixingming'][] = $this->kehujingli;
        $this->fieldsMap['zongjianxingming'][] = $this->kehujingli;
        $this->fieldsMap['zhanqi'][] = FKFangKuanTiJiangBiao::$logicFields['zhanqi'];
        $this->fieldsMap['diya'][]=FKFangKuanTiJiangBiao::$logicFields['diya'];
        $this->fieldsMap['huanfanglei'][]=FKFangKuanTiJiangBiao::$logicFields['huanfanglei'];

        $model = \Prj\Data\FKFengKongJingLiMingCe::getCopy();
        $this->fengkongjingli = $model->db()->getPair($model->tbname(),'id', 'xingming');
        $this->fieldsMap['fengkongjingli'][] = $this->fengkongjingli;

        $fangkuanren = \Prj\Data\FKFangKuanRenMingCe::fangkuanren();
        $this->fieldsMap['fangkuanren'][]=[0=>'']+$fangkuanren;


        $model= FKRongZiXiangMuBiao::getCopy();
        $this->rongzihetongInfo = $model->db()->getAssoc($model->tbname(), 'id', 'jiekuanren,jiekuanewanyuan,qishiriqi,yue');
        foreach($this->rongzihetongInfo as $k => $v){
            if(!empty($v['jiekuanewanyuan'])){
                $this->rongzihetongInfo[$k]['jiekuanewanyuan']*=0.000001;
            }else {
                $this->rongzihetongInfo[$k]['jiekuanewanyuan'] = '';
            }
            if(!empty($v['qishiriqi'])){
                $this->rongzihetongInfo[$k]['qishiriqi'] = date('Y-m-d', $this->rongzihetongInfo[$k]['qishiriqi']);
            }else {
                $this->rongzihetongInfo[$k]['qishiriqi'] = '';
            }
        }
        $this->_view->assign('rongzihetongInfo', $this->rongzihetongInfo);
    }

    /**
     * 结清情况
     * @var array
     */

//    protected $arr_rongzihetongbianhao;

    protected $auto_fields_value;
    protected $pageSizeEnum = [10, 20, 30];
    protected $kehujingli;
    protected $rongzihetongbianhao;
    protected $rongzihetongInfo;
    protected $fengkongjingli;
    /**
     * 根据合同编号获得值的字段
     * @var array
     */
    protected $autoFields = [
        'jiekuanren','fangkuanshijian','jiekuanewanyuan','qixianyue'
    ];
    protected $fieldsMap = [
        'id'=>['id','id','b800','text'],
        'rongzihetongbianhao'=>['rongzihetongbianhao','融资合同编号','b801', 'select'],
        'jiekuanren'=>['jiekuanren','借款人','b802','text'],
        'fangkuanshijian'=>['fangkuanshijian','放款时间 ','b803','text'],
        'jiekuanewanyuan'=>['jiekuanewanyuan','借款额(万元)','b804','text'],
        'qixianyue'=>['qixianyue','期限(月)','b805','text'],
        'anyuebiliyue'=>['anyuebiliyue','按月比例(%/月) ','b806','text'],
        'zhanqi'=>['zhanqi','展期','b807', 'select'],
        'diya'=>['diya','抵押','b808','select'],
        'huanfanglei'=>['huanfanglei','换房类 ','b809','select'],
        'tijiangbiliyue'=>['tijiangbiliyue','提奖比例(%/月) ','b810','text'],
        'tijiangjineyuan'=>['tijiangjineyuan','提奖金额(元) ','b811','text'],
        'yuliubili'=>['yuliubili','预留比例(%)','b812','text'],
        'kehujingli'=>['kehujingli','客户经理','b813','select'],
        'gerentijiangyuan'=>['gerentijiangyuan','个人提奖(元) ','b814','text'],
        'gerenyuliuyuan'=>['gerenyuliuyuan','个人预留(元) ','b815','text'],
        'gerenfafangyuan'=>['gerenfafangyuan','个人发放(元)','b816','text'],
        'jinglixingming'=>['jinglixingming','经理姓名 ','b817','select'],
        'jinglitijiangbili'=>['jinglitijiangbili','经理提奖比例(%) ','b818','text'],
        'jinglitijiang'=>['jinglitijiang','经理提奖(元)','b819','text'],
        'jingliyuliu'=>['jingliyuliu','经理预留(元)','b820','text'],
        'jinglifafang'=>['jinglifafang','经理发放(元)','b821','text'],
        'zongjianxingming'=>['zongjianxingming','总监姓名','b822','select'],
        'zongjiantijiangbili'=>['zongjiantijiangbili','总监提奖比例(%)','b823','text'],
        'zongjiantijiang'=>['zongjiantijiang','总监提奖(元)','b824','text'],
        'zongjianyuliu'=>['zongjianyuliu','总监预留(元)','b825','text'],
        'zongjianfafang'=>['zongjianfafang','总监发放(元)','b826','text'],
        'fengkongjingli'=>['fengkongjingli','风控经理 ','b827', 'selects'],
        'fengkongtijiang'=>['fengkongtijiang','风控提奖(元)','b828','text'],
        'fangkuanren'=>['fangkuanren','放款人','b829','select'],
        'fangkuanrentijiang'=>['fangkuanrentijiang','放款人提奖(元)','b830','text'],
        'beizhu'=>['beizhu','备注','b831','text'],
    ];


    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');
        foreach($this->fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKFangKuanTiJiangBiao::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($this->fieldsMap), $where, 'sort createTime');
            foreach($records as $k => $v) {
                foreach($v as $key => $value) {
                    foreach($v as $key => $value) {
                        $records[$k][$key] = \Prj\Data\FKFangKuanTiJiangBiao::parseFieldToString($key, $value);
                    }
                }
                if(isset($this->rongzihetongInfo[$v['rongzihetongbianhao']])){
                    $records[$k]['jiekuanren'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['jiekuanren'];
                    $records[$k]['fangkuanshijian'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['qishiriqi'];
                    $records[$k]['jiekuanewanyuan'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['jiekuanewanyuan'];
                    $records[$k]['qixianyue'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['yue'];
                }else {
                    $records[$k]['jiekuanren'] = '';
                    $records[$k]['fangkuanshijian'] = '';
                    $records[$k]['jiekuanewanyuan'] = '';
                    $records[$k]['qixianyue'] = '';
                }
            }

            $this->_viewFk->setPk('id',false)->setData($records);
            $excel = $this->_viewFk->toExcel($records);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        $pageid = $this->_request->get('pageId',1)-0;
        $ps = current($this->pageSizeEnum);
        $pagesize = $this->_request->get('pageSize', $ps) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum,false);
        $pager->init(-1, $pageid);

        $rongzihetongbianhao = trim($this->_request->get('rongzihetongbianhao'));
        $kehujingli = $this->_request->get('kehujingli');
        $this->_view->assign('rongzihetongbianhao', $rongzihetongbianhao);
        $this->_view->assign('kehujingli', $kehujingli);
        $where = [];

        if(!empty($kehujingli)){
            $tmp=[];
            foreach($this->kehujingli as $id => $xingming) {
                if(strpos($xingming, $kehujingli)!==false){
                    $tmp[]=$id;
                }
            }

            if(!empty($tmp)){
                $where['kehujingli'] = $tmp;
            }
        }
        if(!empty($rongzihetongbianhao)){
            $model_rongzixiangmubiao = \Prj\Data\FKRongZiXiangMuBiao::getCopy();
            $ids = $model_rongzixiangmubiao->db()->getCol($model_rongzixiangmubiao->tbname(), 'id',
                ['rongzihetongbianhao*'=>'%'.$rongzihetongbianhao.'%']);
            if(!empty($ids)){
                $where['rongzihetongbianhao']= $ids;
            }
        }

        $records = FKFangKuanTiJiangBiao::paged($pager, $where, 'sort createTime');
        foreach($records as $k => $v) {
            foreach($v as $key => $value) {
                foreach($v as $key => $value) {
                    $records[$k][$key] = \Prj\Data\FKFangKuanTiJiangBiao::parseFieldToString($key, $value);
                }
            }

            if(isset($this->rongzihetongInfo[$v['rongzihetongbianhao']])){
                $records[$k]['jiekuanren'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['jiekuanren'];
                $records[$k]['fangkuanshijian'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['qishiriqi'];
                $records[$k]['jiekuanewanyuan'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['jiekuanewanyuan'];
                $records[$k]['qixianyue'] = $this->rongzihetongInfo[$v['rongzihetongbianhao']]['yue'];
            }else {
                $records[$k]['jiekuanren'] = '';
                $records[$k]['fangkuanshijian'] = '';
                $records[$k]['jiekuanewanyuan'] = '';
                $records[$k]['qixianyue'] = '';
            }

        }
        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'del'));

        $this->_viewFk->addTab('subtab_yuliufanhuanzhang', '预留返还帐', \Sooh\Base\Tools::uri([], 'index', 'yuliufanhuanzhang'), 'id', 'rongzihetongbianhao');
        $this->_viewFk->addTab('subtab_rongzixiangmubiao', '融资项目表', \Sooh\Base\Tools::uri([], 'index', 'rongzixiangmubiao'), 'id', 'rongzihetongbianhao');
        $this->_viewFk->addTab('subtab_rongzidangan', '融资档案', \Sooh\Base\Tools::uri([], 'index', 'rongzidangan'), 'id', 'rongzihetongbianhao');
        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('where', urlencode(json_encode($where)));
    }

    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        $record['updateTime']=time();
        if(!empty($record['fengkongjingli'])){
            $record['fengkongjingli'] = json_encode($record['fengkongjingli']);
        }
        foreach(FKFangKuanTiJiangBiao::$formatMoneyType as $fieldName => $unit) {
            if(!empty($record[$fieldName])){
                if(!is_numeric($record[$fieldName])){
                    return $this->returnError('金额必须是数字');
                }else{
                    $amount = ($record[$fieldName]-0)/$unit*100;
                    if(sizeof(explode('.', $amount))>1){
                        return $this->returnError('金额最多精确到分');
                    }
                    $record[$fieldName] = $amount;
                }
            }else {
                $record[$fieldName] = 0;
            }
        }

        foreach(FKFangKuanTiJiangBiao::$formatDateType as $fieldName=>$v) {
            if(!empty($record[$fieldName])){
                if(!\Rpt\Funcs::check_date($record[$fieldName])){
                    return $this->returnError('日期格式错误');
                }else {
                    $record[$fieldName] = date('Ymd', strtotime($record[$fieldName]));
                }
            }else {
                $record[$fieldName] = 0;
            }
        }

        foreach(FKFangKuanTiJiangBiao::$formatPercentageType as $fieldName) {
            if(empty($record[$fieldName])){
                $record[$fieldName]+=0;
            }
            if(!is_numeric($record[$fieldName])) {
                return $this->returnError('请检查数字相关字段格式');
            }else {
                $record[$fieldName] *= 100;
            }
        }

        foreach($this->autoFields as $fieldName) {
            unset($record[$fieldName]);
        }

        // 新增记录
        if(empty($id)){
            $model = FKFangKuanTiJiangBiao::getCopy();
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
            $model = FKFangKuanTiJiangBiao::getCopy($id);
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
        $model=FKFangKuanTiJiangBiao::getCopy($id);
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
    protected $title=[ 'rongzihetongbianhao','jiekuanren','fangkuanshijian','jiekuanewanyuan','qixianyue',
                       'anyuebiliyue','zhanqi','diya','huanfanglei','tijiangbiliyue','tijiangjineyuan',
                       'yuliubili','kehujingli','gerentijiangyuan','gerenyuliuyuan','gerenfafangyuan',
                       'jinglixingming','jinglitijiangbili','jinglitijiang','jingliyuliu','jinglifafang',
                       'zongjianxingming','zongjiantijiangbili','zongjiantijiang','zongjianyuliu','zongjianfafang',
                       'fengkongjingli','fengkongtijiang','fangkuanren','fangkuanrentijiang','beizhu']; 
    protected $formatDateType = [
        'fangkuanshijian'
    ];

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
                
                $arr[2]=\Prj\Misc\FengKongImport::checktime($arr[2]);
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],\Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('rongzihetongbianhao'));
                $arr[6]=\Prj\Misc\FengKongImport::transvk($arr[6],FKFangKuanTiJiangBiao::$logicFields['zhanqi']);
                $arr[7]=\Prj\Misc\FengKongImport::transvk($arr[7],FKFangKuanTiJiangBiao::$logicFields['diya']);
                $arr[8]=\Prj\Misc\FengKongImport::transvk($arr[8],FKFangKuanTiJiangBiao::$logicFields['huanfanglei']);
                $arr[12]=\Prj\Misc\FengKongImport::transvk($arr[12],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[16]=\Prj\Misc\FengKongImport::transvk($arr[16],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[21]=\Prj\Misc\FengKongImport::transvk($arr[21],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[26]=\Prj\Misc\FengKongImport::transv2k($arr[26],\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming'));
                $arr[28]=\Prj\Misc\FengKongImport::transvk($arr[28],\Prj\Data\FKFangKuanRenMingCe::fangkuanren());
                
                
                $result[]=[
                    'rongzihetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'fangkuanshijian'=>$arr[2],
                    'jiekuanewanyuan'=>$arr[3],
                    'qixianyue'=>$arr[4],
                    'anyuebiliyue'=>$arr[5],
                    'zhanqi'=>$arr[6],
                    'diya'=>$arr[7],
                    'huanfanglei'=>$arr[8],
                    'tijiangbiliyue'=>$arr[9],
                    'tijiangjineyuan'=>$arr[10],
                    'yuliubili'=>$arr[11],
                    'kehujingli'=>$arr[12],
                    'gerentijiangyuan'=>$arr[13],
                    'gerenyuliuyuan'=>$arr[14],
                    'gerenfafangyuan'=>$arr[15],
                    'jinglixingming'=>$arr[16],
                    'jinglitijiangbili'=>$arr[17],
                    'jinglitijiang'=>$arr[18],
                    'jingliyuliu'=>$arr[19],
                    'jinglifafang'=>$arr[20],
                    'zongjianxingming'=>$arr[21],
                    'zongjiantijiangbili'=>$arr[22],
                    'zongjiantijiang'=>$arr[23],
                    'zongjianyuliu'=>$arr[24],
                    'zongjianfafang'=>$arr[25],
                    'fengkongjingli'=>$arr[26],
                    'fengkongtijiang'=>$arr[27],
                    'fangkuanren'=>$arr[28],
                    'fangkuanrentijiang'=>$arr[29],
                    'beizhu'=>$arr[30],
                ];
    
            }

            foreach ($result as $data){

                $data['updateTime']=time();
                if(!empty($data['fengkongjingli'])){
                    $data['fengkongjingli'] = json_encode($data['fengkongjingli']);
                }
                
                if(!empty($data['jiekuanewanyuan'])){
                    $data['jiekuanewanyuan']=$data['jiekuanewanyuan']*10000*100;
                }
                
                foreach(FKFangKuanTiJiangBiao::$formatMoneyType as $fieldName => $unit) {
                    if(!empty($data[$fieldName])){
                        if(!is_numeric($data[$fieldName])){
                            return $this->returnError('金额必须是数字');
                        }else{
                            $amount = ($data[$fieldName]-0)/$unit*100;
                            if(sizeof(explode('.', $amount))>1){
                                return $this->returnError('金额最多精确到分');
                            }
                            $data[$fieldName] = $amount;
                        }
                    }else {
                        $data[$fieldName] = 0;
                    }
                }
               
                foreach($this->formatDateType as $fieldName=>$v) {
                    if(!empty($data[$v])){
                        if(!\Rpt\Funcs::check_date($data[$v])){
                            return $this->returnError('日期格式错误');
                        }else {
                            $data[$v] = date('Ymd', strtotime($data[$v]));
                        }
                    }else {
                        $data[$v] = 0;
                    }
                }
               
                foreach(FKFangKuanTiJiangBiao::$formatPercentageType as $fieldName) {
                    if(empty($data[$fieldName])){
                        $data[$fieldName]+=0;
                    }
                    if(!is_numeric($data[$fieldName])) {
                        return $this->returnError('请检查数字相关字段格式');
                    }else {
                        $data[$fieldName] *= 100;
                    }
                }
             
                try{
                   
                    $model = FKFangKuanTiJiangBiao::getCopy('');
                    $tb = $model->tbname();
                    $db = $model->db();
                    $data['createTime']=time();
                    $data['iRecordVerID'] = 1;
                    
                    $id = $db->addRecord($tb, $data);
                    $this->_view->assign('_id', $id);
                   
                }catch (\Exception $e){
                    return $this->returnError('导入失败');
                }
            }
             
            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('导入成功');
            return;
        }else{
    
        }
    }
}