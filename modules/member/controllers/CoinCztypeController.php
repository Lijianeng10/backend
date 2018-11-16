<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\UserCoinCzType;
use yii\data\ArrayDataProvider;

class CoinCztypeController extends Controller {

    /**
     * 礼品分类
     * @return type
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $cczType = $request->get('ccz_type', '');
        $where = [];
        if (!empty($cczType)) {
            $where['cz_type'] = $cczType;
        }
        $data = UserCoinCzType::find()->select(['coin_cz_type_id', 'cz_type', 'cz_type_name', 'cz_money', 'cz_coin', 'weal_type', 'weal_value', 'weal_time', 'status', 'opt_name'])->where($where)->orderBy('cz_type')->asArray()->all();
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['cz_type'],
            ],
        ]);
        return $this->render('index', ['data' => $provider, 'cczType' => $cczType]);
    }

    /**
     * 新增充值类型
     * @return json
     */
    public function actionAddCztype() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $wealType = UserCoinCzType::WEAL_TYPE;
            return $this->render('add-cztype', ['wealType' => $wealType]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $type = $request->post('ccz_type', '');
            $typeName = $request->post('ccz_type_name', '');
            $money = $request->post('cz_money', '');
            $coinValue = $request->post('cz_coin', '');
            $wealType = $request->post('weal_type', '');
            $wealValue = $request->post('weal_value', 0);
            $wealTime = $request->post('weal_time', 0);
            $typeExist = UserCoinCzType::find()->select(['coin_cz_type_id'])->where(['cz_type' => $type])->asArray->one();
            if (!empty($typeExist)) {
                return $this->jsonResult(109, '该充值类型已存在！请勿重新添加');
            }
            $typeInfo = new UserCoinCzType();
            $typeInfo->cz_type = $type;
            $typeInfo->cz_type_name = $typeName;
            $typeInfo->cz_money = $money;
            $typeInfo->cz_coin = $coinValue;
            $typeInfo->weal_type = $wealType;
            $typeInfo->weal_value = empty($wealValue) ? 0 : $wealValue;
            $typeInfo->weal_time = empty($wealTime) ? 0 : $wealTime;
            $typeInfo->opt_name = $session['admin_name'];
            $typeInfo->modify_time = date('Y-m-d H:i:s');
            if ($typeInfo->validate()) {
                if (!$typeInfo->save()) {
                    return $this->jsonResult(109, '类型新增失败');
                }
                return $this->jsonResult(600, '类型新增成功');
            } else {
                return $this->jsonResult(109, '表单验证失败');
            }
        }
    }

    /**
     * 编辑充值类型
     * @return json
     */
    public function actionEditCztype() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $typeId = $request->get('cz_type_id', '');
            if ($typeId == '') {
                echo '参数错误';
                exit();
            }
            $wealType = UserCoinCzType::WEAL_TYPE;
            $where = ['coin_cz_type_id' => $typeId];
            $typeInfo = UserCoinCzType::find()->select(['coin_cz_type_id', 'cz_type', 'cz_type_name', 'cz_money', 'cz_coin', 'weal_type', 'weal_value', 'weal_time', 'status'])->where($where)->orderBy('cz_type')->asArray()->one();
            return $this->render('edit-cztype', ['cczType' => $typeInfo, 'wealType' => $wealType]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $typeName = $request->post('ccz_type_name', '');
            $money = $request->post('cz_money', '');
            $coinValue = $request->post('cz_coin', '');
            $wealType = $request->post('weal_type', '');
            $wealValue = $request->post('weal_value', 0);
            $wealTime = $request->post('weal_time', 0);
            $typeId = $request->post('cz_type_id', '');
            if ($typeId == '') {
                return $this->jsonResult(109, '参数有误');
            }

            $typeInfo = UserCoinCzType::findOne(['coin_cz_type_id' => $typeId]);
            $typeInfo->cz_type_name = $typeName;
            $typeInfo->cz_money = $money;
            $typeInfo->cz_coin = $coinValue;
            $typeInfo->weal_type = $wealType;
            $typeInfo->weal_value = empty($wealValue) ? 0 : $wealValue;
            $typeInfo->weal_time = empty($wealTime) ? 0 : $wealTime;
            $typeInfo->opt_name = $session['admin_name'];
            $typeInfo->modify_time = date('Y-m-d H:i:s');
            if ($typeInfo->validate()) {
                if (!$typeInfo->save()) {
                    return $this->jsonResult(109, '类型编辑失败');
                }
                return $this->jsonResult(600, '类型编辑成功');
            } else {
                return $this->jsonResult(109, '表单验证失败');
            }
        }
    }

    /**
     * 删除充值类型
     * @return json
     */
    public function actionDeltype() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/coin-cztype/index');
        }
        $request = Yii::$app->request;
        $typeId = $request->post('type_id', '');

        if ($typeId == '') {
            return $this->jsonResult(2, '参数有误', '');
        }
        $result = UserCoinCzType::deleteAll(['coin_cz_type_id' => $typeId]);
        if ($result != false) {
            return $this->jsonResult(600, '删除成功', '');
        } else {
            return $this->jsonResult(109, '删除失败', '');
        }
    }

    /**
     * 停启充值类型
     * @return json
     */
    public function actionEditStatus() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/coin-cztype/index');
        }
        $request = Yii::$app->request;
        $typeId = $request->post('type_id', '');

        if ($typeId == '') {
            return $this->jsonResult(2, '参数有误', '');
        }
        $typeInfo = UserCoinCzType::findOne(['coin_cz_type_id' => $typeId]);
        if (empty($typeInfo)) {
            return $this->jsonResult(109, '此类型不存在，请刷新');
        }
        $typeInfo->status = $typeInfo->status == 1 ? 2 : 1;
        $session = Yii::$app->session;
        $typeInfo->opt_name = $session['admin_name'];
        $typeInfo->modify_time = date('Y-m-d H:i:s');
        if (!$typeInfo->save()) {
            return $this->jsonResult(109, '操作失败', '');
        }
        return $this->jsonResult(600, '操作成功', '');
    }

}
