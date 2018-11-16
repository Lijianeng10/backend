<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\PayType;
use yii\data\ArrayDataProvider;

class PaytypeController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $request = Yii::$app->request;
        $payTypeId = $request->get('pay_type_id', '0');
        $typeList = [];
        $where = [];
        $orWhere = [];
        if ($payTypeId != 0) {
            $where['pay_type_id'] = $payTypeId;
            $orWhere['parent_id'] = $payTypeId;
        }
        $list = PayType::find()->orderBy('parent_id')->asArray()->all();
        $payTypeList = PayType::find()->where($where)->orWhere($orWhere)->orderBy('parent_id')->asArray()->all();
        $tree[0] = "全部";
        $this->childtree($list, $tree, '', 0);
        $list = new ArrayDataProvider([
            'allModels' => $payTypeList,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => ['pay_type_id'],
            ],
        ]);
        return $this->render('index', ['data' => $list, 'typeList' => $tree]);
    }

    public function actionAddPayType() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $parentList = [];
            $parentList[0] = '父级';
            $data = PayType::find()->select(['pay_type_id', 'pay_type'])->where(['parent_id' => 0])->asArray()->all();
            foreach ($data as $val) {
                $parentList[$val['pay_type_id']] = $val['pay_type'];
            }
            return $this->render('add-pay-type', ['data' => $parentList]);
        }
        $request = Yii::$app->request;
        $typeName = $request->post('pay_type_name', '');
        $typeCode = $request->post('pay_type_code', '');
        $parentId = $request->post('parent_id', '');
        $typeSort = $request->post('pay_type_sort', '');
        $typeRemark = $request->post('remark', '');
        if($typeName == '' || $typeCode == '' || $parentId == ''){
            return $this->jsonResult(100, '参数缺失');
        }

        $at_time = 'y/m/d h:i:s';
        $payType = new PayType;
        $payType->pay_type_name = $typeName;
        $payType->pay_type_code = $typeCode;
        $payType->parent_id = $parentId;
        $payType->pay_type_sort = $typeSort;
        $payType->remark = $typeRemark;
        if ($parentId == 0) {
            $maxPayType = PayType::find()->max('pay_type');
            $payType->pay_type = $maxPayType + 1;
            $payType->parent_name = '';
        } else {
            $parentName = PayType::find()->select(['pay_type', 'pay_type_name'])->where(['pay_type_id' => $parentId])->asArray()->one();
            if (empty($parentName)) {
                return $this->jsonResult(109, '该上级不存在');
            }
            $payType->parent_name = $parentName['pay_type_name'];
            $payType->pay_type = $parentName['pay_type'];
        }
        $payType->create_time = date($at_time);
        if (!$payType->validate()) {
            return $this->jsonResult(109, '数据验证失败', $payType->getFirstErrors());
        }
        if (!$payType->save()) {
            return $this->jsonResult(109, '数据保存失败', $payType->getFirstErrors());
        }
        $this->updateLimitPayRedis();
        return $this->jsonResult(600, '新增成功');
    }

    public function actionSetPayType() {
        if (!Yii::$app->request->isPost) {
            return $this->jsonResult(109, '操作有误');
        }
        $request = Yii::$app->request;
        $payTypeId = $request->post('id', '');
        $status = $request->post('status', '');
        if ($payTypeId == '' || $status == '') {
            return $this->jsonResult(109, '参数缺失');
        }
        $payType = PayType::find()->where(['pay_type_id' => $payTypeId])->one();
        if (empty($payType)) {
            return $this->jsonResult(109, '数据有误，请稍后再试');
        }
        $payType->status = $status;
        $payType->modify_time = date('Y-m-d H:i:s');
        if (!$payType->save()) {
            return $this->jsonResult(109, '数据保存失败');
        }
        $this->updateLimitPayRedis();
        return $this->jsonResult(600, '设置成功', true);
    }

    public function actionDeleteType() {
        if (!Yii::$app->request->isPost) {
            return $this->jsonResult(109, '操作有误');
        }
        $request = Yii::$app->request;
        $payTypeId = $request->post('typeId', '');
        if ($payTypeId == '') {
            return $this->jsonResult(109, '参数缺失');
        }
        $payType = PayType::deleteAll(['pay_type_id' => $payTypeId]);
        if ($payType == false) {
            return $this->jsonResult(109, '删除失败');
        }
        $this->updateLimitPayRedis();
        return $this->jsonResult(600, '删除成功', true);
    }

    /**
     * 生成权限子集效果
     */
    public function childtree($info, &$tree, $str, $pid = 0) {
        $str.="|--";
        if (!empty($info)) {
            foreach ($info as $k => &$v) {
                if ($v['parent_id'] == $pid) {
                    $tree[$v["pay_type_id"]] = $str . $v["pay_type"];
                    $this->childtree($info, $tree, $v["pay_type_id"], $str);
                    unset($info[$k]);
                }
            }
        }
    }

    /**
     * 更新限制支付缓存
     */
    public function updateLimitPayRedis() {
        file_get_contents(Yii::$app->params['userDomain']."/api/cron/background/update-pay-limit");
    }

    
    public function actionEditPayType() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $payTypeId = $request->get('pay_type_id', '');
            if($payTypeId == ''){
                return $this->jsonResult(100, '参数缺失');
            }
            $data = PayType::find()->select(['pay_type_id', 'pay_type', 'pay_type_code', 'pay_type_name', 'parent_name', 'remark', 'pay_type_sort', 'default'])->where(['pay_type_id' => $payTypeId])->asArray()->one();
