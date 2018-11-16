<?php

namespace app\modules\website\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\PayType;
use yii\data\ArrayDataProvider;
use app\modules\common\models\CallbackBase;
use yii\data\ActiveDataProvider;
use app\modules\common\models\CallbackDetail;
use app\modules\common\models\CallbackLog;

class CallbackLogController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $request = \Yii::$app->request;
        $get= \Yii::$app->request->get();
        $dataInfo = CallbackLog::find();
        $data = new ActiveDataProvider([
            'query' => $dataInfo,
            'pagination' => [
                'pageSize' =>20
            ]
        ]);
         return $this->render('index', ['data' => $data,'get'=>$get]);
    }

    public function actionAdd() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            return $this->render('add', []);
        }
        $request = Yii::$app->request;
        $url = $request->post('url', '');
        $times = $request->post('times', 3);
        $name = $request->post('name', '');
        $agent_id = $request->post('agent_id', 0);
        $third_type = $request->post('third_type', 0);
        $type = $request->post('type', 0);
        $remark = $request->post('remark', '');
        if(!$url || !$times|| !$name){
            return $this->jsonResult(100, '参数缺失');
        }

        $callbackBase = new CallbackBase();
        $callbackBase->url = $url;
        $callbackBase->times = $times;
        $callbackBase->name = $name;
        $callbackBase->agent_id = $agent_id;
        $callbackBase->remark = $remark;
        $callbackBase->c_time = time();
        $callbackBase->third_type = $third_type;
        $callbackBase->type = $type;
        $callbackBase->code = self::creatCode();
        if (!$callbackBase->validate()) {
            return $this->jsonResult(109, '数据验证失败', $callbackBase->getFirstErrors());
        }
        if (!$callbackBase->save()) {
            return $this->jsonResult(109, '数据保存失败', $callbackBase->getFirstErrors());
        }
        return $this->jsonResult(600, '新增成功');
    }

	private static function creatCode()
	{
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$string = time();
		$len=10;
		for (; $len >=1; $len --)
		{
			$position = mt_rand() % strlen($chars);
			$position2 = mt_rand() % strlen($string);
			$string = substr_replace($string, substr($chars, $position, 1), $position2, 0);
		}
		return $string;
	}

    
    public function actionEdit() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $id = $request->get('id', '');
            if($id == ''){
                return $this->jsonResult(100, '参数缺失');
            }
            $data = CallbackBase::find()->select('*')->where(['id' => $id])->asArray()->one();
            return $this->render('edit', ['data' => $data]);
        }
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        $url = $request->post('url', '');
        $times = $request->post('times', 3);
        $name = $request->post('name', '');
        $agent_id = $request->post('agent_id', 0);
        $remark = $request->post('remark', '');
        if(!$url || !$times|| !$name){
            return $this->jsonResult(100, '参数缺失');
        }
        
        $callbackBase = CallbackBase::findOne(['id' => $id]);
        $callbackBase->url = $url;
        $callbackBase->times = $times;
        $callbackBase->name = $name;
        $callbackBase->agent_id = $agent_id;
        $callbackBase->remark = $remark;
        if(!$callbackBase->validate()){
            return $this->jsonResult(109, '数据验证失败', $payType->getFirstErrors());
        }
        if(!$callbackBase->save()) {
            return $this->jsonResult(109, '数据保存失败', $payType->getFirstErrors());
        }
        return $this->jsonResult(600, '编辑成功');
    }

}
