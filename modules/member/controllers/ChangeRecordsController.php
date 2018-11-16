<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\ExchangeRecord;
use app\modules\member\models\ExgiftRecord;
use app\modules\member\models\User;
use app\modules\member\helpers\Constants;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class ChangeRecordsController extends Controller{
    
    /**
     * 咕币变动记录
     * @return 
     */
    public function actionIndex(){
        $transactionType=Constants::TRANSACTION_TYPE;
        $compar = Constants::COMPAR;
        $payType = Constants::PAY_TYPE;
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $get = $request->get();
        $infor = $request->get('user_infor', '');
        $agent = $request->get('agent', '');
        $start = $request->get('start_date', '');
        $end = $request->get('end_date', '');
        $integral = $request->get('integral','0');
        $intval = $request->get('integral_val','');
        $order_code = $request->get('order_code','');
        $transaction_type = $request->get('transaction_type',0);
        $exchange_type = $request->get('exchange_type',0);

//        if($agent != ''){
//            $whStr1 = "u.agent_name = '" . $agent . "'or u.agent_code = '" . $agent ." '";
//        }
        $data = (new Query())->select('i.*,i.create_time as itime,u.*')
                ->from('user_gl_coin_record as i')
                ->innerJoin('user as u', 'i.user_id = u.user_id');
        if($infor != ''){
            $data = $data->andWhere(["or",["u.cust_no"=>$infor],["u.user_tel"=>$infor],["u.user_name"=>$infor]]);
        }
        if($start!=''){
            $data = $data->andWhere([">=","i.create_time",$start." 00:00:00"]);
        }
        if($end!=''){
            $data = $data->andWhere(["<=","i.create_time",$end." 23:59:59"]);
        }
        if($integral != '0' && $integral != ''){
            if($intval != ''){
                $data =$data->andWhere([$integral, 'i.coin_value', $intval]);
            }
        }
        if($order_code != ''){
            $data = $data->andWhere(["i.order_code"=>$order_code]);
        }
        if(!empty($transaction_type)){
            $data = $data->andWhere(["i.transaction_type"=>$transaction_type]);
        }
        if(!empty($exchange_type)){
            $data = $data->andWhere(["i.exchange_type"=>$exchange_type]);
        }
        $data = $data->orderBy('i.create_time desc');
        $provider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['data' => $provider, 'get' => $get, 'compar' => $compar,"transactionType"=>$transactionType,'payType'=>$payType]);
    }
}

