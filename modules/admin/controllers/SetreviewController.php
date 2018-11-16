<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;

class SetreviewController extends Controller {
    
    public function actionIndex() {
        $key = "IsShield:shield";
        $info = \Yii::redisGet($key);
        $data['is_open'] = $info;
        return $this->render('index', ['data' =>$data]);
    }
    
    public function actionDoSet() {
        $key = "IsShield:shield";
        $info = \Yii::redisGet($key);
        if(empty($info)){
            $isOpen = 1;
        }  else {
            $isOpen = 0;
        }
        \Yii::redisSet($key, $isOpen);
        $newInfo = \Yii::redisGet($key);
        if($info == $newInfo ) {
            return $this->jsonResult(109, '操作失败');
        }
        return $this->jsonResult(600, '操作成功');
    }
}