//            print_r($data);die;
            return $this->render('edit-pay-type', ['data' => $data]);
        }
        $request = Yii::$app->request;
        $typeId = $request->post('pay_type_id', '');
        $typeName = $request->post('pay_type_name', '');
        $typeCode = $request->post('pay_type_code', '');
        $typeSort = $request->post('pay_type_sort', '');
        $typeRemark = $request->post('remark', '');
        if($typeName == '' || $typeCode == '' || $typeId == ''){
            return $this->jsonResult(100, '参数缺失');
        }
        
        $at_time = 'Y-m-d H:i:s';
        $payType = PayType::findOne(['pay_type_id' => $typeId]);
        $payType->pay_type_name = $typeName;
        $payType->pay_type_code = $typeCode;
        $payType->pay_type_sort = $typeSort;
        $payType->remark = $typeRemark;
        $payType->modify_time = date($at_time);
        if(!$payType->validate()){
            return $this->jsonResult(109, '数据验证失败', $payType->getFirstErrors());
        }
        if(!$payType->save()) {
            return $this->jsonResult(109, '数据保存失败', $payType->getFirstErrors());
        }
        return $this->jsonResult(600, '编辑成功');
    }
    
    public function actionSetDefault() {
        if(!Yii::$app->request->isPost){
            return $this->jsonResult(109, '操作有误');
        }
        $request = Yii::$app->request;
        $payTypeId = $request->post('id', '');
        $default = $request->post('type_detault', '');
        if($payTypeId == '' || $default == ''){
            return $this->jsonResult(109, '参数缺失');
        }
        if($default == 1){
            $upAll = PayType::updateAll(['default' => 0]);
            if($upAll === false){
                return $this->jsonResult(109, '操作失败，请稍后再试');
            }
        }
        $payType = PayType::find()->where(['pay_type_id' => $payTypeId])->one();
        if(empty($payType)){
            return $this->jsonResult(109, '数据有误，请稍后再试');
        }
        $payType->default = $default;
        $payType->modify_time = date('Y-m-d H:i:s');
        if(!$payType->save()){
            return $this->jsonResult(109, '数据保存失败');
        }
        return $this->jsonResult(600, '设置成功', true);
    }
}
