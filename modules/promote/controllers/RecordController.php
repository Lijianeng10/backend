<?php

namespace app\modules\promote\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\promote\models\RedeemCode;

class RecordController extends Controller {
    /**
     * 
     * @return type
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $redeem_code = $request->get('redeem_code', '');
        $storeInfo = $request->get('storeInfo', '');
        $status = $request->get('status', '');
        $recordStatus=[
            ""=>"请选择",
            "0"=>"未领取",
            "1"=>"未使用",
            "2"=>"已使用",
//            "3"=>"已过期",
//            "4"=>"已废除",
            
        ];
        $Info = RedeemCode::find()
                ->select("redeem_code.*,store_user.store_name,store_user.store_code")
                ->leftJoin("store_user","store_user.id = redeem_code.store_id");
        if (!empty($redeem_code)) {
            $Info = $Info->andWhere(["redeem_code.redeem_code"=>$redeem_code]);
        }
        if (isset($storeInfo)&&!empty($storeInfo)) {
            $Info = $Info->andWhere(["or",["store_user.store_code"=>$storeInfo],["store_user.store_name"=>$storeInfo]]);
        }
        if (isset($status)&&$status!="") {
            $Info = $Info->andWhere(["redeem_code.status" => $status]);
        }
        $Info =$Info->orderBy('redeem_code.redeem_code_id desc');
        $data = new ActiveDataProvider([
            'query' => $Info,
        ]);
        return $this->render('index', ['data'=>$data,'get' => $get,"recordStatus"=>$recordStatus]);
    }
    /**
     * 新增兑换券
     */
    public function actionAddCode(){
        $this->layout = false;
        $request = \Yii::$app->request;
        if (\Yii::$app->request->isPost) {
            $ns = $request->post('ns', '');
            $nums = $request->post('nums', '');
            $numbers = $request->post('numbers', '');
            if (empty($ns)||empty($nums)||empty($numbers)) {
                return $this->jsonResult(109, '参数缺失');
            }
             //新增兑换码列表
            $db = Yii::$app->db;
            $times = date('Y-m-d H:i:s');
            $detail = "insert into redeem_code(redeem_code,type,create_time) values";
            for ($i = 1; $i <= $numbers; $i++) {
                $redeem_code = $this->getRedeemMark($nums);
                //验证兑换码是否重复
                $result = RedeemCode::find()->where(["redeem_code" => $redeem_code])->asArray()->one();
                if (!empty($result)) {
                     return $this->jsonResult(109, "兑换码重复");
                }
                if ($i == $numbers) {
                    $detail.="('" . $redeem_code . "','"  . $ns . "','" . $times . "')";
                } else {
                    $detail.="('" . $redeem_code . "','"  . $ns . "','" . $times . "'),";
                }
            }
            $addDetail = $db->createCommand($detail)->execute();
            if($addDetail){
                 return $this->jsonResult(600, '兑换码生成成功');
            }else{
                return $this->jsonResult(109, '生成失败');
            }
           
        } else {
            return $this->render('add-code');
        }
        
    }
     /**
     * 生成兑换码
     */
    public function getRedeemMark($nums) {
        $str = md5(uniqid(microtime(true), true));
        $mark = strtoupper(substr($str, 0, $nums));
        return $mark;
    }
}
