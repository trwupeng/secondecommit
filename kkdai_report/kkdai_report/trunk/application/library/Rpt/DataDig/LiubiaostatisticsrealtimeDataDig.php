<?php
/**
 * 流标数据
 * 没上架的标, 没有抓取
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/11/30 0030
 * Time: 上午 9:48
 */
namespace Rpt\DataDig;
class LiubiaostatisticsrealtimeDataDig extends \Rpt\DataDig\RealtimeDataDigBase {


    public $tableHeaderForDay =[
        'ymd_publish' => ['上架日期'],
        'bid_id' => ['标的Id'],
        'bid_title' => ['标的标题'],
        'bid_amount' => ['标的金额(元)', null, 'amount'],
        'bid_period' => ['期限'],
        'bid_interest' => ['利率(%)'],
        'raise_amount' => ['实际募集金额(元)', null, 'amount'],
        'bid_status' => ['状态'],
        'bid_serviceFee'=>['服务费(元)'],
    ];

    public function dataForDay ($where=null, $pager=null, $isDownload=false) {
        $tmp_where = $where;
        $where = [
            'bid_status'=>4011,
            'bid_publish_startdate!'=>null
        ];
        $records = [];
        !empty($tmp_where['ymdFrom'])
            && $where['bid_publish_startdate]']=$tmp_where['ymdFrom'].' 00:00:00';
        !empty($tmp_where['ymdTo'])
            && $where['bid_publish_startdate[']=$tmp_where['ymdTo'].' 23:59:59';

        $fields = 'date_format(bid_publish_startdate, \'%Y-%m-%d\') as ymd_publish,
            bid_id, bid_title, product_type, bid_period, bid_interest,
            bid_amount,(bid_amount - bid_free_amount) as raise_amount,
            bid_serviceFee,bid_status';

        $totalCount = $this->db_produce->getRecordCount('phoenix.bid',$where);
        if(!$totalCount) {
            return $records;
        }

        if(!$isDownload) {
            $fields .= ',bid_id as __pkey__';
        }

        if (!empty($pager)) {
            $pager->init($totalCount, -1);
            $records = $this->db_produce->getRecords( 'phoenix.bid', $fields,
                $where, 'rsort bid_publish_startdate', $pager->page_size,
                $pager->rsFrom()
            );
        }else {
            $records = $this->db_produce->getRecords( 'phoenix.bid', $fields,
                $where, 'rsort bid_publish_startdate' );
        }
        foreach ($records as $k => $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDay), '');
            $tmp['bid_id'] = $r['bid_id'];
            $tmp['bid_title']=$r['bid_title'];
            $tmp['bid_amount']=number_format($r['bid_amount']*0.01, 2);
            $tmp['bid_interest']=$r['bid_interest'] *0.01;
            $tmp['ymd_publish'] = $r['ymd_publish'];
            !empty($r['raise_amount'])
                &&$tmp['raise_amount']=number_format($r['raise_amount']*0.01,2);
            !empty($r['bid_serviceFee'])
                &&$tmp['bid_serviceFee']=number_format($r['bid_serviceFee']*0.01, 2);

