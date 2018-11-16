<?php

namespace app\modules\website\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\member\helpers\Constants;
use app\modules\common\models\ActivityType;

class ActivityTypeController extends Controller {

    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $typeList = (new Query())->select("*")
                ->from("activity_type");
        $typeList = $typeList->orderBy("create_time desc");
        $data = new ActiveDataProvider([
            'query' => $typeList,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('index', ["data" => $data,"get" => $get]);
    }

    /*
     * 新增活动类型
     */
    public function actionAddType() {
        if (Yii::$app->request->isGet) {
            $this->layout=false;
            return $this->render('add-type');
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $type_name = $request->post("type_name","");
            if (empty($type_name) ) {
                return $this->jsonResult("109", "参数缺失，请将表单填写完整");
            }
            $data = ActivityType::findOne(["type_name"=>$type_name]);
            if(!empty($data)){
                return $this->jsonResult("109", "类型名称重复，请检查");
            }
            //保存活动数据
            $type = new ActivityType();
            $type->type_name = $type_name;
            $type->create_time = date("Y-m-d H:i:s");
            if($type->validate()){
                $res = $type->save();
                if ($res == false) {
                    return $this->jsonResult(109, "新增失败");
                }else{
                    return $this->jsonResult(600, "新增成功");
                }
            }else{
                return $this->jsonResult(109, "表单验证失败",$type->getFirstErrors());
            }
        }
    }
    /*
     * 修改优惠券状态
     */

    public function actionDelType() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $updateRes = ActivityType::deleteAll(["activity_type_id" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }


}
