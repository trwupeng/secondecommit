<?php
/**
 *
 * 固定的取目前这几种券的数据. sql好写 .
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1 0001
 * Time: 21:53
 */
namespace Rpt\DataDig;
class VoucherrealtimeDataDig extends \Rpt\DataDig\RealtimeDataDigBase {

    private function buildSql ($fields='*', $where='', $join='', $order='', $pager=null) {
        $sql = 'select '.$fields.' from customer_coupon';
        !empty($join) && $sql.=' '.$join;
        !empty($where) && $sql.=' where'.' '.$where;
        !empty($order) && $sql.= ' '.$order;
        !empty($pager) && $sql.=' limit '.$pager->rsFrom().','.$pager->page_size;
        return $sql;
    }

    public $tableHeaderForMonthHandedout = [
        'ym'            => ['月份'],
        'type1amount'   => ['抵现券发放金额合计(元)', null, 'amount'],
        'type4amount'   => ['提现券发放金额合计(元)', null, 'amount'],
        'type2count'    => ['加息券发放数量合计(个)'],
        'type3amount'   => ['返现券发放金额合计(元)', null, 'amount'],
    ];

    private function buildWhereForMonthHandedout ($where) {
        $queryWhere = '';
        if (!empty($where['ymFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date>=\''.$where['ymFrom'].'-01 00:00:00\'';
        }
        if(!empty($where['ymTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date<=\''.$where['ymTo'].'-31 23:59:59\'';
        }
        return $queryWhere;
    }

    /**
     * 月发放数据
     * @param  array $where
     * @param null|object $pager
     * @param bool|false $isDownload
     */
    public function dataForMonthHandedout ($where=[], $pager=null, $isDownload=false) {
        $queryWhere=$this->buildWhereForMonthHandedout($where);
        $records = [];
        $sql = $this->buildSql(
            'count(distinct(date_format(create_date, \'%Y-%m\'))) as totalCount',
            $queryWhere
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if(empty($result)) {
            return $records;
        }
        $totalCount = $result[0]['totalCount'];
        if (!empty($pager)) {
            $pager->init($totalCount, -1);
        }
        $fields ='DATE_FORMAT(create_date,\'%Y-%m\') as ym,'
                .'sum(case when type=1 then amount*0.01 end) as type1amount,'
                .'sum(case when type=3 then amount*0.01 end) as type3amount,'
                .'count(case when type=2 then type end) as type2count,'
                .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $join='';
        $order = 'group by ym order by ym desc';

        $sql = $this->buildSql($fields, $queryWhere, $join, $order, $pager);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForMonthHandedout), '');
            $tmp['ym'] = $r['ym'];
            !empty($r['type1amount'])
                && $tmp['type1amount']=number_format($r['type1amount'], 2);
            !empty($r['type3amount'])
                && $tmp['type3amount']=number_format($r['type3amount'], 2);
            !empty($r['type2count'])
                && $tmp['type2count'] = $r['type2count'];
            !empty($r['type4amount'])
                && $tmp['type4amount']=number_format($r['type4amount'], 2);
            !$isDownload && $tmp['__pkey__'] =$r['ym'];
            $records[] = $tmp;
        }
        return $records;

    }

    public function summaryForMonthHandedout($where) {
        $queryWhere=$this->buildWhereForMonthHandedout($where);
        $tip = '合计:'
            .'发放抵现券<span style="color:red;">{sum_dixianquan}</span>元,'
            .'发放提现券<span style="color:red;">{sum_tixianquan}</span>元,'
            .'发放加息券<span style="color:red;">{count_jiaxiquan}</span>个,'
            .'发放返现券<span style="color:red;">{sum_fanxianquan}</span>元';
        $temp = array_fill_keys(
            ['{sum_dixianquan}','{sum_tixianquan}','{count_jiaxiquan}','{sum_fanxianquan}'],
            0
        );
        $fields = 'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $sql = $this->buildSql($fields,$queryWhere);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $result = $result[0];
        !empty($result['type1amount'])
            && $temp['{sum_dixianquan}']=number_format($result['type1amount'],2);
        !empty($result['type4amount'])
            && $temp['{sum_tixianquan}']=number_format($result['type4amount'],2);
        !empty($result['type2count'])
            && $temp['{count_jiaxiquan}']=$result['type2count'];
        !empty($result['type3amount'])
            && $temp['{sum_fanxianquan}']=number_format($result['type3amount'],2);

        $tip = str_replace(array_keys($temp), $temp, $tip);
        return $tip;
    }

    /**
     * 日报的数据
     * @param array $where
     * @param null $pager
     * @param bool|false $isDownload
     * @return array
     */
    public function dataForDayHandedout ($where=[], $pager=null, $isDownload=false) {
        $queryWhere=$this->buildWhereForDayHandedout($where);
        $records = [];
        $sql = $this->buildSql(
            'count(distinct(date_format(create_date, \'%Y-%m-%d\'))) as totalCount',
            $queryWhere
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if(empty($result)) {
            return $records;
        }
        $totalCount = $result[0]['totalCount'];
        if (!empty($pager)) {
            $pager->init($totalCount, -1);
        }
        $fields ='DATE_FORMAT(create_date,\'%Y-%m-%d\') as ymd,'
            .'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $join='';
        $order = 'group by ymd order by ymd desc';

        $sql = $this->buildSql($fields, $queryWhere, $join, $order, $pager);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDayHandedout), '');
            $tmp['ymd'] = $r['ymd'];
            !empty($r['type1amount'])
            && $tmp['type1amount']=number_format($r['type1amount'], 2);
            !empty($r['type3amount'])
            && $tmp['type3amount']=number_format($r['type3amount'], 2);
            !empty($r['type2count'])
            && $tmp['type2count'] = $r['type2count'];
            !empty($r['type4amount'])
            && $tmp['type4amount']=number_format($r['type4amount'], 2);
            !$isDownload && $tmp['__pkey__'] =$r['ymd'];
            $records[] = $tmp;
        }
        return $records;
    }
    private function buildWhereForDayHandedout($where) {
        $queryWhere = '';
        if (!empty($where['ymdFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date>=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        return $queryWhere;
    }

    public $tableHeaderForDayHandedout = [
        'ymd'            => ['日期'],
        'type1amount'   => ['抵现券发放金额合计(元)', null, 'amount'],
        'type4amount'   => ['提现券发放金额合计(元)', null, 'amount'],
        'type2count'    => ['加息券发放数量合计(个)'],
        'type3amount'   => ['返现券发放金额合计(元)', null, 'amount'],
    ];

    public function summaryForDayHandedout ($where){
        $queryWhere=$this->buildWhereForDayHandedout($where);
        $tip = '合计:'
            .'发放抵现券<span style="color:red;">{sum_dixianquan}</span>元,'
            .'发放提现券<span style="color:red;">{sum_tixianquan}</span>元,'
            .'发放加息券<span style="color:red;">{count_jiaxiquan}</span>个,'
            .'发放返现券<span style="color:red;">{sum_fanxianquan}</span>元';
        $temp = array_fill_keys(
            ['{sum_dixianquan}','{sum_tixianquan}','{count_jiaxiquan}','{sum_fanxianquan}'],
            0
        );
        $fields = 'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $sql = $this->buildSql($fields,$queryWhere);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $result = $result[0];
        !empty($result['type1amount'])
        && $temp['{sum_dixianquan}']=number_format($result['type1amount'],2);
        !empty($result['type4amount'])
        && $temp['{sum_tixianquan}']=number_format($result['type4amount'],2);
        !empty($result['type2count'])
        && $temp['{count_jiaxiquan}']=$result['type2count'];
        !empty($result['type3amount'])
        && $temp['{sum_fanxianquan}']=number_format($result['type3amount'],2);

        $tip = str_replace(array_keys($temp), $temp, $tip);
        return $tip;
    }

    public $tableHeaderForDetailHandedout = [
        'customer_id' => ['用户编号'],
        'customer_name' => ['用户昵称'],
        'customer_realname' => ['真实姓名'],
        'customer_cellphone' => ['手机号'],
        'create_date' => ['赠送时间'],
        'type' => ['券类型'],
        'amount' => ['赠送金额(元)/加息(元)'],
    ];

    private function buildSqlForDetail ($fields='*', $where='', $join='', $order='', $pager=null) {
        $sql = 'select '.$fields.' from customer_coupon';
        !empty($join) && $sql.=' '.$join;
        !empty($where) && $sql.= ' where '.$where;
        !empty($order) && $sql.= ' '.$order;
        !empty($pager) && $sql.= ' limit '.$pager->rsFrom().','.$pager->page_size;
        return $sql;
    }

    private function buildWhereForDetailHandedout ($where) {
        $queryWhere = '';
        if (!empty($where['ymdFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date>=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'create_date<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        if (!empty($where['customer_realname'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer.customer_realname like \'%'.$where['customer_realname'].'%\'';
        }
        if(!empty($where['customer_cellphone'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer.customer_cellphone ='.$where['customer_cellphone'];
        }
        if(!empty($where['type'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer_coupon.type='.$where['type'];
        }

        return $queryWhere;
    }


    public function dataForDetailHandedout ($where=[], $pager=null) {
        $where = $this->buildWhereForDetailHandedout($where);
        $records = [];
        $sql = $this->buildSqlForDetail(
            'count(*) as totalCount',
            $where,
            'left join customer using (customer_id)'
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if (empty($result)) {
            return $records;
        }
        $totalCount  = $result[0]['totalCount'];
        !empty($pager) && $pager->init($totalCount, -1);

        $fields = 'customer_id,customer_name,customer_realname,
            customer_cellphone,customer_coupon.create_date,
            customer_coupon.type,(customer_coupon.amount*0.01) as amount';

        $sql = $this->buildSqlForDetail(
            $fields,
            $where,
            'left join customer using (customer_id)',
            'order by create_date desc',
            $pager
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);

        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDetailHandedout), '');
            $tmp['customer_id'] = $r['customer_id'];
            $tmp['customer_name'] = $r['customer_name'];
            $tmp['customer_realname'] = $r['customer_realname'];
            $tmp['customer_cellphone'] = $r['customer_cellphone'];
            $tmp['create_date'] = $r['create_date'];
            if (isset(\Rpt\Funcs::$voucherEnum[$r['type']])) {
                $tmp['type'] = \Rpt\Funcs::$voucherEnum[$r['type']];
            }else {
                $tmp['type'] = $r['type'];
            }

            if ($r['type'] != 2) {
                $tmp['amount'] = number_format($r['amount'], 2);
            }else {
                $tmp['amount']= $r['type'].'%';
            }

            $records[] = $tmp;
        }
        return $records;
    }

    public $tableHeaderForMonthUse = [
        'ym'            => ['月份'],
        'type1amount'   => ['抵现券使用金额合计(元)', null, 'amount'],
        'type4amount'   => ['提现券使用金额合计(元)', null, 'amount'],
        'type2count'    => ['加息券使用数量合计(个)'],
        'type3amount'   => ['返现券使用金额合计(元)', null, 'amount'],
    ];

    private function buildWhereForMonthUse ($where) {
        $queryWhere = '';
        if (!empty($where['ymFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date>=\''.$where['ymFrom'].'-01 00:00:00\'';
        }
        if(!empty($where['ymTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date<=\''.$where['ymTo'].'-31 23:59:59\'';
        }

        !empty($queryWhere) && $queryWhere .= ' and ';
        $queryWhere .= ' status=1 and handle_date is not null';
        return $queryWhere;
    }

    /**
     * 月发放数据
     * @param  array $where
     * @param null|object $pager
     * @param bool|false $isDownload
     */
    public function dataForMonthUse ($where=[], $pager=null, $isDownload=false) {
        $queryWhere=$this->buildWhereForMonthUse($where);
        $records = [];
        $sql = $this->buildSql(
            'count(distinct(date_format(handle_date, \'%Y-%m\'))) as totalCount',
            $queryWhere
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if(empty($result)) {
            return $records;
        }
        $totalCount = $result[0]['totalCount'];
        if (!empty($pager)) {
            $pager->init($totalCount, -1);
        }
        $fields ='DATE_FORMAT(handle_date,\'%Y-%m\') as ym,'
            .'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $join='';
        $order = 'group by ym order by ym desc';

        $sql = $this->buildSql($fields, $queryWhere, $join, $order, $pager);
var_log($sql, 'sql###');
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForMonthUse), '');
            $tmp['ym'] = $r['ym'];
            !empty($r['type1amount'])
            && $tmp['type1amount']=number_format($r['type1amount'], 2);
            !empty($r['type3amount'])
            && $tmp['type3amount']=number_format($r['type3amount'], 2);
            !empty($r['type2count'])
            && $tmp['type2count'] = $r['type2count'];
            !empty($r['type4amount'])
            && $tmp['type4amount']=number_format($r['type4amount'], 2);
            !$isDownload && $tmp['__pkey__'] =$r['ym'];
            $records[] = $tmp;
        }
        return $records;

    }

    public function summaryForMonthUse($where) {
        $queryWhere=$this->buildWhereForMonthUse($where);
        $tip = '合计:'
            .'使用抵现券<span style="color:red;">{sum_dixianquan}</span>元,'
            .'使用提现券<span style="color:red;">{sum_tixianquan}</span>元,'
            .'使用加息券<span style="color:red;">{count_jiaxiquan}</span>个,'
            .'使用返现券<span style="color:red;">{sum_fanxianquan}</span>元';
        $temp = array_fill_keys(
            ['{sum_dixianquan}','{sum_tixianquan}','{count_jiaxiquan}','{sum_fanxianquan}'],
            0
        );
        $fields = 'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $sql = $this->buildSql($fields,$queryWhere);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $result = $result[0];
        !empty($result['type1amount'])
        && $temp['{sum_dixianquan}']=number_format($result['type1amount'],2);
        !empty($result['type4amount'])
        && $temp['{sum_tixianquan}']=number_format($result['type4amount'],2);
        !empty($result['type2count'])
        && $temp['{count_jiaxiquan}']=$result['type2count'];
        !empty($result['type3amount'])
        && $temp['{sum_fanxianquan}']=number_format($result['type3amount'],2);

        $tip = str_replace(array_keys($temp), $temp, $tip);
        return $tip;
    }


    private function buildWhereForDayUse($where) {
        $queryWhere = '';
        if (!empty($where['ymdFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date>=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        !empty($queryWhere) && $queryWhere .= ' and ';
        $queryWhere .= ' status=1 and handle_date is not null';
        return $queryWhere;
    }

    public $tableHeaderForDayUse = [
        'ymd'            => ['日期'],
        'type1amount'   => ['抵现券使用金额合计(元)', null, 'amount'],
        'type4amount'   => ['提现券使用金额合计(元)', null, 'amount'],
        'type2count'    => ['加息券使用数量合计(个)'],
        'type3amount'   => ['返现券使用金额合计(元)', null, 'amount'],
    ];

    public function summaryForDayUse ($where){
        $queryWhere=$this->buildWhereForDayUse($where);
        $tip = '合计:'
            .'使用抵现券<span style="color:red;">{sum_dixianquan}</span>元,'
            .'使用提现券<span style="color:red;">{sum_tixianquan}</span>元,'
            .'使用加息券<span style="color:red;">{count_jiaxiquan}</span>个,'
            .'使用返现券<span style="color:red;">{sum_fanxianquan}</span>元';
        $temp = array_fill_keys(
            ['{sum_dixianquan}','{sum_tixianquan}','{count_jiaxiquan}','{sum_fanxianquan}'],
            0
        );
        $fields = 'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $sql = $this->buildSql($fields,$queryWhere);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        $result = $result[0];
        !empty($result['type1amount'])
        && $temp['{sum_dixianquan}']=number_format($result['type1amount'],2);
        !empty($result['type4amount'])
        && $temp['{sum_tixianquan}']=number_format($result['type4amount'],2);
        !empty($result['type2count'])
        && $temp['{count_jiaxiquan}']=$result['type2count'];
        !empty($result['type3amount'])
        && $temp['{sum_fanxianquan}']=number_format($result['type3amount'],2);

        $tip = str_replace(array_keys($temp), $temp, $tip);
        return $tip;
    }

    public function dataForDayUse ($where=[], $pager=null, $isDownload=false) {
        $queryWhere=$this->buildWhereForDayUse($where);
        $records = [];
        $sql = $this->buildSql(
            'count(distinct(date_format(handle_date, \'%Y-%m-%d\'))) as totalCount',
            $queryWhere
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if(empty($result)) {
            return $records;
        }
        $totalCount = $result[0]['totalCount'];
        if (!empty($pager)) {
            $pager->init($totalCount, -1);
        }
        $fields ='DATE_FORMAT(handle_date,\'%Y-%m-%d\') as ymd,'
            .'sum(case when type=1 then amount*0.01 end) as type1amount,'
            .'sum(case when type=3 then amount*0.01 end) as type3amount,'
            .'count(case when type=2 then type end) as type2count,'
            .'sum(case when type=4 then amount*0.01 end) as type4amount';
        $join='';
        $order = 'group by ymd order by ymd desc';

        $sql = $this->buildSql($fields, $queryWhere, $join, $order, $pager);
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDayUse), '');
            $tmp['ymd'] = $r['ymd'];
            !empty($r['type1amount'])
            && $tmp['type1amount']=number_format($r['type1amount'], 2);
            !empty($r['type3amount'])
            && $tmp['type3amount']=number_format($r['type3amount'], 2);
            !empty($r['type2count'])
            && $tmp['type2count'] = $r['type2count'];
            !empty($r['type4amount'])
            && $tmp['type4amount']=number_format($r['type4amount'], 2);
            !$isDownload && $tmp['__pkey__'] =$r['ymd'];
            $records[] = $tmp;
        }
        return $records;
    }




    public $tableHeaderForDetailUse = [
        'customer_id' => ['用户编号'],
        'customer_name' => ['用户昵称'],
        'customer_realname' => ['真实姓名'],
        'customer_cellphone' => ['手机号'],
        'handle_date' => ['使用时间'],
        'type' => ['券类型'],
        'amount' => ['使用金额(元)'],
        'bid_title' => ['标的名称'],
    ];

//    private function buildSqlForDetail ($fields='*', $where='', $join='', $order='', $pager=null) {
//        $sql = 'select '.$fields.' from customer_coupon';
//        !empty($join) && $sql.=' '.$join;
//        !empty($where) && $sql.= ' where '.$where;
//        !empty($order) && $sql.= ' '.$order;
//        !empty($pager) && $sql.= ' limit '.$pager->rsFrom().','.$pager->page_size;
//        return $sql;
//    }

    private function buildWhereForDetailUse ($where) {
        $queryWhere = '';
        if (!empty($where['ymdFrom'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date>=\''.$where['ymdFrom'].' 00:00:00\'';
        }
        if(!empty($where['ymdTo'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'handle_date<=\''.$where['ymdTo'].' 23:59:59\'';
        }
        if (!empty($where['customer_realname'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer.customer_realname like \'%'.$where['customer_realname'].'%\'';
        }
        if(!empty($where['customer_cellphone'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer.customer_cellphone ='.$where['customer_cellphone'];
        }
        if(!empty($where['type'])) {
            !empty($queryWhere) && $queryWhere .= ' and ';
            $queryWhere .= 'customer_coupon.type='.$where['type'];
        }

        !empty($queryWhere) && $queryWhere .= ' and ';
        $queryWhere .= ' status=1 and handle_date is not null';

        return $queryWhere;
    }


    public function dataForDetailUse ($where=[], $pager=null, $isDownload=false) {
        $where = $this->buildWhereForDetailUse($where);
        $records = [];
        $sql = $this->buildSqlForDetail(
            'count(*) as totalCount',
            $where,
            'left join customer using (customer_id)'
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        if (empty($result)) {
            return $records;
        }
        $totalCount  = $result[0]['totalCount'];
        !empty($pager) && $pager->init($totalCount, -1);


        // 获取标的名称
        $fields = 'poi_id';
        $sql = $this->buildSqlForDetail(
            $fields,
            $where,
            '',
            'order by handle_date desc',
            $pager
        );
        $arr_poi_id = \Rpt\Funcs::execSql($sql,$this->db_produce);
        foreach($arr_poi_id as $k => $v) {
            $arr_poi_id[$k] = $v['poi_id'];
        }
        $arr_bid=[];
        $arr_poi=[];
        if (!empty($arr_poi_id)) {
            $arr_poi = $this->db_produce->getAssoc('phoenix.bid_poi','poi_id','bid_id, amount', ['poi_id'=>$arr_poi_id]);

            foreach($arr_poi as $r) {
                if(!in_array($r['bid_id'], $arr_bid)){
                    $arr_bid[] = $r['bid_id'];
                }
            }
        }
        if (!empty($arr_bid)) {
            $arr_bid = $this->db_produce->getAssoc('phoenix.bid', 'bid_id', 'bid_title,product_type,bid_period', ['bid_id'=>$arr_bid]);
        }
        $fields = 'customer_id,customer_name,customer_realname,
            customer_cellphone,customer_coupon.handle_date,
            customer_coupon.type,(customer_coupon.amount*0.01) as amount,
            customer_coupon.poi_id';

        $sql = $this->buildSqlForDetail(
            $fields,
            $where,
            'left join customer using (customer_id)',
            'order by handle_date desc',
            $pager
        );
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);

        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDetailUse), '');
            $tmp['customer_id'] = $r['customer_id'];
            $tmp['customer_name'] = $r['customer_name'];
            $tmp['customer_realname'] = $r['customer_realname'];
            $tmp['customer_cellphone'] = $r['customer_cellphone'];
            $tmp['handle_date'] = $r['handle_date'];
            if (isset(\Rpt\Funcs::$voucherEnum[$r['type']])) {
                $tmp['type'] = \Rpt\Funcs::$voucherEnum[$r['type']];
            }else {
                $tmp['type'] = '<span style="color:red">'.$r['type'].'</span>';
            }
//error_log($tmp['customer_realname']. ' 订单号:'.$r['poi_id']. ' 标的号:'.$arr_poi[$r['poi_id']]['bid_id'].' 标的类型:'.$arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['product_type'].' 券金额:'.$r['amount'].' 投标金额:'.$arr_poi[$r['poi_id']]['amount'].' 期限:'.$arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['bid_period']);
            if ($r['type'] != 2) {
                $tmp['amount'] = number_format($r['amount'], 2);
            }else {
                $productType = $arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['product_type'];
                if ($productType == 1) {
                    $tmp['amount'] = $arr_poi[$r['poi_id']]['amount']*0.01*$r['amount']/12*$arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['bid_period'];
                    $tmp['amount'] = number_format(round($tmp['amount'], 2), 2);
                }elseif($productType==2){
                    $tmp['amount'] = $arr_poi[$r['poi_id']]['amount']*0.01*$r['amount']/372*$arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['bid_period'];
                    $tmp['amount'] = number_format(round($tmp['amount'], 2), 2);
                }else {
                        $tmp['amount'] = '<span style="color:red">'.$r['amount'].'%</span>';
                }
            }

            isset($arr_poi[$r['poi_id']])
                &&isset($arr_bid[$arr_poi[$r['poi_id']]['bid_id']])
                && $tmp['bid_title'] = $arr_bid[$arr_poi[$r['poi_id']]['bid_id']]['bid_title'];
            $records[] = $tmp;
        }
        return $records;
    }
}