            if ($r['product_type'] == 1) {
                $period_unit = '月';
            }else{
                $period_unit = '天';
            }
            $tmp['bid_period']=$r['bid_period'].$period_unit;
            $tmp['bid_status']
                =\Rpt\KkdStatus::returnCodeName('bid_status_of_bid', $r['bid_status']);
            if (!$isDownload) {
                $tmp['__pkey__'] = $r['bid_id'];
            }
            $records[$k] = $tmp;

        }
        return $records;
    }

    public function summaryForDay () {
        $tip = '规则: 不包含未上架的标的';
        return $tip;
    }

    public $tableHeaderForDetail = [
        'customer_id' => ['客户编号', 160],
        'customer_realname' =>['客户姓名'],
        'customer_cellphone' =>['手机号'],
        'bid_title' => ['标的名称'],
        'bid_amount' => ['标的总额(元)', null, 'amount'],
        'bid_period' => ['标的期限'],
        'bid_interest' => ['标的利率(%)'],
        'amount' => ['投资金额(元)', null, 'amount'],
        'poi_status' => ['状态']
    ];
    public function dataForDetail ($where=null, $pager=null) {
        $records = [];
        $fields = 'customer.customer_id, customer_realname, customer_cellphone,
            bid.bid_title, bid.bid_amount, bid.bid_period, bid_interest,
            bid_poi.amount, poi_status,bid_poi.create_time,bid.product_type';
        // 指定标的id
        if(!empty($where['bid_id'])) {
            if(!empty($pager)){
                $totalCount = $this->countByBid($where);
                if(!$totalCount){
                    return $records;
                }
                $pager = $pager->init($totalCount, -1);
                $sql = $this->sqlForRecordsByBid($fields, $where, $pager);
            }else{
                $sql = $this->sqlForRecordsByBid($fields, $where);
            }
        }else { // 未指定标的, 从流标统计过来的全部下载.
//            $totalCount = $this->countByPublishDate($where);
            $sql = $this->sqlForRecordsByPublishDate($fields, $where);
        }

        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        foreach($result as $r) {
            $tmp = array_fill_keys(array_keys($this->tableHeaderForDetail), '');
            $tmp['customer_id'] = $r['customer_id'];
            $tmp['customer_realname'] = $r['customer_realname'];
            $tmp['customer_cellphone'] = $r['customer_cellphone'];
            $tmp['bid_title'] = $r['bid_title'];
            $tmp['bid_amount'] = number_format($r['bid_amount'], 2);
            $tmp['bid_interest'] = $r['bid_interest']*0.01;
            $tmp['amount'] = number_format($r['amount']*0.01, 2);
            $tmp['poi_status'] =
                \Rpt\KkdStatus::returnCodeName('poi_status_of_bid_poi',$r['poi_status']);
            $unit = '月';
            $r['product_type'] !=1 && $unit='天';
            $tmp['bid_period'] = $r['bid_period'].$unit;
            $records[] = $tmp;
        }
//var_log($records, 'records###########');
        return $records;
    }


    private function countByBid ($where) {
        $where = $this->buildWhereByBid($where);
        $sql = 'select count(*) as totalCount from bid_poi'
            .' '.$where;
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        return $result[0]['totalCount'];

    }
    private function countByPublishDate($where) {
        $where = $this->buildWhereByPublishDate($where);
        $sql = 'select count(*) as totalCount from ('
            .'  select * from bid'
            .' '.$where
            .')bid'
            .' inner join bid_poi'
            .' using (bid_id)';
        $result = \Rpt\Funcs::execSql($sql, $this->db_produce);
        return $result[0]['totalCount'];
    }

    private function buildWhereByBid ($where) {
        $bid_id = $where['bid_id'];
        return 'where bid_id=\''.$bid_id.'\'';
    }
    private function buildWhereByPublishDate ($where) {
        $where = 'where bid_status = 4011 and bid_publish_startdate is not null';
        !empty($where['ymdFrom'])
            && $where .= ' '.$where['ymdFrom'].' 00:00:00';
        !empty($where['ymdTo'])
            && $where .= ' '.$where['ymdTo'].' 23:59:59';
        return $where;
    }

    private function sqlForRecordsByBid ($fields, $where, $pager=null) {
        $where = $this->buildWhereByBid($where);
        $sql = 'select '.$fields.' from ('
            .'  select customer_id,bid_id,amount,poi_status,create_time from bid_poi'
            .' '.$where
            .' )bid_poi'
            .' left join bid using (bid_id)'
            .' left join customer'
            .' on bid_poi.customer_id = customer.customer_id'
            .' order by bid_poi.create_time desc';
        if(!empty($pager)){
            $sql .=  ' limit '.$pager->rsFrom().','.$pager->page_size;
        }
        return $sql;
    }

    private function sqlForRecordsByPublishDate ($fields, $where, $pager=null) {
        $where = $this->buildWhereByPublishDate($where);
        $sql = 'select '.$fields.' from ('
		    .' select bid.product_type, bid.bid_publish_startdate,bid_id,bid.bid_title, bid.bid_amount,bid.bid_period, bid_interest  from bid'
		    .' '.$where
            .')bid'
            .' inner join bid_poi'
            .' using (bid_id)'
            .' left join customer'
            .' on customer.customer_id = bid_poi.customer_id'
            .' order by bid.bid_publish_startdate desc ,bid_poi.create_time desc';
        if(!empty($pager)){
            $sql .=  ' limit '.$pager->rsFrom().','.$pager->page_size;
        }
        return $sql;
    }

//    private function recordsByBid ($fields, $where, $pager=null) {
//        $bid_id = $where['bid_id'];
//        $sql = 'select '.$fields.' from ('
//            .'  select customer_id,bid_id, amount, poi_status from bid_poi'
//            .'  where bid_id=\''.$bid_id.'\''
//            .' )bid_poi'
//            .'left join bid using (bid_id)'
//            .'left join customer'
//            .'on bid_poi.customer_id = customer.customer_id';
//
//    }
//
//    private function recordsByPublishDate ($fields, $where, $pager=null) {
//
//    }

}