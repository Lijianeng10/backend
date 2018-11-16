<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use app\modules\lottery\models\LotteryPlay;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\LotteryCategory;
use yii\data\ActiveDataProvider;

class PlayController extends Controller {
    
    public function actionIndex() {
        $request = Yii::$app->request;
        $category = $request->get('category','');
        $code = $request->get('code','');
        $where = array();
        $andwhere = array();
        if($category != ''){
            $cateName = LotteryCategory::find()->select('cp_category_name')->where(['lottery_category_id'=>$category])->asArray()->one();
            $andwhere['category_name'] = $cateName['cp_category_name'];
        }
        if($code != ''){
            $where['lottery_code'] = $code;
        }       
        $play = LotteryPlay::find()->orderBy('lottery_code')->where($where)->andwhere($andwhere);
//        $playList = array();
//        foreach ($play as &$val) {
//            if(array_key_exists($val['lottery_code'], $playList)){
//                $mark = ['play_code' => $val['lottery_play_code'], 'play_name' => $val['lottery_play_name'], 'num_count' => $val['number_count'], 'example' => $val['example'], 'remark' => $val['format_remark']];
//                $playList[$val['lottery_code']]['play_mark'][] = $mark;
//            } else {
//                $playList[$val['lottery_code']] = ['lottery_code'=>$val['lottery_code'], 'lottery_name' => $val['lottery_name'], 'category_name' => $val['category_name'], 
//                    'play_mark' => [['play_code' => $val['lottery_play_code'], 'play_name' => $val['lottery_play_name'], 'num_count' => $val['number_count'], 'example' => $val['example'], 'remark' => $val['format_remark']]]];
//            }
//        }
        
        $provider = new ActiveDataProvider([
            'query' => $play,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_code'],
            ],
        ]);
        $loCate = new LotteryCategory;
        $category = $loCate->getCategoryList();
        return $this->render('index',['result'=>$provider,'category'=>$category]);
    }
    
    /**
     * 删除玩法
     * @return type
     */
    public function actionDelplay() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/play/index');
        }

        $request = Yii::$app->request;
        $playId = $request->post('play_id','');
        
        if($playId == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        
        $result = LotteryPlay::deleteAll(['lottery_play_id'=>$playId]);
        if($result != false) {
            return $this->jsonResult(1,'删除成功','');
        } else {
            return $this->jsonResult(2,'删除失败','');
        }
           
    }
    
    /**
     * 编辑页面
     * @return type
     */
    public function actionEdit() {
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $get = Yii::$app->request->get();
        $playId = $get['play_id'];
        $lotModel = new Lottery;
        $lot = $lotModel->getLotterynamelist();
        $model = LotteryPlay::find()
                    ->where(['lottery_play_id' => $playId])
                    ->asArray()
                    ->one();
        $model["lottery"] = $lot;
        return $this->render('edit', ['model' => $model]);
    }
    
    /**
     * 编辑保存
     */
    public function actionEditplay(){
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/play/edit');
        }

        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $nums_count = $request->post('nums_count','');
        if($nums_count == ''){
            return $this->jsonResult(2,'号码个数不可为空','');
        }
        $post = $request->post();
        $model = LotteryPlay::findOne(["lottery_play_id" => $post['play_id']]);
        $lottery_name = Lottery::findOne(['lottery_code'=>$post['lottery_code']]);
        if(empty($lottery_name)){
            return $this->jsonResult(2,'请选择有效彩种','');
        }
        $only = LotteryPlay::find()->select('lottery_play_id')->where(['lottery_code'=>$post['lottery_code'],'lottery_play_code'=>$post['play_code']])->orWhere(['lottery_code'=>$post['lottery_code'],'lottery_play_name' => $post['play_name']])->andWhere(['!=','lottery_play_id',$post['play_id']])->asArray()->one();
        if(!empty($only)){
            return $this->jsonResult(2,'此彩种的玩法已存在','');
        }
        $model->lottery_code = $post['lottery_code'];
        $model->lottery_name = $lottery_name['lottery_name'];
        $model->lottery_play_name = $post["play_name"];
        $model->lottery_play_code = $post['play_code'];
        $model->example =  $post['example'];
        $model->number_count = $post["nums_count"];
        $model->format_remark = $post['remark'];
        $model->update_time = date($format);
        if ($model->validate(["lottery_code", "lottery_name", "lottery_play_code", "lottery_play_name", "example", "number_count", "format_remark" ,"update_time"])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'编辑成功','');
            } else {
                return $this->jsonResult(2,'编辑失败','');
            }
        } else {
            return $this->jsonResult(2,'编辑失败,参数有误，不可为空');
        }
    }
    
    /**
     * 添加新玩法
     */
    public function actionAddplay(){
        if (!Yii::$app->request->isAjax) {
            $lotModel = new Lottery;
            $lot = $lotModel->getLotterynamelist();
            return $this->render("addplay", ["model" => $lot]);
        }  
        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = $request->post();
        $code = $request->post('lottery_code','');
        if($code == '' || $code == 0){
            return $this->jsonResult(2,'请选择彩种','');
        }
        $nums_count = $request->post('nums_count','');
        if($nums_count == ''){
            return $this->jsonResult(2,'号码个数不可为空','');
        }
        $model = new LotteryPlay;
        $lottery_name = Lottery::findOne(['lottery_code'=>$post['lottery_code']]);
        $category_name = LotteryCategory::findOne(['lottery_category_id' => $lottery_name['lottery_category_id']]);
        $only = LotteryPlay::find()->select('lottery_play_id')->where(['lottery_code'=>$code,'lottery_play_code'=>$post['play_code']])->orWhere(['lottery_play_name' => $post['play_name']])->asArray()->one();
        if(!empty($only)){
            return $this->jsonResult(2,'此彩种的玩法已存在','');
        }
        $model->lottery_code = $post['lottery_code'];
        $model->lottery_name = $lottery_name['lottery_name'];
        $model->category_name = $category_name['cp_category_name']; 
        $model->lottery_play_name = $post["play_name"];
        $model->lottery_play_code = $post['play_code'];
        $model->example =  $request->post('example','');
        $model->number_count = $post["nums_count"];
        $model->format_remark = $request->post('remark','');
        $model->update_time = date($format);
        if ($model->validate(['lottery_code', 'lottery_name', 'category_name', 'lottery_play_name', 'lottery_play_code', 'example', 'number_count', 'format_remark', 'update_time'])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'新增成功','');
            } else {
                return $this->jsonResult(2,'新增失败','');
            }
        } else {
            return $this->jsonResult(2,'编辑失败,参数有误，不可为空');
        
        }
    }
}




