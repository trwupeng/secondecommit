<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/29
 * Time: 10:34
 */
use Sooh\Base\Form\Item as form_def;
class DiscuzController extends \Prj\ManagerCtrl {
    public function indexAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'img'    => ['图片', '20'],
            'exp'    => ['说明', '20'],
            'type'    => ['类型', '20'],
            'url'    => ['超链接', '20'],
            'sort'    => ['排序', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户号(精确)', '', form_def::text))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\Img::paged($pager,['status]'=>0],'rsort type sort sort rsort updateTime');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
                }
                //todo 主键匹配
                if(!in_array($v['orderId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $imgUrl = \Sooh\Base\Tools::uri(['id'=>$v['fileId']],'img','public','index');
            $tempArr['img'] = "<img style='height:50px' src='".$imgUrl."' />";
            $tempArr['type'] = \Prj\Consts\Discuz::$img_types[$tempArr['type']];
            //===
            $_pkey_val_ = \Prj\Misc\View::encodePkey(['id'=>$v['imgId']]);
            $newArr[$_pkey_val_] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }
    public function editAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('exp', form_def::factory('说明', '', form_def::text, [], []))
            ->addItem('type', form_def::factory('类型', '', form_def::select, \Prj\Consts\Discuz::$img_types+[''=>''], ['data-rule' => 'required']))
            ->addItem('url', form_def::factory('跳转链接', '', form_def::text, [], []))
            ->addItem('sort', form_def::factory('排序', 0, form_def::text, [], ['data-rule' => 'number']))
            ->addItem('_fileId', '')
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fileId = $this->_request->get('_fileId');
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                    if(empty($fileId))return $this->returnError('未上传图片！');
                    $fields['fileId'] = $fileId;
                    $img = \Prj\Data\Img::add($fields,$this->manager->getField('loginName'));
                    try{
                        $img->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    try{
                        $result = \Prj\Data\File::updateStatus($fileId,1);
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                    $img = \Prj\Data\Img::getCopy($where['id']);
                    $img->load();
                    if(!$img->exists())return $this->returnError('找不到记录');
                    foreach($fields as $k=>$v){
                        $img->setField($k,$v);
                    }
                    if(!empty($fileId))$img->setField('fileId',$fileId);
                    try{
                        $img->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }

                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $img = \Prj\Data\Img::getCopy($where['id']);
            $img->load();
            $arr = $img->dump();
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
            $imgUrl = \Sooh\Base\Tools::uri(['id'=>$arr['fileId']],'img','public','index');
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('imgUrl', $imgUrl);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function uploadAction(){
        $fileArr = $_FILES['file'];
        $data = file_get_contents($fileArr['tmp_name']);
        if($fileId = \Prj\Data\File::createNew($data)){
            $this->_view->assign('fileId', $fileId);
        }else{

        }
        $this->returnOK('成功');
    }

    public function deleteAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        try{
            \Prj\Data\Img::updStatus($where['id'],-4);
        }catch (\ErrorException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK('成功');
    }

    /**
     * 推送图片到DZ
     * @throws ErrorException
     * @throws \Sooh\Base\ErrorException
     */
    public function pushImgAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $where = ['status]'=>0];
        $rs = \Prj\Data\Img::loopFindRecords($where);
        $newRs = [];
        foreach($rs as $v){
            $newRs[] = [
                'title'=>$v['exp'],
                'img'=>'http://'.$_SERVER['HTTP_HOST'].\Sooh\Base\Tools::uri(['id'=>$v['fileId']],'img','public','index'),
                'url'=>$v['url'],
                'type'=>$v['type'],
            ];
        }

        $data = json_encode($newRs);
        $url = \Sooh\Base\Ini::getInstance()->get('uriBase')['dzimg'];
        if(empty($url))return $this->returnError('配置错误#missing_url');
        $dt = time();
        $key = \Sooh\Base\Ini::getInstance()->get('TestKey');
        if(empty($key))return $this->returnError('配置错误#missing_key');
        $sign = md5($key.$dt);
        $url .= "?dt=$dt&sign=$sign";
        $return = \Prj\Misc\Funcs::curl_post($url,['data'=>$data],5);
        if(empty($return))return $this->returnError('连接失败');
        $return = json_decode($return,true);
        var_log($data,'pushImg#'.$url.'#>>>');
        if($return['code']!=200)return $this->returnError('连接错误#'.$return['msg']);
        return $this->returnOK('成功');
    }
}