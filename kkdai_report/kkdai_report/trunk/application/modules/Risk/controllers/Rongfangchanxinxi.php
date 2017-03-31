<?php

use \Prj\Data\FKRongFangChanXinXi as FKRongFangChanXinXiModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;
/**
 * 融房产信息
 * Class RongfangchanxinxiController
 * @author lingtm <lingtima@gmail.com>
 */
class RongfangchanxinxiController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $where = $this->_request->get('where');
        if (!empty($where)) {
            $where = json_decode(urldecode($where), true);
        } else {
            $keHuBianHao = $this->_request->get('kehubianhao');
            !empty($keHuBianHao) && $where['kehubianhao'] = $keHuBianHao;
        }

        if ($isDownloadExcel) {
            $model = FKRongFangChanXinXiModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $where, 'sort createTime');
        } else {
            $data = FKRongFangChanXinXiModel::paged($pager, $where, 'sort createTime');
        }
        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    if ($key == 'diyalv') {
                        $temp[$k][$key] = round($var / 100);
                    } else {
                        $temp[$k][$key] = FKRongFangChanXinXiModel::parseFieldToString($key, $var);
                    }
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/rongfangchanxinxi/update', '/risk/rongfangchanxinxi/delete?')
             ->addRow('kehubianhao', '客户编号', 'b401', 'text', [], 100)
             ->addRow('kehu', '客户', 'b402', 'text', [], 60)
             ->addRow('fangchanquyu', '房产区域', 'b403', 'select', FengKongEnum::getInstance()->get('fangchanquyu'), 100)
             ->addRow('fangchandizhi', '房产地址', 'b404', 'text', [], 280)
             ->addRow('chanquanren', '产权人', 'b405', 'text', [], 150)
             ->addRow('mianji', '面积[㎡]', 'b406', 'text', [], 70)
             ->addRow('fangchanleixing', '房产类型', 'b407', 'select', ['无', '住宅', '大住宅', '商铺', '办公楼',], 80)
             ->addRow('shifouxuweihu', '是否需维护', 'b408', 'select', ['无', '是', '否',], 80)
             ->addRow('shifoudiya', '是否抵押', 'b409', 'select', ['无', '是', '否',], 60)
             ->addRow('pingguzhiwanyuan', '评估值[万元]', 'b410', 'text', [], 100)
             ->addRow('pinggushijian', '评估时间', 'b411', 'datepicker', [], 120)
             ->addRow('yinhangdiyae', '银行抵押额', 'b412', 'text', [], 100)
             ->addRow('yinhangshengyue', '银行剩余额', 'b413', 'text', [], 100)
             ->addRow('jiekuane', '借款额', 'b414', 'text', [], 60)
             ->addRow('diyalv', '抵押率[%]', 'b415', 'text', [], 100)
             ->addRow('jiekuandaoqishijian', '借款到期时间', 'b416', 'datepicker', [], 120)
             ->addRow('chandiaochaxunshijian', '产调查询时间', 'b417', 'datepicker', [], 120)
             ->addRow('xiacichaxunshijian', '下次查询时间', 'b418', 'datepicker', [], 120)
             ->addRow('beizhu', '备注', 'b419', 'text', [], 150);

        if ($isDownloadExcel) {
            $excel = $view->toExcel($temp);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        $this->_view->assign('_type', $this->_request->get('_type'));
        $this->_view->assign('where', $where);
        return 0;
    }

    public function updateAction()
    {
        $addFlag = false;
        $data = $this->getRequest()->get('values')[0];
        $key = $this->getRequest()->get('id');
        if (empty($key)) {
            $addFlag = true;
        }
        unset($data['id']);

        if (empty($data)) {
            $this->returnError('没有更新的内容，更新失败');
            return 0;
        }

        if ($addFlag) {
            try {
                $records = $this->formatField($data);
                $records['createTime'] = $records['updateTime'] = time();
                $records['status'] = 1;

                $tmpModel = FKRongFangChanXinXiModel::getCopy('');
                $ret = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('新增失败');
                    return 0;
                }
            }
            $this->_view->assign('_id', $ret);
            $this->returnOK('新增成功');
            return 0;
        } else {
            //update
            $model = FKRongFangChanXinXiModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $this->formatAndSetField($model, $data);
                $model->setField('updateTime', time());
                $model->update();
                $this->_view->assign('_id', $key);
                $this->returnOK('更新成功');
                return 0;
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('更新失败');
                    return 0;
                }
            }
        }
    }

    /**
     * 删除
     * @return int
     */
    public function deleteAction()
    {
        $key = $this->getRequest()->get('_id');

        $model = FKRongFangChanXinXiModel::getCopy($key);
        $model->load();
        if ($model->exists()) {
            $model->delete();
            $this->returnOK('删除成功');
            return 0;
        } else {
            $this->returnError('记录不存在或者已经被删除');
            return 0;
        }
    }

    /**
     * @param \Sooh\DB\Base\KVObj $model
     * @param       array         $data
     * @throws Exception
     * @return bool
     */
    private function formatAndSetField($model, $data)
    {
        $data = $this->formatField($data);
        foreach ($data as $k => $v) {
            $model->setField($k, $v);
        }
        return true;
    }

    /**
     * 预处理表单数据
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function formatField($data)
    {
        $ret = [];
        foreach ($data as $k => $v) {
            if (($tmpV = FKRongFangChanXinXiModel::parseStringToField($k, $v)) === false) {
                throw new \Exception('表单值不合法', 90001);
            }
            $ret[$k] = $tmpV;
        }

        return $ret;
    }

    /**
     * 导入功能开发
     **/
    
    protected $location;
    protected $title=['kehubianhao', 'kehu','fangchanquyu','fangchandizhi','chanquanren',
                       'mianji', 'fangchanleixing', 'shifouxuweihu','shifoudiya','pingguzhiwanyuan',
                      'pinggushijian', 'yinhangdiyae', 'yinhangshengyue', 'jiekuane', 'diyalv',
                      'jiekuandaoqishijian', 'chandiaochaxunshijian', 'xiacichaxunshijian', 'beizhu'];  
    
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

                $arr[10]=\Prj\Misc\FengKongImport::checktime($arr[10],$arr[0]);
                $arr[15]=\Prj\Misc\FengKongImport::checktime($arr[15],$arr[0]);
                $arr[16]=\Prj\Misc\FengKongImport::checktime($arr[16],$arr[0]);
                $arr[17]=\Prj\Misc\FengKongImport::checktime($arr[17],$arr[0]);
                

                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],FengKongEnum::getInstance()->get('fangchanquyu'));
                $arr[6]=\Prj\Misc\FengKongImport::transvk($arr[6],['无', '住宅', '大住宅', '商铺', '办公楼',]);
                $arr[7]=\Prj\Misc\FengKongImport::transvk($arr[7],['无', '是', '否',]);
                $arr[8]=\Prj\Misc\FengKongImport::transvk($arr[8],['无', '是', '否',]);

                $result[]=[
                    'kehubianhao'=>$arr[0],
                    'kehu'=>$arr[1],
                    'fangchanquyu'=>$arr[2],
                    'fangchandizhi'=>$arr[3],
                    'chanquanren'=>$arr[4],
                    'mianji'=>$arr[5],
                    'fangchanleixing'=>$arr[6],
                    'shifouxuweihu'=>$arr[7],
                    'shifoudiya'=>$arr[8],
                    'pingguzhiwanyuan'=>$arr[9],
                    'pinggushijian'=>$arr[10],
                    'yinhangdiyae'=>$arr[11],
                    'yinhangshengyue'=>$arr[12],
                    'jiekuane'=>$arr[13],
                    'diyalv'=>$arr[14],
                    'jiekuandaoqishijian'=>$arr[15],
                    'chandiaochaxunshijian'=>$arr[16],
                    'xiacichaxunshijian'=>$arr[17],
                    'beizhu'=>$arr[18],
                ];
    
            }
      
            foreach ($result as $data){
                 try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
  
                    $tmpModel = FKRongFangChanXinXiModel::getCopy('');
                    $ret = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                    $this->_view->assign('_id', $ret);
                } catch (\Exception $e) {
                    if ($e->getCode() >= 90000) {
                        $this->returnError($e->getMessage());
                        return 0;
                    } else {
                        $this->returnError('导入失败');
                        return 0;
                    }
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
                case 'pinggushijian':
                  if(!empty($v)){
                  $datetime=explode('-',$v);
                  if(empty($datetime[2])){
                      $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                      throw new \Exception($name.'日期格式不正确', 90003);
                  }else{
                      $v=$v ? strtotime($v) : 0;
                    }
                  }
                  $ret[$k]=$v;
                  break;
                case 'jiekuandaoqishijian':
                    if(!empty($v)){
                    $datetime=explode('-',$v);
                    if(empty($datetime[2])){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'日期格式不正确', 90003);
                    }else{
                      $v=$v ? strtotime($v) : 0;
                    }
                    }
                    $ret[$k]=$v;
                    break;
                case 'chandiaochaxunshijian':
                    if(!empty($v)){
                    $datetime=explode('-',$v);
                    if(empty($datetime[2])){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'日期格式不正确', 90003);
                    }else{
                      $v=$v ? strtotime($v) : 0;
                    }
                    }
                    $ret[$k]=$v;
                    break;
                case 'xiacichaxunshijian':
                    if(!empty($v)){
                    $datetime=explode('-',$v);
                    if(empty($datetime[2])){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'日期格式不正确', 90003);
                    }else{
                      $v=$v ? strtotime($v) : 0;
                    }
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangchanquyu':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangchanleixing':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'shifouxuweihu':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'shifoudiya':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKRongFangChanXinXiModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
