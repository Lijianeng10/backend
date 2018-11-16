<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\ExchangeRecord;
use app\modules\member\models\ExgiftRecord;
use app\modules\member\helpers\Constants;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class ExchangeRecordController extends Controller {

    /**
     * 兑换记录
     * @return 
     */
    public function actionIndex() {
        $exType = Constants::EX_TYPE;
        $payType = Constants::PAY_TYPE;
        $orderStatus = Constants::ORDER_STATUS;
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $get = $request->get();
        $info = $request->get('user_info', '');
        $order_code = $request->get('order_code', '');
        $start = $request->get('start_date', '');
        $end = $request->get('end_date', '');
        $type = $request->get('ex_type', '0');
        $pay_type = $request->get('pay_type', '0');
        $order_status = $request->get('order_status', '0');
        $data = (new Query())->select('e.*,u.user_name,u.agent_name')
                ->from('exchange_record as e')
                ->leftJoin('user as u', "u.cust_no = e.cust_no");
        if($info!=''){
            $data = $data->andWhere(["or",["u.cust_no"=>$info],["u.user_tel"=>$info],["u.user_name"=>$info]]);
        }
        if($order_code!=''){
            $data = $data->andWhere(["e.exch_code"=>$order_code]);
        }
        if($start!=''){
            $data = $data->andWhere([">=","e.create_time",$start." 00:00:00"]);
        }
        if($end!=''){
            $data = $data->andWhere(["<=","e.create_time",$end." 23:59:59"]);
        }
        if ($type != '0' && $type != '') {
            $data = $data->andWhere(["e.exch_type"=>$type]);
        }
        if (!empty($pay_type)) {
            $data = $data->andWhere(["e.pay_type"=>$pay_type]);
        }
        //默认只显示已支付订单
        if (!empty($order_status)) {
            $data = $data->andWhere(["e.order_status"=>$order_status]);
        }else{
            if (isset($get["choose"])) {
                $data = $data->andWhere(["e.order_status"=>$get["choose"]]);
            } else {
                $data = $data->andWhere(["<>", "e.order_status", "1"]);
            }
        }
        $data = $data->orderBy('e.exchange_record_id desc');
        $provider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        return $this->render('index',['data'=>$provider, 'exType' => $exType,'get'=>$get,'payType'=>$payType,'orderStatus'=>$orderStatus]);
    }
    
    /**
     * 积分兑换查看
     * @return 
     */
    public function actionView(){
        if (Yii::$app->request->isGet) {
            $exType = Constants::EX_TYPE;
            $get = Yii::$app->request->get();
            if(!isset($get['exchange_id'])){
                echo '参数错误';
                return $this->redirect('/member/exchange-record/index');
            }
            $query = new Query;
            $data = $query->select('e.*,u.user_tel,u.province,u.city,u.area,u.address,u.agent_name,u.user_name')
                    ->from('exchange_record as e')
                    ->leftJoin('user as u', 'u.cust_no = e.cust_no')
                    ->where(['exchange_record_id'=>$get['exchange_id']])
                    ->orderBy('e.exchange_record_id')
                    ->one();
            $detail = ExgiftRecord::find()->where(['exchange_id'=>$get['exchange_id']])->asArray()->all();
            if(empty($data)){
                echo '该订单表不存在，请返回原页';
                return $this->redirect('/member/exchange-record/index');
            }
            return $this->render('view', ['data' => $data,'detail' => $detail, 'exType'=>$exType]);
        }  else {
            echo '操作错误';
            return $this->redirect('/member/exchange-record/index');
        } 
    }
}
