<?php
namespace Prj\Misc;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/11
 * Time: 11:34
 */
class ViewFK {
    /**
     * @var array
     *  'key1' => [
     *      'name' => '字段1',
     *      'mark' => 'b101',
     *      'width' => 200,
     *      'type' => 'text',
     *      'options' => [
     *      'k1' => '选项1',
     *      'k2' => '选项1',
     *      ],
     *  ],
     */
    public $_header = []; //表头
    public static $_request;
    protected $_data = []; //数据集
    /**
     * @var array
     * 'id'=>'tab1',
     * 'name'=>'哈哈1',
     * 'api'=>'',
     * 'key'=>'key1',
     * 'value'=>'key1'
     */
    protected $_tab = []; //自标签页
    protected $_pk; //主键名称
    protected $_pkShow; //是否展示主键
    public $pid; //唯一码
    protected $_api = [ //更新删除接口
        'update' => '',
        'delete' => ''
    ];
    protected $_pager; //分页对象
    protected $_search = false; //是否展示搜索框
    protected $_tableId = 'pagerForm'; //表html id
    protected static $_self = null; //自身
    protected $_frm = null;
    protected $_searchExport = false;
    protected $_where = [];
    protected $otBtn = [];
    public static $_showUpdate = 1;
    public static $_showDelete = 1;
    protected static $that; //一个实例
    protected $searchOT; //搜索框额外按钮

    protected $operate = true;
    protected $replePerfix = '';

    public static function getInstance(){
        if(!self::$that)self::$that = new self;
        return self::$that;
    }

    public function  __construct(){
        $this->_frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $this->pid = mt_rand(1000,9999);
        self::$_self = $this;
    }

    public function addSearch($key , $title ,$type = 'text', $value = ''){
        if(!$this->_search)$this->_search = true;
        $this->_frm->addItem($key, \Sooh\Base\Form\Item::factory($title, $value, $type));
        return $this;
    }

    public function addSearchOT($html){
        $this->searchOT .= ' '.$html;
        return $this;
    }

    public function getSearch(){
        $this->_frm->fillValues();
        return ($this->_frm->flgIsThisForm) ? $this->_frm->getWhere() : [];
    }

    public static function output(){
        self::$_self->show();
    }



    public function setData($rs){
        $this->_data = $rs;
        return $this;
    }

    public function setOperate($bool = true) {
        $this->operate = $bool;
        return $this;
    }

    public function setReple($perfix = '')
    {
        $this->replePerfix = $perfix;
        return $this;
    }

    /**
     * @param bool $searchExport 是否展示导出按钮
     * @param array $where
     * @return $this
     */
    public function showSearch($searchExport = true , $where = []){
        $this->_searchExport = $searchExport;
        $this->_search = true;
        $this->_where = $where;
        return $this;
    }

    public function setAction($update , $delete){
        $this->_api = [
            'update' => $update,
            'delete' => $delete
        ];
        return $this;
    }

    public function setPk($key , $show = true){
        $this->_pk = $key;
        $this->_pkShow = $show;
        return $this;
    }

    public function setPager(\Sooh\DB\Pager $pager){
        $this->_pager = $pager;
        $this->_frm->addItem('pageId', $pager->pageid())
            ->addItem('pageSize', $pager->page_size);
        return $this;
    }

    public function getRow() {
        return $this->_header;
    }

    /**
     * 添加一个子页签
     * @param $id
     * @param $name
     * @param $api
     * @param $key
     * @param $value
     * @return $this
     */
    public function addTab($id , $name ,  $api , $key , $value){
        $tmp = [
            'name'=>$name,
            'id'=>$id,
            'api'=>$api,
            'key'=>$key,
            'value'=>$value,
        ];
        $this->_tab[] = $tmp;
        return $this;
    }

    /**
     * 添加一列表头
     * @param $key 字段
     * @param $name 字段名
     * @param $mark 类名
     * @param string $type
     * @param array $options
     * @param int $width
     * @param array $setting
     * @return $this
     */
    public function addRow($key , $name , $mark = null , $type = 'text' , $options = [] , $width = 150 , $setting = []){
        $tmp = [
            'name' => $name,
            'mark' => $mark,
            'type' => $type,
            'options' => $options,
            'width' => $width,
            'setting' => $setting,
        ];
        $this->_header[$key] = $tmp;
        return $this;
    }

    public function addBtn($html){
        $this->otBtn[] = $html;
    }

