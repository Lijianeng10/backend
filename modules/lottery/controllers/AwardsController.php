<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use app\modules\lottery\models\LotteryLevels;
use app\modules\lottery\models\LotteryCategory;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\helpers\Constant;

class AwardsController extends Controller {
    
    public function actionIndex() { 
        $request = Yii::$app->request;
        $category = $request->get('category','');
        $code = $request->get('code','');
        $where = array();
        $andwhere = array();
        $orwhere = [];
        if($category != ''){
            $cateName = LotteryCategory::find()->select('cp_category_name')->where(['lottery_category_id'=>$category])->asArray()->one();
            $andwhere['lottery_category'] = $cateName['cp_category_name'];
        }
        if($code != ''){
            $where['lottery_code'] = $code;
            $orwhere['lottery_name'] = $code;
        }
        $levels = LotteryLevels::find()->orderBy('lottery_code,levels_code')->where($where)->orWhere($orwhere)->andwhere($andwhere);
//        $levelList = array();
//        foreach ($levels as $val){
//            if(array_key_exists($val['lottery_code'], $levelList)){
//                $levelList[$val['lottery_code']]['awards_code'][] =  ["l_code" =>$val['levels_code'], 'l_name'=>$val['levels_name'], 'l_remark' => $val['levels_remark'],'levels_bonus'=>$val['levels_bonus'], 'bonus_category'=>$val['levels_bonus_category'],'levels_red'=>$val['levels_red'], 'levels_blue'=>$val['levels_blue']];
//                
//            } else {
//                $levelList[$val['lottery_code']] = ['lottery_code'=>$val['lottery_code'], 'lottery_name' => $val['lottery_name'], 'category_name' => $val['lottery_category'], 
//                    'awards_code' => [["l_code" =>$val['levels_code'], 'l_name'=>$val['levels_name'], 'l_remark' => $val['levels_remark'],
//                        'levels_bonus'=>$val['levels_bonus'], 'bonus_category'=>$val['levels_bonus_category'],'levels_red'=>$val['levels_red'], 'levels_blue'=>$val['levels_blue']]]];
//            }
//        }
        $provider = new ActiveDataProvider([
            'query' => $levels,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_code'],
            ],
        ]);
        $loCate = new LotteryCategory;
        $category = $loCate->getCategoryList();
        return $this->render('index',['result'=>$provider, 'category'=>$category]);
    }
    
    
    /**
     * 删除奖级
     * @return type
     */
    public function actionDelaward() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/awards/index');
        }

        $request = Yii::$app->request;
        $award_id = $request->post('award_id','');
        
        if($award_id == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        
        $result = LotteryLevels::deleteAll(['levels_id'=>$award_id]);
        if($result != false) {
            return $this->jsonResult(1,'删除成功','');
        } else {
            return $this->jsonResult(2,'删除失败','');
        }
           
    }
    
    /**
     * 添加新奖级
     */
    public function actionAddawards(){
        if (!Yii::$app->request->isAjax) {
            $lotModel = new Lottery;
            $lot = $lotModel->getLotterynamelist();
            $levels = Constant::LEVEL_CODE;
            $redNums = Constant::RED_NUMS;
            $blueNums = Constant::BLUE_NUMS;
            $awards = Constant::awardList();
            $model['lottery'] = $lot;
            $model['levels'] = $levels;
            $model['red_nums'] = $redNums;
            $model['blue_nums'] = $blueNums;
            $model['awards'] = $awards;
            return $this->render("addawards", ["model" => $model]);
        }
        
        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = $request->post();
        if($post['lottery_code'] == 0){
            return $this->jsonResult(2,'请选择有效彩种','');
        }
        $cate = $request->post('awards_code','');
        if($cate == '' || $cate == '0'){
            return $this->jsonResult(2,'请选择有效奖金类型');
        }
        $model = new LotteryLevels;
        $lottery_name = Lottery::findOne(['lottery_code'=>$post['lottery_code']]);
        $category_name = LotteryCategory::findOne(['lottery_category_id' => $lottery_name['lottery_category_id']]);
        $model->lottery_code = $post['lottery_code'];
        $model->lottery_name = $lottery_name['lottery_name'];
        $model->lottery_category = $category_name['cp_category_name']; 
        $model->levels_code = $post["levels_code"];
        $model->levels_name = $post['levels_name'];
        $model->levels_red =  $post['red_nums'];
        $model->levels_blue = $post['blue_nums'];
        $model->levels_remark = $post["mark"];
        $model->levels_bonus_category =$post['awards_code'];
        $model->levels_bonus = $post['awards'];
        $model->levels_bonus_remark = $post['remark'];
        if ($model->validate(['lottery_code', 'lottery_name', 'category_name', 'levels_code', 'levels_name', 'levels_red', 'levels_red', 'levels_blue', 'levels_remark', 'levels_bonus_category', 'levels_bonus', 'levels_bonus_remark'])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'新增成功','');
            } else {
                return $this->jsonResult(2,'新增失败','');
            }
        } else {
            return $this->jsonResult(2,'新增失败,参数有误，不可为空',$model->getFirstErrors());
        
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
        $awardsId = $get['awards_id'];
        $lotModel = new Lottery;
        $lot = $lotModel->getLotterynamelist();
        $model = LotteryLevels::find()
                    ->where(['levels_id' => $awardsId])
                    ->asArray()
                    ->one();
        $model["lottery"] = $lot;
        $levels = Constant::LEVEL_CODE;
        $redNums = Constant::RED_NUMS;
        $blueNums = Constant::BLUE_NUMS;
        $awards = Constant::awardList();
        $model['levels'] = $levels;
        $model['red_nums'] = $redNums;
        $model['blue_nums'] = $blueNums;
        $model['awards'] = $awards;
        return $this->render('edit', ['model' => $model]);
    }
    
    /**
     * 编辑保存
     */
    public function actionEditawards(){
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/awards/edit');
        }

        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $cate = $request->post('awards_code','');
        if($cate == '' || $cate == '0'){
            return $this->jsonResult(2,'请选择有效奖金类型');
        }
        $post = $request->post();
        $model = LotteryLevels::findOne(['levels_id' => $post['levels_id']]);
        $data = Constant::AWARD_CODE;
        $model->levels_code = $post["levels_code"];
        $model->levels_name = $post['levels_name'];
        $model->levels_red =  $post['red_nums'];
        $model->levels_blue = $post['blue_nums'];
        $model->levels_remark = $post["mark"];
        $model->levels_bonus_category =$post['awards_code'];
        $model->levels_bonus = $post['awards'];
        $model->levels_bonus_remark = $post['remark'];
        if ($model->validate(['lottery_code', 'lottery_name', 'category_name', 'levels_code', 'levels_name', 'levels_red', 'levels_red', 'levels_blue', 'levels_remark', 'levels_bonus_category', 'levels_bonus', 'levels_bonus_remark'])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'编辑成功','');
            } else {
                return $this->jsonResult(2,'编辑失败','');
            }
        } else {
            return $this->jsonResult(2,'编辑失败,参数有误，不可为空',$model->getFirstErrors());
        
        }
    }
}
