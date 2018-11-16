<?php

namespace app\modules\website\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\modules\common\helpers\Constants;

/**
 * Default controller for the `index` module
 */
class AccessController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        $session = \Yii::$app->session;
        $get= \Yii::$app->request->get();
        $where =['and'];
        $where[] = ['in','info_type',[1,2,3,4,5]];
        //不是咕啦内部用户只能看到自己的统计数据
        if($session['type']!=0){
            if($session['admin_name']=='meitu'){
                $where[] = ['info_type'=>1];
            }
        }
        $type = Constants::ACCESS_TYPE;
        $accessRecord = (new Query())->select("*")
                ->from("access")
                ->where($where);
        if(!empty($get["type"])){
            $accessRecord=$accessRecord->andWhere(["info_type"=>$get["type"]]);
        }
        if (!empty($get["startdate"])) {
            $accessRecord = $accessRecord->andWhere([">=", "date_time", $get["startdate"] . " 00:00:00"]);
        }
        if (!empty($get["enddate"])) {
            $accessRecord = $accessRecord->andWhere(["<=", "date_time", $get["enddate"] . " 23:59:59"]);
        }
        $accessRecord=$accessRecord->orderBy('date_time desc');
        $data = new ActiveDataProvider([
            'query' => $accessRecord,
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);
        return $this->render('index', ['data' => $data,'get'=>$get,"type"=>$type]);
    }
}