    /**
     * 表单类型解析
     * @param $key
     * @return string
     */
    protected function _input($key){
        $str = '';
        $type = $this->_header[$key]['type'];
        $options = $this->_header[$key]['options'];
        $mark = $this->_header[$key]['mark'];
        $id = $this->_header[$key]['mark'];
        if($this->_header[$key]['setting']['readOnly']){
            $readOnly = 'readonly="readonly"';
        }
        switch ($type) {
            case 'text' :
                return '<input '.$readOnly.' data-id="'.$key.'" style="color:inherit" onblur="map(this)" onchange="map(this)" class="'.$mark.'" data-mark="'.$id.'" type="text" name="values[#index#]['.$key.']" placeholder="-">';
            case 'checkbox' :
                foreach($options as $k=>$v){
                    $str.='<input type="checkbox" class="'.$mark.'" name="values[#index#]['.$key.']" id="doc-test-a2-1[#index#]" data-toggle="icheck" value="'.$k.'" data-label="'.$v.'">';
                }
                return $str;
            case 'select' :
                $str = '<select onchange="map(this)" onclick="map(this)" class="'.$mark.'" name="values[#index#]['.$key.']" data-toggle="selectpicker">';
                foreach($options as $k=>$v){
                    $str.='<option value="'.$k.'">'.$v.'</option>';
                }
                $str.= '</select>';
                return $str;
            case 'datepicker' :
                $str = '<input '.$readOnly.' style="color:inherit" onblur="map(this)" onchange="map(this)" class="'.$mark.'" data-mark="'.$id.'" type="text" name="values[#index#]['.$key.']" data-toggle="datepicker">';
                return $str;
            case 'selects' :
                $str = '<select data-width="inherit" name="values[#index#]['.$key.'][]" data-toggle="selectpicker" multiple>';
                foreach($options as $k=>$v){
                    if(is_array($v)){
                        $style = $v['style'];
                        $icon = $v['icon'] ?('data-icon="'.$v['icon'].'"'):'';
                        $str.='<option style="'.$style.'" '.$icon.'  value="'.$k.'">'.$v['value'].'</option>';
                    }else{
                        $str.='<option value="'.$k.'">'.$v.'</option>';
                    }
                }
                $str.= '</select>';
                return $str;
            case 'textarea':
                $str = '<textarea name="values[#index#]['.$key.']" data-height="autoheight" rows="5" style="height:auto"></textarea>';
                return $str;
            default : return '';
        }
    }


    /**
     * Html-页签
     * @return string
     */
    protected function _tabHtml(){
        $li = '';
        $tab = '';
        if(empty($this->_tab))return '';
        foreach($this->_tab as $k=>$v){
            $active = $k==0 ? $active = 'active' : $active = '';
            $li.='<li class="'.$active.'"><a href="#'.$v['id'].'" role="tab" data-toggle="tab">'.$v['name'].'</a></li>';
            $tab.='<div style="max-height:230px" class="tab-pane fade in '.$active.'" id="'.$v['id'].'">没有内容</div>';
        }

        $html = <<<html
<ul class="nav nav-tabs" role="tablist">
    $li
</ul>
<div class="tab-content">
    $tab
</div>
html;
        return $html;
    }

    protected $_width = 0;

    /**
     * Html-表头内容
     * @param bool $type
     * @return string
     */
    protected function _headHtml($type = false){
        $deleteApi = $this->_api['delete'];
        $headStr = '';
        $width = 0;
        $num = 0;//计数
        //拼表头
        foreach($this->_header as $k=>$v){
            if(!$this->_pkShow){
                if($k == $this->_pk)continue;
            }
            $class = $num < 2 ? "floatBtn" : "";
            $tmp = ' <th class="'.$class.'" title="'.$v['name'].'" width="'.$v['width'].'">';
            $con = $type ? $v['name'] : $this->_input($k);
            $tmp.=$con;
            $tmp.='</th>';
            $headStr.=$tmp;
            $width+=$v['width'];
            $num ++;
        }
        $this->_width = $width;
        $btn = $type ? '' : '<a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>&nbsp<a href="'.$deleteApi.'" class="btn btn-red row-del">删</a>';
        $headStr .= '<th title="" data-addtool="true" width="100">'.($this->table_type=='normal' ? '操作' : '').
            $btn.
            '</th>';
        return $headStr;
    }

