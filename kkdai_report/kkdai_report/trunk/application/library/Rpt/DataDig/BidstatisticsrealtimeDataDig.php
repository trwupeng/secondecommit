<?php
/**
 * 投标统计(实时)
 * 避免了15年4月份之前的老数据, 5月份过渡期的不太准, 数据在15年6月份及之后才是最准的
 *
 * 月报和日报包包含体验金, 不包含流标产品已经确认
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/11/23 0023
 * Time: 下午 5:18
 */
namespace Rpt\DataDig;
class BidstatisticsrealtimeDataDig extends \Rpt\DataDig\RealtimeDataDigBase {

    public $tableHeaderForMonth = [
        'ym'=>['交易日期'],
        'amount_succ_super'=>['超级用户成功投资金额(元)', null, 'amount'],
        'amount_succ_normal'=>['非超级用户成功投资金额(元)',null, 'amount'],
        'total'=>['合计(元)', null, 'amount']
    ];

    private function buildWhereForMonth ($where) {
        $result = ['where_ttz'=>'','where_dq'=>''];
        if(!empty($where['ymFrom'])) {
            !empty($where_ttz) && $where_ttz .= ' and ';
            $where_ttz .= 'create_date >=\''.$where['ymFrom'].'-01 00:00:00\'';
            !empty($where_dq) && $where_dq .= ' and ';
            $where_dq .= 'create_time >=\''.$where['ymFrom'].'-01 00:00:00\'';
        }

        if(!empty($where['ymTo'])) {
            !empty($where_ttz) && $where_ttz .= ' and ';
            $where_ttz .= 'create_date <=\''.$where['ymTo'].'-31 23:59:59\'';
            !empty($where_dq) && $where_dq .= ' and ';
            $where_dq .= 'create_time <=\''.$where['ymTo'].'-31 23:59:59\'';
        }

        !empty($where_ttz) && $where_ttz .= ' and ';
        $where_ttz .= 'type = 1 and `status` in (1,4)';
        !empty($where_dq) && $where_dq.= ' and ';
        $where_dq .= 'poi_type=0 and poi_status in (601,603, 610)'
                .' and (bid_type!=501 or bid_type is null)';

        $result['where_ttz'] = $where_ttz;
        $result['where_dq'] = $where_dq;
        return $result;
    }

