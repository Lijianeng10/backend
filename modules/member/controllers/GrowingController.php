<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\UserGrowth;
use yii\data\ArrayDataProvider;
use app\modules\member\helpers\Constants;

class GrowingController extends Controller{
    
    /**
     * 会员成长机制
     * @return 
     */
    public function actionIndex(){
        $data = UserGrowth::find()->orderBy('user_growth_id')->asArray()->all();
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['user_growth_id'],
            ],
        ]);
        return $this->render('index',['data'=>$provider]);
    }
    
    /**
     * 新增会员成长机制
     * @return json
     */
    public function actionAddgrowing(){
        $this->layout = false;
        $source = Constants::GROWTH_SOURCE;
        $type = Constants::GROWTH_TYPE;
        if (Yii::$app->request->isGet) {
            return $this->render("addgrowing",['source' => $source, 'type' => $type]);
        }elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $post = $request->post();
            $growthSource = $request->post('growth_source','');
            $valueType = $request->post('growth_type','');
            if($growthSource == '' || $growthSource == 0){
                return $this->jsonResult(2,'请选择有效来源');
            }
            if($valueType == '' || $valueType == 0){
                return $this->jsonResult(2,'请选择有效类型');
            }
            $data = new UserGrowth();
            $data->growth_source = $source[$growthSource];
            $data->growth_type = $type[$valueType];
            $data->growth_value = $post['growth_value'];
            $data->growth_remark = $post['growth_remark'];
            if ($data->validate()){
                $growingId = $data->save();
                if($growingId == false){
                    return $this->jsonResult(109, '新增失败', '');
                }
                return $this->jsonResult(600, '新增成功', '');
            }else {
                return $this->jsonResult(109,'新增失败,参数有误，不可为空');
            }
        }  else {
            echo '操作错误';
            exit();
        }
    }
    
    /**
     * 编辑会员成长机制
     * @return json
     */
    public function actionEditgrowing(){
        $this->layout = false;
        $source = Constants::GROWTH_SOURCE;
        $type = Constants::GROWTH_TYPE;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $growthId = $request->get('growth_id','');
            if($growthId == ''){
                echo '操作错误';
                exit();
            }
            $data = UserGrowth::find()->where(['user_growth_id' => $growthId])->asArray()->one();
            return $this->render("editgrowing", ['data'=>$data, 'source' => $source, 'type' => $type]);
        }elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $post = $request->post();
            $growthId = $request->post('growth_id','');
            $growthSource = $request->post('growth_source','');
            $valueType = $request->post('growth_type','');
            if($growthId == ''){
                return $this->jsonResult(2,'参数有误','');
            }
            if($source == '' || $source == 0){
                return $this->jsonResult(2,'请选择有效来源');
            }
            if($valueType == '' || $valueType == 0){
                return $this->jsonResult(2,'请选择有效类型');
            }
            $data = UserGrowth::find()->where(['user_growth_id' => $growthId])->one();
            $data->growth_source = $source[$growthSource];
            $data->growth_type = $type[$valueType];
            $data->growth_value = $post['growth_value'];
            $data->growth_remark = $post['growth_remark'];
            $data->opt_id = $session['admin_id'];
            if ($data->validate()){
                $growingId = $data->save();
                if($growingId == false){
                    return $this->jsonResult(109, '编辑失败', '');
                }
                return $this->jsonResult(600, '编辑成功', '');
            }  else {
                return $this->jsonResult(109,'编辑失败,参数有误，不可为空');
            }
        }  else {
            echo '操作错误';
            exit();
        }
    }
    
    /**
     * 删除会员成长机制
     * @return json
     */
    public function actionDelgrowing() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/growing/index');
        }

        $request = Yii::$app->request;
        $growthId = $request->post('growth_id','');
        
        if($growthId == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        
        $result = UserGrowth::deleteAll(['user_growth_id'=>$growthId]);
        if($result != false) {
            return $this->jsonResult(600,'删除成功','');
        } else {
            return $this->jsonResult(109,'删除失败','');
        }
           
    }
}