    protected function _headPerfHtml($type = false){
        $deleteApi = $this->_api['delete'];
        $headStr = '';
        $width = 0;
        $num = 0;//计数
        //拼表头
        foreach($this->_header as $k=>$v){
            if(!$this->_pkShow){
                if($k == $this->_pk)continue;
            }
            $tmp = ' <th title="'.$v['name'].'" width="'.$v['width'].'">';
            $con = $type ? $v['name'] : $this->_input($k);
            $tmp.=$con;
            $tmp.='</th>';

            $headStr.=$tmp;
            $width+=$v['width'];
            $num ++;
        }
        $this->_width = $width;
        if ($this->operate) {
            $btn = $type ? '' : '<a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>&nbsp<a href="'.$deleteApi.'" class="btn btn-red row-del">删</a>';
            $headStr .= '<th title="" data-addtool="true" width="100">'.($this->table_type=='normal' ? '操作' : ''). $btn. '</th>';
        }
        return $headStr;
    }

    /**
     * Html-列表数据
     * @return string
     */
    protected function _dataHtml(){
        $deleteApi = $this->_api['delete'];
        $dataStr = '';
        //拼内容
        //var_log($this->_data,'this->data>>>');
        foreach($this->_data as $k=>$v){
            $idVal = is_array($v[$this->_pk]) ? $v[$this->_pk]['value'] : $v[$this->_pk];
            $tmp = '<tr class="xxx floatTr" data-id="'.$idVal.'">';
            $num = 0;//计数
            foreach($this->_header as $key=>$arr){
                if(!$this->_pkShow){
                    if($key == $this->_pk)continue;
                }
                $value = (is_array($v[$key]) && $v[$key]['value']) ? $v[$key]['value'] : $v[$key];
                $style = (is_array($v[$key]) && $v[$key]['style']) ? $v[$key]['style'] : '';
                $title = $value;

                if(is_array($value)){
                    $value = implode(',',$value);
                }else{
                    if($arr['options'])$title = $arr['options'][$value];
                }
                $class = $num < 2 ? "floatBtn" : "";
                $tmp.='<td class="'.$class.'" style="'.$style.'" data-key="'.$key.'" data-val="'.$value.'" title="'.$title.'">'.$value.'</td>';
                $num++;
            }
            $otBtn = '';
            foreach ($this->otBtn as $v){
                $v = str_replace('{$id}',$idVal,$v);
                $v = str_replace(urlencode('{$id}'),$idVal,$v);
                $otBtn .= ("&nbsp".$v);
            }
            //var_log($otBtn,'btn>>>');
            $saveBtn = '<a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>';
            $delBtn = '<a href="'.$deleteApi.'&_id='.$idVal.'" date-toggle="doajax" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>';
            if(!self::$_showUpdate)$saveBtn = '';
            if(!self::$_showDelete)$delBtn = '';
            $tmp.='<td data-noedit="true">'.
                '<span class="floatBtn">'.
                $otBtn."&nbsp".
                $saveBtn.
                '&nbsp'.
                $delBtn.
                '</td>';
            $tmp.='</tr>';
            $dataStr.=$tmp;
        }
        return $dataStr;
    }