    /**
     *
     *  月报
     * @param null $where
     * @param null $pager
     * @param bool|false $isDownload 是否下载
     * @param bool|false $grabData 最后抓取记录标识
     * @return array
     */
    public function dataForMonth ($where=null, $pager=null, $isDownload=false, $grabData = false) {
        $records = [];
        // 以where转换为条件
        $result = $this->buildWhereForMonth($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        // 抓当前页的记录
        if ($grabData) {
            // 获取数据
            $sql = $this->buildSql(
                'sum(amount) as amount, ym , customer.flag', // fields
                'DATE_FORMAT(create_date,\'%Y-%m\')as ym, amount, yuebao_poi.customer_id', // fields_ttz
                $where_ttz,
                'DATE_FORMAT(create_time,\'%Y-%m\') as ym, amount, bid_poi.customer_id', // fields_dq
                $where_dq,
                'left join customer using (customer_id)',
                '',
                'group by ym, flag',
                'order by ym desc',
                $pager
            );
            $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
            foreach($result as $r) {
                if (!isset($records[$r['ym']])) {
                    $records[$r['ym']] =
                        array_fill_keys(array_keys($this->tableHeaderForMonth), '');
                    $records[$r['ym']]['ym'] = $r['ym'];
                    !$isDownload && $records[$r['ym']]['__pkey__'] = $r['ym'];
                }
                if ($r['flag'] == 1) {
                    $records[$r['ym']]['amount_succ_super'] += $r['amount']*0.01;
                }else {
                    $records[$r['ym']]['amount_succ_normal'] += $r['amount']*0.01;
                }
                $records[$r['ym']]['total'] += $r['amount']*0.01;
            }
            foreach($records as $k => $r) {
               foreach($this->tableHeaderForMonth as $key=> $v) {
                    if (isset($v[2]) && $v[2] == 'amount'){
                        !empty($r[$key])
                            &&$records[$k][$key] = number_format($r[$key],2);
                    }
                }
            }
//var_log($records, 'records########');
            return $records;
        }else {
            // 查询出总数据记录数目
            $sql = $this->buildSql(
                'count(distinct(ym)) as totalCount', // fields
                'DATE_FORMAT(create_date,\'%Y-%m\')as ym', // fields_ttz
                $where_ttz,
                'DATE_FORMAT(create_time,\'%Y-%m\') as ym', // fields_dq
                $where_dq
            );

            $total_count = \Rpt\Funcs::execSql($sql,$this->db_produce);


            if(empty($total_count)) {
                return records;
            }
            $total_count = $total_count[0]['totalCount'];
            if(!empty($pager)) {
                $pager->init($total_count, -1);
                // 获取当前分页的月份
                $sql = $this->buildSql(
                    'distinct(ym) as ym', // fields
                    'DATE_FORMAT(create_date,\'%Y-%m\')as ym', // fields_ttz
                    $where_ttz,
                    'DATE_FORMAT(create_time,\'%Y-%m\') as ym', // fields_dq
                    $where_dq,
                    '',
                    '',
                    '',
                    'order by ym desc',
                    $pager
                );
                $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
                $arr_ym=[];
                foreach ($result as $r) {
                    $arr_ym[] = $r['ym'];
                }
                $where['ymFrom'] = min($arr_ym);
                $where['ymTo'] = max($arr_ym);
                $records = $this->dataForMonth($where, null, $isDownload, true);
            }else {
                $pager = new \Sooh\DB\Pager(2000, '', false);
                $pager->init($total_count, -1);
                $page_count = $pager->page_count;
                for ($pageid = 1; $pageid <= $page_count; $pageid++) {
                    $pager->init(-1, $pageid);
                    $sql = $this->buildSql(
                        'distinct(ym) as ym', // fields
                        'DATE_FORMAT(create_date,\'%Y-%m\')as ym', // fields_ttz
                        $where_ttz,
                        'DATE_FORMAT(create_time,\'%Y-%m\') as ym', // fields_dq
                        $where_dq,
                        '',
                        '',
                        '',
                        'order by ym desc',
                        $pager
                    );
                    $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
                    $arr_ym=[];
                    foreach ($result as $r) {
                        $arr_ym[] = $r['ym'];
                    }
                    $where['ymFrom'] = min($arr_ym);
                    $where['ymTo'] = max($arr_ym);
                    $ret = $this->dataForMonth($where, null, $isDownload, true);
                    $records = array_merge($records, $ret);
                }
            }
        }
//var_log($records, 'records########');
        return $records;
    }

    /**
     * 生成sql
     * @param string $fields  最终要查询的字段
     * @param string $fields_ttz union联表中天天赚的字段
     * @param string $where_ttz union联表中天天赚的查询条件
     * @param string $fields_dq  union联表中定期产品的字段
     * @param string $where_dq union链表中定期产品的查询条件
     * @param string $join join联表语句
     * @param string $joinwhere 最终的where条件
     * @param string $groupby 最终的分组条件
     * @param string $orderby 最终的排序
     * @param null $pager 分页用的pager类
     * @return string 返回sql语句
     */
    private function buildSql ($fields='*', $fields_ttz='*', $where_ttz='', $fields_dq='*', $where_dq='', $join='', $joinwhere='', $groupby='', $orderby='', $pager=null) {

        $sql = 'select '.$fields.' from ('
            .'('
            .'  select '.$fields_ttz
	        .'  from yuebao_poi';
        !empty($where_ttz) && $sql .= ' where '.$where_ttz;
        $sql.=')'
            .'union all'
            .'('
            .'  select '.$fields_dq
	        .'  from bid_poi';
        !empty($where_dq) && $sql.= ' where '.$where_dq;
        $sql.=')'
            .')tb';
            !empty($join) && $sql.=' '.$join;
            !empty($joinwhere) && $sql.=' where '.$joinwhere;
            !empty($groupby) && $sql.=' '.$groupby;
            !empty($orderby) && $sql.= ' '.$orderby;
            $pager !=null
                && $sql.=' limit '.$pager->rsFrom().','.$pager->page_size;
//var_log($sql, 'sql#########');
        return $sql;
    }

    /**
     * 月报合计信息
     * @param $where
     * @return mixed
     */
    public function summaryForMonth ($where) {

        $tip = '合计:'
            . '&nbsp;超级用户成功投资<span style="color:red;">{sum_succ_super}</span>元,'
            .'&nbsp;非超级用户投资成功<span style="color:red;">{sum_succ_normal}</span>元,'
            .'&nbsp;总成功投资<span style="color:red;">{sum_succ_total}</span>元';
        $rule = '规则:&nbsp;不包含体验标, 不包含流标, 投资金额是实际用户投资定期产品的金额与实际用户投资天天赚的金额之和';
        $records = array_fill_keys(
            ['{sum_succ_super}', '{sum_succ_normal}', '{sum_succ_total}'],
            0
        );
        $result = $this->buildWhereForMonth($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        $sql = $this->buildSql(
            'sum(amount) as amount,customer.flag',
            'customer_id, amount',
            $where_ttz,
            'customer_id, amount',
            $where_dq,
            'left join customer using (customer_id)',
            '',
            'group by flag'
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            if ($r['flag'] == 1) {
                $records['{sum_succ_super}'] += $r['amount']*0.01;
            }else {
                $records['{sum_succ_normal}'] += $r['amount']*0.01;
            }
            $records['{sum_succ_total}'] += $r['amount']*0.01;
        }
        foreach ($records as $k => $v) {
            $records[$k] = number_format($v, 2);
        }
        $tip = str_replace(array_keys($records), $records, $tip);
        $tip .= '<br />'.$rule;
        return $tip;
    }


    /**
     * 日报表格头部标题
     * @var array
     */
    public $tableHeaderForDay = [
        'ymd'                   =>['日期', 100],
        'amount_succ_normal'    =>['非超级用户成功投标金额(元)', null, 'amount'],
        'count_succ_normal'     =>['非超级用户成功投标笔数'],
        'amount_fail_normal'    =>['非超级用户投资失败金额(元)', null, 'amount'],
        'count_fail_normal'     =>['非超级用户投资失败笔数',],
        'amount_succ_super'     =>['超级用户成功投标金额(元)', null, 'amount'],
        'count_succ_super'      =>['超级用户成功投标笔数'],
        'amount_fail_super'     =>['超级用户投资失败金额(元)', null, 'amount'],
        'count_fail_super'      =>['超级用户投标失败笔数'],
        'sumSuccAmount'         =>['合计成功金额(元)', null, 'amount'],
        'sumSuccCount'          =>['合计成功笔数'],
        'sumFailedAmount'       =>['合计失败金额(元)', null, 'amount'],
        'sumFailedCount'        =>['合计失败笔数'],
    ];


    public function dataForDay ($where=null, $pager=null, $isDownload=false) {
        $records = [];
        $result = $this->buildWhereForDay($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        // 通过查询条件获取总记录数
        $sql = $this->buildSql(
            'count(distinct(ymd)) as totalCount',
            'distinct((DATE_FORMAT(create_date, \'%Y-%m-%d\')))AS ymd ',
            $where_ttz,
            'distinct((DATE_FORMAT(create_time, \'%Y-%m-%d\')))AS ymd',
            $where_dq
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if(empty($result)) {
            return $records;
        }
        $totalCount = $result[0]['totalCount'];
        if(!empty($pager)) {
            $pager->init($totalCount, -1);
            $records = $this->curPageDataForDay ($where, $pager, $isDownload);
        }else {
            $pager = new \Sooh\DB\Pager(2000, '', false);
            $pager->init($totalCount, -1);
            for ($pageid=1; $pageid <= $pager->page_count; $pageid++) {
                $pager->init(-1, $pageid);
                $ret = $this->curPageDataForDay ($where, $pager, $isDownload);
                $records = array_merge($records, $ret);
            }
        }
        return $records;
    }

    private function buildWhereForDay($where) {
        $where_ttz = $where_dq = '';
        if(!empty($where['ymdFrom'])){
            !empty($where_ttz) && $where_ttz .= ' and ';
            $where_ttz .= 'create_date >=\''.$where['ymdFrom'].' 00:00:00\'';
            !empty($where_dq) && $where_dq .= ' and ';
            $where_dq .= 'create_time >=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])){
            !empty($where_ttz) && $where_ttz .= ' and ';
            $where_ttz .= 'create_date<=\''.$where['ymdTo'].' 23:59:59\'';
            !empty($where_dq) && $where_dq .= ' and ';
            $where_dq .= 'create_time<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        !empty($where_ttz) && $where_ttz.= ' and ';
        $where_ttz .= 'type=1 and `status`>0';
        !empty($where_dq) && $where_dq .= ' and ';
        $where_dq .= ' poi_status in (601,602,603,606,607,608,610) and poi_type=0'
                .' and (bid_type !=501 or bid_type is null)'
                .' and bid_id not in (select bid_id from bid where bid_status=4011)'; // 包含失败的, 产品要求包含体验金, 不包含流标
        return ['where_ttz'=>$where_ttz, 'where_dq'=>$where_dq];
    }

    private function curPageDataForDay($where, $pager, $isDownload=false) {
        $result = $this->buildWhereForDay($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        // 当前分页的日期范围
        $sql = $this->buildSql(
            'distinct(ymd) as ymd',
            'distinct((DATE_FORMAT(create_date, \'%Y-%m-%d\')))AS ymd ',
            $where_ttz,
            'distinct((DATE_FORMAT(create_time, \'%Y-%m-%d\')))AS ymd',
            $where_dq,
            '',
            '',
            'group by ymd',
            'order by ymd desc',
            $pager
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $arr_ymd[] = $r['ymd'];
        }
        $ymdMin = min($arr_ymd);
        $ymdMax = max($arr_ymd);
        // 根据日期范围获取数据
        $result = $this->buildWhereForDay(['ymdFrom'=>$ymdMin, 'ymdTo'=>$ymdMax]);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        $sql = $this->buildSql(
            'sum(amount) as amount, count(*) as count, ymd, customer.flag, poi_status',
            'customer_id, amount, (DATE_FORMAT(create_date, \'%Y-%m-%d\')) AS ymd,
                yuebao_poi.`status` as poi_status',
            $where_ttz,
            'customer_id, amount, (DATE_FORMAT(create_time, \'%Y-%m-%d\')) AS ymd,
                poi_status',
            $where_dq,
            'left join customer using(customer_id)',
            '',
            'group by ymd, flag, poi_status',
            'order by ymd desc',
            null
        );
        return $this->transferDataForDay($sql, $isDownload);

    }
    /**
     * 日数据的数据转换
     * @param $sql
     * @param bool|false $isDownload
     * @return array
     */
    private function transferDataForDay ($sql, $isDownload=false) {
        $records = [];
            $result = \Rpt\Funcs::execSql($sql,$this->db_produce);
        if(empty($result)) {
            return $records;
        }
//var_log($result, 'records########');
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDay), '');
            foreach($result as $r) {
                !isset($records[$r['ymd']]) && $records[$r['ymd']] = $tmp;
                if(!$isDownload){
                    !isset($records[$r['ymd']]['__pkey__'])
                        && $records[$r['ymd']]['__pkey__']=$r['ymd'];
                }
                $records[$r['ymd']]['ymd'] = $r['ymd'];
                if($r['flag']==='1')  {
                    if(in_array($r['poi_status'], [1,4, 601, 603, 610])){
                        $records[$r['ymd']]['amount_succ_super']+=$r['amount']*0.01;
                        $records[$r['ymd']]['count_succ_super']+=$r['count'];
                        $records[$r['ymd']]['sumSuccAmount']+=$r['amount']*0.01;
                        $records[$r['ymd']]['sumSuccCount'] += $r['count'];
                    }else {
                        $records[$r['ymd']]['amount_fail_super']+=$r['amount']*0.01;
                        $records[$r['ymd']]['count_fail_super']+=$r['count'];
                        $records[$r['ymd']]['sumFailedAmount'] += $r['amount']*0.01;
                        $records[$r['ymd']]['sumFailedCount'] += $r['count'];
                    }

                }else {
                    if (in_array($r['poi_status'], [1,4, 601, 603, 610])){
                        $records[$r['ymd']]['amount_succ_normal']+=$r['amount']*0.01;
                        $records[$r['ymd']]['count_succ_normal']+=$r['count'];
                        $records[$r['ymd']]['sumSuccAmount']+=$r['amount']*0.01;
                        $records[$r['ymd']]['sumSuccCount'] += $r['count'];
                    }else {
                        $records[$r['ymd']]['amount_fail_normal']+=$r['amount']*0.01;
                        $records[$r['ymd']]['count_fail_normal']+=$r['count'];
                        $records[$r['ymd']]['sumFailedAmount'] += $r['amount']*0.01;
                        $records[$r['ymd']]['sumFailedCount'] += $r['count'];
                    }
                }
            }
        foreach ($records as $k => $r) {
            foreach ($this->tableHeaderForDay as $key => $v) {
                if (isset($v[2]) && $v[2] == 'amount' && !empty($r[$key])){
                    $records[$k][$key] = number_format($r[$key], 2);
                }
            }
        }
        return $records;
    }

    /**
     * 日报表页面展示的合计信息
     * @param $ymdFrom
     * @param $ymdTo
     * @return mixed|string
     */
    public function summaryForDay ($where) {
        $tip ='合计: '
            .'超级用户成功投资金额<span style="color:red">{sum_super_amount}</span>元,'
            .'非超级用户成功投资金额<span style="color:red">{sum_normal_amount}</span>元,'
            .'投资总金额<span style="color:red">{total}</span>元';
        $result = $this->buildWhereForDay($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        $sql = $this->buildSql(
            'sum(amount) as amount, customer.flag, poi_status',
            'customer_id, amount, (DATE_FORMAT(create_date, \'%Y-%m-%d\')) AS ymd, yuebao_poi.`status` as poi_status ',
            $where_ttz,
            'customer_id, amount, (DATE_FORMAT(create_time, \'%Y-%m-%d\')) AS ymd, poi_status',
            $where_dq,
            'left join customer using (customer_id)',
            '',
            'group by flag, poi_status'
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $tmp = array_fill_keys(
            ['{sum_super_amount}', '{sum_normal_amount}', '{total}'],
            0
        );
        if(empty($result)){
            $tip= str_replace(
                array_keys($tmp),
                '0',
                $tip
            );
        }else {
            foreach($result as $r) {
                if(in_array($r['poi_status'], [1,4, 601, 603, 610])) {
                    if ($r['flag'] === '1') {
                        $tmp['{sum_super_amount}'] += $r['amount']*0.01;
                    }else {
                        $tmp['{sum_normal_amount}'] += $r['amount']*0.01;
                    }
                }

            }
            $tmp['{total}']= $tmp['{sum_normal_amount}'] + $tmp['{sum_super_amount}'];
            foreach ($tmp as $k => $v) {
                $tmp[$k] = number_format($v, 2);
            }
            $tip = str_replace(
                array_keys($tmp),
                $tmp,
                $tip
            );
        }
        $tip .= '<br />'.'规则: 不包含体验标, 不包含流标, 投资金额是实际用户投资定期产品的金额与实际用户投资天天赚的金额之和';
        return $tip;
    }

    /**
     * 用户投标详情表格头部
     * @var array
     */
    public $tableHeaderForDetail = [
        'poi_id'                =>['订单号', 200],
        'customer_id'           =>['用户ID', 200],
        'customer_realname'     =>['姓名', 60],
        'customer_cellphone'    =>['手机号'],
        'create_time'           => ['操作时间',160],
        'pay_amount'            =>['实际投资金额(元)', null, 'amount'],
        'redpacket'             =>['使用红包(元)', null, 'amount'],
        'source'                =>['推广渠道'],
        'clientType'            =>['投标渠道'],
        'poi_status'            =>['投标状态'],
        'bid_title'             => ['标的名称'],
        'product_type'          =>['标的类型'],
        'bid_amount'            =>['标的总额(元)', null, 'amount'],
        'bid_period'            =>['标的期限'],
        'bid_interest'          =>['标的利率(%)'],
        'bid_status'            =>['标的状态'],
    ];

    public function dataForDetail ($where=null, $pager=null) {
        $records = [];
        $result = $this->buildWhereForDetail($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        $joinwhere = $result['where_join'];
        // 获取总记录数目
        $sql = $this->sqlForDetail(
            'count(*) as totalCount',
            'yuebao_poi.customer_id',
            $where_ttz,
            'bid_poi.customer_id',
            $where_dq,
            'left join customer using(customer_id)',
            $joinwhere
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $totalCount = $result[0]['totalCount'] -0;
        if($totalCount ==0) {
            return $records;
        };

        // 获取数据
        if($pager!==null) {
            $pager->init($totalCount, -1);
            $records = $this-> curPageDataForDetail($where, $pager);
        }else {
            $pager = new \Sooh\DB\Pager(2000, null, false);
            $pager->init($totalCount, -1);
            for($pageid =1; $pageid <= $pager->page_count; $pageid++){
                $pager->init(-1, $pageid);
                $ret=  $this->curPageDataForDetail($where, $pager, true);
                $records = array_merge($records, $ret);
            }
        }
        return $records;
    }

    private function curPageDataForDetail ($where, $pager, $isDownlaod=false) {
        $result = $this->buildWhereForDetail($where);
        $where_ttz = $result['where_ttz'];
        $where_dq = $result['where_dq'];
        $joinwhere = $result['where_join'];

        $fields = 'poi_id, customer_id, customer_realname,
                customer_cellphone, create_time, amount, source,
                download_source, clientType, poi_status, bid_title,
                product_type, bid_amount, bid_period,  bid_interest,
                bid_status,coupon_id,customer.flag, _tmp_product_type_';
        $fields_ttz= 'poi_id, yuebao_poi.customer_id,
                yuebao_poi.create_date as create_time, yuebao_poi.amount,
			    \'\' as clientType,  yuebao_poi.`status` as poi_status,
			    title as bid_title, 0 as product_type,
			    yuebao.amount as bid_amount,
			    0 as bid_period, yuebao.interest as bid_interest,
			    yuebao.`status` as bid_status, \'\' as coupon_id,
			    \'ttz\' as _tmp_product_type_ ';
        $fields_dq = 'poi_id, bid_poi.customer_id,
                bid_poi.create_time as create_time, bid_poi.amount,
			    bid_poi.channel as clientType, bid_poi.poi_status,
			    bid.bid_title, bid.product_type, bid.bid_amount,
			    bid.bid_period, bid.bid_interest, bid.bid_status, coupon_id,
			    \'dq\' as _tmp_product_type_';

            $sql = $this->sqlForDetail(
                $fields,
                $fields_ttz,
                $where_ttz,
                $fields_dq,
                $where_dq,
                'left join customer using (customer_id)',
                $joinwhere,
                '',
                'order by create_time desc',
                $pager
            );
            $sqlForRedPacket = $this->sqlForDetail(
                'coupon_id',
                $fields_ttz,
                $where_ttz,
                $fields_dq,
                $where_dq,
                'left join customer using (customer_id)',
                $joinwhere,
                '',
                'order by create_time desc',
                $pager
            );
        return $this->transferDataForDetail($sql, $sqlForRedPacket);
    }

    private function buildWhereForDetail ($where) {
        $where_ttz = $where_dq = $joinwhere = '';
        if(!empty($where['ymdFrom'])){
            $where_ttz .= ' and yuebao_poi.create_date >=\''.$where['ymdFrom'].' 00:00:00\'';
            $where_dq .= ' and bid_poi.create_time >=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])){
            $where_ttz .= ' and yuebao_poi.create_date<=\''.$where['ymdTo'].' 23:59:59\'';
            $where_dq .= ' and bid_poi.create_time<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        if(!empty($where['customer_id'])) {
            $where_ttz .= ' and yuebao_poi.customer_id=\''.$where['customer_id'].'\'';
            $where_dq .= ' and bid_poi.customer_id=\''.$where['customer_id'].'\'';
        }
        if(!empty($where['bid_id'])) {
            $where_ttz .= ' and yuebao_id=\''.$where['bid_id'].'\'';
            $where_dq .= ' and bid_id=\''.$where['bid_id'].'\'';
        }
        if(!empty($where['bid_title'])) {
            $where_ttz .= ' and yuebao.title like \'%'.$where['bid_title'].'%\'';
            $where_dq .= ' and bid.bid_title like \'%'.$where['bid_title'].'%\'';
        }
        if(!empty($where['customer_cellphone'])){
            !empty($joinwhere)&& $joinwhere .= ' and ';
            $joinwhere .= ' customer_cellphone ='.$where['customer_cellphone'];
        }

        if(!empty($where['customer_realname'])) {
            !empty($joinwhere) && $joinwhere .= ' and ';
            $joinwhere .= ' customer_realname like \'%'.$where['customer_realname'].'%\'';
        }

        return [
            'where_ttz' => $where_ttz,
            'where_dq' => $where_dq,
            'where_join' => $joinwhere
        ];
    }
    /**
     * 投资详情数据转换
     * @param $sql
     * @param $sqlRedPaketInfo
     * @return array
     */
    private function transferDataForDetail ($sql, $sqlRedPaketInfo) {
        $records = [];
        $result = \Rpt\Funcs::execSql($sql,$this->db_produce);
        if(empty($result)){
            return $records;
        }

        $arrRedpaketId = [];
        $result_redpaket = \Rpt\Funcs::execSql($sqlRedPaketInfo, $this->db_produce);
        foreach($result_redpaket as $r) {
            !empty($r['coupon_id']) && $arrRedpaketId[] = $r['coupon_id'];
        }
        $result_redpaket=null;
        if(!empty($arrRedpaketId)) {
            $arrRedpaketInfo = $this->db_produce->getAssoc(
                'phoenix.customer_coupon',
                'id',
                'type,status,amount',
                ['id'=>$arrRedpaketId]
            );
            unset($arrRedpaketId);
        }
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDetail), '');
            $tmp['poi_id'] = $r['poi_id'];
            $tmp['customer_id'] = $r['customer_id'];
            $tmp['customer_realname'] = $r['customer_realname'];
            $tmp['customer_cellphone'] = $r['customer_cellphone'];
            $tmp['create_time'] = $r['create_time'];

            $tmp['clientType'] = $r['clientType']; // 投标客户端 要不要做了呢
            $tmp['product_type'] = \Rpt\Funcs::product_type($r['product_type']);
            $tmp['bid_title'] = $r['bid_title'];
            !empty($r['bid_amount'])
                && $tmp['bid_amount'] = $r['bid_amount']*0.01;
            !empty($r['bid_interest'])
                && $tmp['bid_interest'] = $r['bid_interest']*0.01;
            // 实际投资金额 和红包使用金额
            if($r['coupon_id']!=='' && isset($arrRedpaketInfo[$r['coupon_id']])){
                $redpacketAmount = $arrRedpaketInfo[$r['coupon_id']]['amount'];
                if($arrRedpaketInfo[$r['coupon_id']]['type'] == 2) {
                    $tmp['pay_amount'] = $r['amount'];
                    // 区分房宝宝定期宝加息计算
                    if($r['product_type'] == 1){
                        $tmp['redpacket'] =
                            round($redpacketAmount*0.0001*$r['amount']/12*$r['bid_period'],2);
                    }elseif($r['product_type'] ==2) {
                        $tmp['redpacket']
                            = round($redpacketAmount*0.00001*$r['amount']/372*$r['bid_period'],2);
                    }else { //目前只知道有定期宝和房宝宝的加息计算方式, 标的表里不存在的标或者其他的直接输出百分比
                        $tmp['redpacket']
                            = '<span style="color:red">'.$redpacketAmount*0.01.'%</span>';
                    }
                }else {
                    $tmp['pay_amount'] =  ($r['amount']-$redpacketAmount)*0.01;
                    $tmp['redpacket'] = $redpacketAmount*0.01;
                }
            }else {
                $tmp['pay_amount'] = $r['amount']*0.01;
            }

            // 订单状态
            if($r['_tmp_product_type_'] == 'dq') {
                $tmp['poi_status']
                    =\Rpt\KkdStatus::returnCodeName(
                    'poi_status_of_bid_poi', $r['poi_status']
                );
                $tmp['bid_status'] = \Rpt\KkdStatus::returnCodeName(
                    'bid_status_of_bid', $r['bid_status']
                );
                if($r['product_type'] !=null){
                    if($r['product_type'] == 1) {
                        $tmp['bid_period'] = $r['bid_period'].'月';
                    }else{
                        $tmp['bid_period'] = $r['bid_period'].'天';
                    }
                }
            }else {
                $tmp['poi_status'] =\Rpt\KkdStatus::returnCodeName(
                    'status_of_yuebao_poi', $r['poi_status']
                );
            }

            if ($r['flag'] > 0) {
                $tmp['source'] = \Rpt\Funcs::customerFlag($r['flag']);
            }else {
                !empty($r['source']) && $tmp['source'] = $r['source'];
                !empty($r['download_source'])
                    && $tmp['source'] = $r['download_source'];
                if(empty($tmp['source'])){
                    $tmp['source']=\Rpt\Funcs::customerFlag($r['flag']);
                }
            }

            $records[] = $tmp;
        }
        foreach ($records as $k => $r) {
            foreach ($this->tableHeaderForDetail as $key => $v) {
                if (isset($v[2]) && $v[2] == 'amount' && !empty($r[$key])){
                    $records[$k][$key] = number_format($r[$key],2);
                }
            }
        }
        return $records;

    }
    private function sqlForDetail ($fields, $fields_ttz, $where_ttz, $fields_dq, $where_dq, $join='', $joinwhere='', $groupby='', $orderby='', $pager=null) {
        $sql = 'select '. $fields
            .' from'
            .'('
            .'	select '.$fields_ttz
            .'	from yuebao_poi'
            .'	left join yuebao using (yuebao_id)'
            .'	where yuebao_poi.type = 1'
            .' '.$where_ttz
            .'	UNION ALL'
            .'	select '.$fields_dq
            .'	from bid_poi'
            .'	left join bid using (bid_id)'
            .'	where poi_type=0'
            .' and bid_poi.poi_status in (601,602,603,606,607,608,610)' // 避免了15年4月份之前的老数据. 数据在15年6月份及之后才是最准的
            .'  and (bid.bid_status!=4011 or bid.bid_status is null)'
            .' '.$where_dq
            .')tb';
        !empty($join) && $sql.=' '.$join;
        !empty($joinwhere) && $sql.=' where '.$joinwhere;
        !empty($groupby) && $sql.=' '.$groupby;
        !empty($orderby) && $sql.=' '.$orderby;
        $pager !=null && $sql.=' limit '.$pager->rsFrom().','.$pager->page_size;
        return $sql;
    }

    public function summaryForDetail () {
        $tip = '规则: 包含体验标, 不包含流标, 包含真实用户投资天天赚的投标记录';
        return $tip;
    }

}