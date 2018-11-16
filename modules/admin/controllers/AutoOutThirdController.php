<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\helpers\Constants;
use yii\web\Controller;
use app\modules\admin\models\AutoOutThird;

class AutoOutThirdController extends Controller {

    /**
     * 门店机器信息列表
     */
    public function actionIndex() {
        $request = \Yii::$app->request;
        $get = $request->get();
        $query = (new Query())->select(['auto_out_third_id', 'third_code', 'third_name', 'out_type', 'out_lottery', 'status', 'weight', 'opt_name'])
                ->from("auto_out_third");
        if (isset($get["third_name"]) && !empty($get["third_name"])) {
            $query = $query->andWhere(['third_name' => $get['third_name']]);
        }
        if (isset($get["type"]) && !empty($get["type"])) {
            $query = $query->andWhere(["type" => $get["type"]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        $queryData = $query->orderBy("auto_out_third_id desc");
        $data = new ActiveDataProvider([
            'query' => $queryData,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render("index", ["data" => $data, 'getData' => $get]);
    }
    
    /**
     * 编辑出票方信息
     */
    public function actionAddThird() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $autoLottery = Constants::AUTO_LOTTERY;
            return $this->render("add-out-third", ["data" => $autoLottery]);
        } elseif (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $thirdCode = $request->post('third_code', '');
            $thirdName = $request->post('third_name', '');
            $outType = $request->post('out_type', '');
            $codes = $request->post('codes', '');
            if (empty($thirdName) || empty($outType) || empty($thirdCode)) {
                return $this->jsonResult(109, '参数缺失');
            }
            $isExist = AutoOutThird::find()->select(['third_code'])->where(['third_code' => $thirdCode])->asArray()->one();
            if(!empty($isExist)) {
                return $this->jsonError(109, '该编号已存在,请勿重复定义!!');
            }
            $autoOutThird = new AutoOutThird();
            $autoOutThird->third_code = $thirdCode;
            $autoOutThird->third_name = $thirdName;
            $autoOutThird->out_type = $outType;
            $autoOutThird->out_lottery = implode(',', $codes);
            $autoOutThird->opt_name = \Yii::$app->session["admin_name"];
            $autoOutThird->create_time = date('Y-m-d H:i:s');
            if ($autoOutThird->validate()) {
                if (!$autoOutThird->save()) {
                    return $this->jsonResult(109, "新增失败");
                } else {
                    return $this->jsonResult(600, "新增成功");
                }
            } else {
                return $this->jsonResult(109, "新增失败，表单验证失败");
            }
        }
    }
    

    /**
     * 出票方启用禁用
     */
    public function actionEditUse() {
        $request = \Yii::$app->request;
        $post = $request->post();
        $res = AutoOutThird::updateAll(["status" => $post["sta"],], ["auto_out_third_id" => $post["auto_out_third_id"]]);
        if ($res) {
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(109, "修改失败");
        }
    }

    /**
     * 编辑出票方信息
     */
    public function actionEditThird() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $get = $request->get();
            $thirdId = $get["auto_out_third_id"];
            if (empty($thirdId)) {
                return $this->jsonResult(109, "参数缺失");
            }
            $field = ['auto_out_third_id', 'third_code', 'third_name', 'out_type', 'out_lottery'];
            $thirdData = AutoOutThird::find()->select($field)->where(['auto_out_third_id' => $thirdId])->asArray()->one();
            $outArr = explode(',', $thirdData['out_lottery']);
            $autoLottery = Constants::AUTO_LOTTERY;
            $thirdData['auto_lottery'] = $autoLottery;
            $thirdData['out_lottery'] = $outArr;
            return $this->render("edit-out-third", ["data" => $thirdData]);
        } elseif (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $thirdId = $request->post('auto_out_third_id', '');
            $thirdName = $request->post('third_name', '');
            $outType = $request->post('out_type', '');
            $codes = $request->post('codes', '');
            if (empty($outType)|| empty($thirdId) || empty($codes) || empty($thirdName)) {
                return $this->jsonResult(109, '参数缺失');
            }
            $autoOutThird = AutoOutThird::findOne(['auto_out_third_id' => $thirdId]);
            if (empty($autoOutThird)) {
                return $this->jsonError(109, '该出票方不存在');
            }
            $autoOutThird->third_name = $thirdName;
            $autoOutThird->out_type = $outType;
            $autoOutThird->out_lottery = implode(',', $codes);
            $autoOutThird->opt_name = \Yii::$app->session["admin_name"];
            $autoOutThird->modify_time = date('Y-m-d H:i:s');
            if ($autoOutThird->validate()) {
                if (!$autoOutThird->save()) {
                    return $this->jsonResult(109, "编辑失败");
                } else {
                    return $this->jsonResult(600, "编辑成功");
                }
            } else {
                return $this->jsonResult(109, "编辑失败，表单验证失败");
            }
        }
    }

    /**
     * 删除出票方信息
     */
    public function actionDelThird() {
        $request = \Yii::$app->request;
        $post = $request->post();
        $result = AutoOutThird::deleteAll(["auto_out_third_id" => $post["auto_out_third_id"]]);
        if ($result) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }
    
    /**
     * 设权重
     * @return type
     */
    public function actionSetWeight() {
        $this->layout = false;
        $request = \Yii::$app->request;
        if (\Yii::$app->request->isPost) {
            $thirdId = $request->post('third_id', '');
            $weight = $request->post('third_weight', 1);
            if (empty($thirdId)) {
                return $this->jsonResult(100, '参数缺失');
            }
            $update = AutoOutThird::updateAll(['weight' => $weight], ['auto_out_third_id' => $thirdId]);
            if ($update === FALSE) {
                return $this->jsonResult(109, '修改失败');
            }
            return $this->jsonResult(600, '修改成功', true);
        } else {
            $thirdId = $request->get('third_id', '');
            if (empty($thirdId)) {
                return $this->jsonResult(100, '参数缺失');
            }
            $thirdData = AutoOutThird::find()->select(['auto_out_third_id', 'third_name', 'weight'])->where(['auto_out_third_id' => $thirdId])->asArray()->one();
            return $this->render('set-weight', ['data' => $thirdData]);
        }
    }

}
