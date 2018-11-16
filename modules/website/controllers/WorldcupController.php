<?php

namespace app\modules\website\controllers;

use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\common\helpers\PublicHelpers;
use app\modules\common\models\WorldCupApply;

/**
 * Default controller for the `index` module
 */
class WorldcupController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $userInfo = $request->get('user_info', '');
        $status = $request->get('status', '');
        $applyStatus = PublicHelpers::APPLY_STATUS;
        $world=  WorldCupApply::find();
        if (!empty($userInfo)) {
            $world = $world->andWhere(["or",["user_name"=>$userInfo],["user_tel"=>$userInfo]]);
        }
        if (isset($status)&&$status!="") {
            $world = $world->andWhere(["status"=>$status]);
        }
        $world =$world->orderBy('id desc');
        $data = new ActiveDataProvider([
            'query' => $world,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['dataList' => $data,"get" => $get,"applyStatus" => $applyStatus]);
    }
    /**
     * 删除广告
     * @return json
     */
    public function actionDelWorld() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/website/worldcup/index');
        }
        $request = Yii::$app->request;
        $Id = $request->post('id', '');
        if ($Id == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = WorldCupApply::deleteAll(['id' => $Id]);
        if ($result != false) {
            return $this->jsonResult(600, '删除成功', '');
        } else {
            return $this->jsonResult(109, '删除失败', '');
        }
    }
    /**
     * 编辑广告
     */
    public function actionEditWorld(){
        if (Yii::$app->request->isGet) {
            $get=Yii::$app->request->get();
            $applyStatus = PublicHelpers::APPLY_STATUS;
            $Id=$get["id"];
            if(empty($Id)){
               return $this->jsonResult(109, '参数有误', '');  
            }
            $info = WorldCupApply::find()->where(["id"=>$Id])->asArray()->one();
            return $this->render('edit-world',["data"=>$info,"applyStatus" => $applyStatus]);
        }elseif(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $Id=$post["id"];
            $user_name = $post["user_name"];
            $user_tel = $post["user_tel"];
            $field = $post["field"];
            $field_name = $post["field_name"];
            $remark = $post["remark"];
            $status = $post["status"];
            $level = $post["level"];
            $money = $post["money"];
            $applyInfo = WorldCupApply::find()->where(["id"=>$Id])->one();
            $applyInfo->user_name= $user_name;
            $applyInfo->user_tel= $user_tel;
            $applyInfo->field= $field;
            $applyInfo->field_name= $field_name;
            $applyInfo->remark= $remark;
            $applyInfo->status= $status;
            $applyInfo->level= $level;
            $applyInfo->money= $money;
           if ($applyInfo->validate()) {
                $result = $applyInfo->save();
                if ($result == false) {
                    return $this->jsonResult(109, '编辑失败');
                }
                return $this->jsonResult(600, '编辑成功');
            } else {
                return $this->jsonResult(109, '表单验证失败', $applyInfo->errors);
            }
        }
    }
}