    protected function _dataPerfHtml($mulitLine = false){
        $deleteApi = $this->_api['delete'];
        $dataStr = '';
        //拼内容
        foreach($this->_data as $k=>$v){
            $idVal = is_array($v[$this->_pk]) ? $v[$this->_pk]['value'] : $v[$this->_pk];
            $tmp = '<tr class="xxx floatTr" data-id="'.$idVal.'">';
            $num = 0;//计数
            foreach($this->_header as $key=>$arr){
                if(!$this->_pkShow){
                    if($key == $this->_pk)continue;
                }
                $value = (is_array($v[$key]) && $v[$key]['value']) ? $v[$key]['value'] : $v[$key];
                $style = (is_array($v[$key]) && $v[$key]['style']) ? $v[$key]['style'] : '';
                $title = $value;

                if(is_array($value)){
                    $value = implode(',',$value);
                    $title = $value;
                }else{
                    if($arr['options'])$title = $arr['options'][$value];
                }
                $class = $num < 2 ? "floatBtn" : "";
                if ($mulitLine && $this->_header[$key]['type'] == 'textarea') {
                    $value = '<pre>' . $value . '</pre>';
                    $title = $value;
                }
                $tmp.='<td class="'.$class.'" style="'.$style.'" data-key="'.$key.'" data-val="'.$value.'" title="'.$title.'">'.$title.'</td>';
                $num++;
            }
            $otBtn = '';
            foreach ($this->otBtn as $v){
                $v = str_replace('{$id}',$idVal,$v);
                $v = str_replace(urlencode('{$id}'),$idVal,$v);
                $otBtn .= ("&nbsp".$v);
            }
            if ($this->operate) {
                $saveBtn = '<a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>';
                $delBtn = '<a href="'.$deleteApi.'&_id='.$idVal.'" date-toggle="doajax" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>';
                $replyBtn = '<input type="checkbox" name="' . $this->replePerfix . 'checkbox" value="' . $idVal . '" />';
                if(!self::$_showUpdate)$saveBtn = '';
                if(!self::$_showDelete)$delBtn = '';
                if ($this->replePerfix == '') $replyBtn = '';
                $tmp.='<td data-noedit="true">'.
                    '<span class="floatBtn">'.
                    $otBtn."&nbsp".
                    $saveBtn.
                    '&nbsp'.
                    $delBtn
                    .'&nbsp' . $replyBtn .
                    '</td>';
                $tmp.='</tr>';
            }
            $dataStr.=$tmp;
        }
        return $dataStr;
    }
    /**
     * Html-JS代码
     * @return string
     */
    protected function _jsHtml(){
        $pid = $this->pid;
        $script = '';
        foreach($this->_tab as $k=>$v){
            isset($k);
            $script.="tab_show('".$v['key']."',$(this).find(\"[data-key='".$v['value']."']\").attr('data-val'),'".$v['api']."','#".$v['id']."');";
        }
        $script = $_GET['_type'] == 'ajax' ? '' : $script;
        $str = <<<html
<script>
    $(function(){
        var pid = '$pid';
        var tab_show = function(key , value , api , target){
            //console.log(value);
            var url = api+'&'+key+'='+value+'&_type=ajax&pageSize=100';
            $(document).bjuiajax('doLoad', {
                target : target,
                url : url
            });
        }
        $(document).off('click','.table tr');
        $(document).on('click','.table-'+pid+' tbody tr',function(){
            if(!$(this).hasClass('loaded')){
                $script
            }
            $('.table tr').removeClass('loaded');
            $(this).addClass('loaded');
        });

        //初始化表格位置
        setTimeout(function(){
            $('table.viewFK').each(function(){
                $(this).height($(this).parent().height() - $(this).position().top);
                $(this).find('td[data-val]').each(function(){
                    var td = this;
                    $(td).find('span.filter-option').css('color',$(td).css('color'));
                });
            });
        },100);
    });
</script>
html;
        return $str;
    }

    protected function _searchHtml(){
        $export = $this->_searchExport;
        if(!$this->_search){return '<form data-toggle="ajaxsearch" id="pagerForm" ></form>';}
        $tmp = '';
        $renderer = new \Prj\Misc\FormRenderer;
        $tmp .= $renderer->formForExport(\Sooh\Base\Tools::uri([],'index'),$this->pid);
        $form = \Sooh\Base\Form\Broker::getCopy('default');
        $tmp .= $renderer->getSearchStandard(
            $form,
            $this->searchOT.
            ($export? $renderer->getBatchArea(null, \Sooh\Base\Tools::uri(array('__EXCEL__'=>1,'where'=>$this->_where)), null , false):null)
        );
        return $tmp;
    }

