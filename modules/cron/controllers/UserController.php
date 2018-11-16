<?php

namespace app\modules\cron\controllers;

use yii\web\Controller;

class UserController extends Controller {

    public $defaultAction = 'main';


    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex() {
        echo 'this is /cron/backup/index';
        die;
    }

    /**
     * 说明:每日统计用户投注信息
     * @author chenqiwei
     * @date 2018/6/25 下午3:34
     * @param
     * @return
     */
    public function actionUserStatisticsByDate(){
        $request = \Yii::$app->request;
        $date = $request->post('date');
        $custNo = $request->post('cust_no');
        $where = '';
        if($date == 'all'){//全部重新计算
            $whereDate = '';
        }elseif(!$date){//日期没填 默认前一天
            $date = date("Y-m-d",strtotime("-1 day"));
            $whereDate = " and DATE_FORMAT(o.out_time,'%Y-%m-%d') = '{$date}'";
        }else{//根据用户输入的日期
            $whereDate = " and DATE_FORMAT(o.out_time,'%Y-%m-%d') = '{$date}'";
        }
        if($custNo){//不为空 单独计算 某个用户数据
            $where = " o.cust_no = '{$custNo}' and ";
        }

        $db = \Yii::$app->db;
        $sql = "
            insert into user_statistics2(cust_no,count,bet_money,pay_money,date,create_time) 
              select nt.cust_no,nt.count,nt.bet_money,nt.pay_money,nt.date,now() as create_time from ( 
                  select o.cust_no,count(o.lottery_order_id) as count ,sum(o.bet_money) as bet_money ,sum(p.pay_money) as pay_money,DATE_FORMAT(o.out_time,'%Y-%m-%d') as date
                  from lottery_order o
                  left join pay_record p on o.lottery_order_code = p.order_code and pay_type = 1
                  where  {$where} o.status in (3,4,5) and o.source != 4 {$whereDate} and o.out_time > '2018-01-01'
                  group by cust_no,DATE_FORMAT(o.out_time,'%Y-%m-%d')) as nt
                  ;
        ";
        try{
            $ret = $db->createCommand($sql)->execute();
        }catch (\Exception $e){
            return $this->jsonResult(600,'succ',$e->errorInfo);
        }
        return $this->jsonResult(600,'succ',$ret);
    }

    /**
     * 说明:每日统计代理商投注信息
     * @author chenqiwei
     * @date 2018/6/25 下午3:34
     * @param
     * @return
     */
    public function actionAgentStatisticsByDate(){
        $request = \Yii::$app->request;
        $date = $request->post('date');
        $agentId = $request->post('agent_id');
        $where = '';
        if($date == 'all'){//全部重新计算
            $whereDate = '';
        }elseif(!$date){//日期没填 默认前一天
            $date = date("Y-m-d",strtotime("-1 day"));
            $whereDate = " and DATE_FORMAT(o.out_time,'%Y-%m-%d') = '{$date}'";
        }else{//根据用户输入的日期
            $whereDate = " and DATE_FORMAT(o.out_time,'%Y-%m-%d') = '{$date}'";
        }
        if($agentId){//不为空 单独计算 某个用户数据
            $where = " o.agent_id = '{$agentId}' and ";
        }

        $db = \Yii::$app->db;
        $sql = "
            insert into agents_statistics2(agent_id,count,bet_money,pay_money,date,create_time) 
              select nt.agent_id,nt.count,nt.bet_money,nt.pay_money,nt.date,now() as create_time from ( 
                select o.agent_id,count(o.lottery_order_id) as count ,sum(o.bet_money) as bet_money ,sum(p.pay_money) as pay_money,DATE_FORMAT(o.out_time,'%Y-%m-%d') as date
                from lottery_order o
                left join pay_record p on o.lottery_order_code = p.order_code and pay_type = 1
                where  {$where} o.status in (3,4,5) and o.source != 4 and o.create_time >'2018-01-01' 
                group by agent_id,DATE_FORMAT(o.out_time,'%Y-%m-%d')) as nt
                ;
        ";
        try{
            $ret = $db->createCommand($sql)->execute();
        }catch (\Exception $e){
            return $this->jsonResult(600,'succ',$e->errorInfo);
        }
        return $this->jsonResult(600,'succ',$ret);
    }

}
