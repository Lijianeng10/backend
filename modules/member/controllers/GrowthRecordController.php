<?php

namespace app\modules\member\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\member\helpers\Constants;
use app\modules\common\models\UserGrowthRecord;

class GrowthRecordController extends Controller {
    
    /**
     * 成长值记录
     */
    public function actionIndex(){
        $compar = Constants::COMPAR;
        $request = Yii::$app->request;
        $get = $request->get();
        $userInfo = $request->get('user_info', '');
        $star = $request->get('start_date', '');
        $end = $request->get('end_date', '');
        $operator = $request->get('operator', '');
        $growth_value = $request->get('growth_value', '');
        $order_code = $request->get('order_code', '');
        $data=  UserGrowthRecord::find()->select("user_growth_record.*,u.user_name,u.user_tel,u.cust_no,ul.level_name");
        if ($userInfo != '') {
            $data = $data->andWhere(["or", ["u.cust_no" => $userInfo], ["u.user_name" => $userInfo], ["u.user_tel" => $userInfo]]);
        }
        if ($star != '') {
            $data = $data->andWhere([">=", "user_growth_record.create_time",$star." 00:00:00" ]);
        }
        if ($end != '') {
            $data = $data->andWhere(["<=", "user_growth_record.create_time",$end." 23:59:59" ]);
        }
        if ($operator != '' && $operator != '0') {
            if ($growth_value != '') {
                $data = $data->andWhere([$operator, 'user_growth_record.growth_value', $growth_value]);
            }
        }
        if(!empty($order_code)){
            $data = $data->andWhere(["user_growth_record.order_code"=>$order_code]);
        }
        $data=$data ->leftJoin("user as u","u.user_id=user_growth_record.user_id")
                ->leftJoin("user_levels as ul","ul.user_level_id=user_growth_record.levels")
                ->orderBy("user_growth_record.create_time desc")
                ->asArray();
        $provider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => ['exchange_record_id'],
            ],
        ]);
        return $this->render('index',["data"=>$provider,'get' => $get,"compar"=>$compar]);
    }
}