    /**
     * 展示可编辑表格
     */
    public function show(){
        $updateApi = $this->_api['update'];
        $pid = $this->pid;
        $headStr = $this->_headHtml();
        $dataStr = $this->_dataHtml();
        $scriptStr = $this->_jsHtml();
        if($_GET['_type'] != 'ajax'){
            $tabHtml = $this->_tabHtml();
            $page = '<div class="bjui-pageFooter" style="">'.\Prj\Misc\View::pagerStandard($this->_pager).'</div>';
            $js = '';
            $search = $this->_searchHtml();
            $tableStyle = 'overflow: auto;height:93%;';
            $mainTable = 'main-table';
            $subTable = 'sub-table';
        }
        $topHeight = $this->_tab ? '70%' : '100%';
        $tableType = 'tabledit';
        echo <<<html
        $js
<style>
    .viewFK .selectpicker.btn{
        height: auto;
    }
    .viewFK .selectpicker.btn span{
        white-space : normal;
    }
    .viewFK #main-table .floatTr .floatBtn{
		position:relative;
		right:0;
		top:0;
	}
	.loaded td{
	    background-color: #fffadd !important;
	}
	td *{
	    text-align: center;
	}
</style>
<table style="height: 100%;width: 100%;overflow: auto;table-layout:fixed" class="viewFK">
    <tr style="height: $topHeight;">
        <td style="vertical-align: top;position: relative" id="$mainTable">
            <div class="bjui-pageHeader">
                $search
            </div>
            <div style='$tableStyle' class="zhuyaoCon">
                <form  data-toggle="validate" method="post" data-id="{$this->_tableId}">
                    <table style="width: {$this->_width}px;min-width: 100%" class="table table-bordered table-hover table-striped table-top table-$pid" data-toggle="$tableType" data-initnum="0" data-action="$updateApi" data-single-noindex="true">
                        <thead>
                        <tr data-idname="{$this->_pk}" class="floatTr">
                            $headStr
                        </tr>
                        </thead>
                        <tbody>
                            $dataStr
                        </tbody>
                    </table>
                </form>
            </div>
            $page
        </td>
    </tr>
    <tr>
         <td style="vertical-align: top" id="$subTable">
            $tabHtml
        </td>
    </tr>
</table>
$scriptStr
html;
    }

    public function showPerf()
    {
        $updateApi = $this->_api['update'];
        $pid = $this->pid;
        $headStr = $this->_headPerfHtml();
        $dataStr = $this->_dataPerfHtml();
        $scriptStr = $this->_jsHtml();
        if($_GET['_type'] != 'ajax'){
            $tabHtml = $this->_tabHtml();
            $js = '';
            $search = $this->_searchHtml();
            $tableStyle = 'overflow: auto;height:93%;';
            $mainTable = 'main-table';
            $subTable = 'sub-table';
        }
        $topHeight = $this->_tab ? '70%' : '100%';
        $tableType = 'tabledit';
        return <<<html
        $js
<form  data-toggle="validate" method="post" data-id="{$this->_tableId}">
                    <table style="width: {$this->_width}px;min-width: 100%" class="table table-bordered table-hover table-striped table-top table-$pid" data-toggle="$tableType" data-initnum="0" data-action="$updateApi" data-single-noindex="true">
                        <thead>
                        <tr data-idname="{$this->_pk}" class="floatTr">
                            $headStr
                        </tr>
                        </thead>
                        <tbody>
                            $dataStr
                        </tbody>
                    </table>
                </form>
$scriptStr
html;
    }

    /**
     * array格式输出
     * @return array
     */
    public function toArray(){
        return [
            'pager'=>$this->_pager->toArray(),
            'data'=>$this->_data,
            'header'=>$this->_header
        ];
    }

    /**
     * 导出excel数据格式化
     * 把查出的记录集传进来,然后输出excel头和格式化后的rs
     * @param $records
     * @return array
     */
    public function toExcel($records){
        $rs = [];
        $head = [];

        foreach($this->_header as $k=>$v){
            if($k == $this->_pk && !$this->_pkShow)continue;
            $head[] = $v['name'];
        }

        foreach($records as $v){
            $tmp = [];
            array_walk($this->_header,function($vv , $kk) use (&$tmp , $v){
                if($kk == $this->_pk && !$this->_pkShow)return;
                if(is_array($v[$kk]) && isset($v[$kk]['value'])){
                    $value = $v[$kk]['value'];
                }else{
                    $value = $v[$kk];
                }
                if($vv['options']){
                    if(is_array($value)){
                        $arr = [];
                        foreach($value as $v){
                            $arr[] = $vv['options'][$v];
                        }
                        unset($value);
                        $value = implode(',',$arr);
                    }else{
                        $value = $vv['options'][$value];
                    }
                }
                $tmp[$kk] = $value;
            });
            $rs[] = $tmp;
        }

        return ['header'=>$head,'records'=>$rs];
    }

    public static function checkRight($rights , $rightPre){
        if(!in_array($rightPre.'_update',$rights))self::$_showUpdate = 0;
        if(!in_array($rightPre.'_delete',$rights) && !in_array($rightPre.'_del',$rights))self::$_showDelete = 0;
    }

    protected $table_type;
    /**
     * 输出传统表格
     * @return string
     */
    public function normalTable(){
        $this->table_type = 'normal';
        $head = $this->_headHtml(true);
        $html = "<table  class=\"table table-bordered table-hover table-striped\" style='width: ".$this->_width."px;min-width: 100%'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= $head;
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        $html .= $this->_dataHtml();
        $html .= "</tbody></table>";
        return $html;
    }

    /**
     * 输出传统表格
     * @return string
     */
    public function normalPerfTable(){
        $head = $this->_headHtml(true);
        $html = "<table  class=\"table table-bordered table-hover table-striped\" style='width: ".$this->_width."px;min-width: 100%'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= $head;
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        $html .= $this->_dataPerfHtml(true);
        $html .= "</tbody></table>";
        return $html;
    }

    public function searchHtml(){
        return $this->_searchHtml();
    }

    public function hideDefaultBtn(){
        self::$_showDelete = false;
        self::$_showUpdate = false;
        return $this;
    }

    protected static $ppid;
    public static $userId;

    public static function log($msg){
        if(!self::$ppid)self::$ppid = mt_rand(1000,9999);
        $userId = self::$userId ? '>>>userId:'.self::$userId : '';
        error_log('['.self::$ppid.']'.$userId.'#msg:'.$msg);
    }
}