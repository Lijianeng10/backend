<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\UserLevels;
use yii\data\ArrayDataProvider;

class GradeController extends Controller{
    
    /**
     * 会员等级
     * @return 
     */
    public function actionIndex(){
//        $session = Yii::$app->session;
//        $agentCode = $session['agent_code'];
//        where(['agent_code'=>$agentCode])->
        $data = UserLevels::find()->orderBy('user_level_id')->asArray()->all();
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['user_level_id'],
            ],
        ]);
        return $this->render('index',['data'=>$provider]);
    }
    
    /**
     * 新增会员等级
     * @return json
     */
    public function actionAddlevels(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            return $this->render("addlevels");
        }elseif (Yii::$app->request->isAjax) {
//            $session = Yii::$app->session;
            $request = Yii::$app->request;
            $post = $request->post();
            $upStatus = $request->post('up_statue','1');
            $data = new UserLevels();
            $data->level_name = $post['level_name'];
            $data->level_growth = $post['growth_vale'];
            $data->up_status = $upStatus;
//            $data->multiple = $post['multiple'];
//            $data->cz_integral = $post['cz_vale'];
//            $data->glcz_discount = $post['discount']/100;
//            $data->glcz_integral = $post['glcz_vale'];
//            $data->agent_code = $session['agent_code'];
            $data->opt_id = \Yii::$app->session["admin_id"];
            $data->create_time = date('Y-m-d H:i:s');
            if ($data->validate()){
                $levelId = $data->save();
                if($levelId == false){
                    return $this->jsonResult(109, '新增失败', '');
                }
                return $this->jsonResult(600, '新增成功', '');
            }  else {
                return $this->jsonResult(109,'新增失败,参数有误，不可为空');
            }
        }  else {
            echo '操作错误';
            exit();
        }
    }
    
    /**
     * 编辑会员等级
     * @return json
     */
    public function actionEditlevels(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $levelId = $request->get('levels_id','');
            if($levelId == ''){
                echo '操作错误';
                exit();
            }
            $data = UserLevels::find()->where(['user_level_id' => $levelId])->asArray()->one();
            return $this->render("editlevels", ['data'=>$data]);
        }elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $post = $request->post();
            $upStatus = $request->post('up_statue',1);
            $levelId = $request->post('level_id','');
            if($levelId == ''){
                return $this->jsonResult(2,'参数有误','');
            }
            $only = UserLevels::find()->where(['level_name' => $post['level_name']])->andWhere(['!=','user_level_id',$levelId])->asArray()->one();
            if(!empty($only)){
                return $this->jsonResult(2,'该等级名称已存在，请重新填写','');
            }
            $data = UserLevels::find()->where(['user_level_id' => $levelId])->one();
            $data->level_name = $post['level_name'];
            $data->level_growth = $post['growth_vale'];
            $data->up_status = $upStatus;
//            $data->multiple = $post['multiple'];
//            $data->glcz_discount = $post['discount']/100;
//            $data->glcz_integral = $post['glcz_vale'];
            $data->opt_id = $session['admin_id'];
            $data->modify_time = date('Y-m-d H:i:s');
            if ($data->validate()){
                $levelId = $data->save();
                if($levelId == false){
                    return $this->jsonResult(109, '编辑失败', '');
                }
                return $this->jsonResult(600, '编辑成功', '');
            }  else {
                return $this->jsonResult(109,'编辑失败,参数有误，不可为空');
            }
        }
    }
    
    /**
     * 删除会员等级
     * @return json
     */
    public function actionDellevels() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/grade/index');
        }

        $request = Yii::$app->request;
        $levelId = $request->post('level_id','');
        
        if($levelId == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        
        $result = UserLevels::deleteAll(['user_level_id'=>$levelId]);
        if($result != false) {
            return $this->jsonResult(600,'删除成功','');
        } else {
            return $this->jsonResult(109,'删除失败','');
        }
           
    }
}

