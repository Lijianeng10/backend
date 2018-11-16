<?php

namespace app\modules\promote\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\promote\models\RedeemCode;
use app\modules\promote\models\StoreUser;
use app\modules\promote\models\RedeemRecord;

class UserController extends Controller {
    /**
     * 
     * @return type
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $open_id = $request->get('open_id', '');
        $storeInfo = $request->get('storeInfo', '');
        $status = $request->get('status', '');
        $codeStatus = $request->get('codeStatus', '');
        $userStatus=[
            ""=>"请选择",
            "0"=>"未领取",
            "1"=>"已领取",
            
        ];
        $useStatus=[
            ""=>"请选择",
//            "0"=>"未领取",
            "1"=>"未使用",
            "2"=>"已使用",
//            "3"=>"已过期",
//            "4"=>"已废除",
            
        ];
        $Info = (new Query())->select("rr.*,s.store_name,u.user_tel,r.redeem_code,r.status as rs")
                ->from("redeem_record as rr")
                ->leftJoin("store_user as s","s.store_code = rr.store_code")
                ->leftJoin("user as u","u.user_id = s.user_id")
                ->leftJoin("redeem_code as r","r.redeem_code_id = rr.redeem_code_id");
        if (!empty($open_id)) {
            $Info = $Info->andWhere(["rr.open_id"=>$open_id]);
        }
        if (isset($storeInfo)&&!empty($storeInfo)) {
            $Info = $Info->andWhere(["or",["s.store_code"=>$storeInfo],["s.store_name"=>$storeInfo],["u.user_tel"=>$storeInfo]]);
        }
        if (isset($status)&&$status!="") {
            $Info = $Info->andWhere(["rr.status" => $status]);
        }
        if (isset($codeStatus)&&$codeStatus!="") {
            $Info = $Info->andWhere(["r.status" => $codeStatus]);
        }
        $Info = $Info->orderBy('rr.id desc');
        $data = new ActiveDataProvider([
            'query' => $Info,
        ]);
        return $this->render('index', ['data'=>$data,'get' => $get,"userStatus"=>$userStatus,"useStatus"=>$useStatus]);
    }
}
