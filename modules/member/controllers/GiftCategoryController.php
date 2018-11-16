<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\GiftCategory;
use yii\data\ArrayDataProvider;
use app\modules\member\models\Gift;

class GiftCategoryController extends Controller{
    
    /**
     * 礼品分类
     * @return type
     */
    public function actionIndex(){
        $request = Yii::$app->request;
        $get = $request->get();
        $category_name = $request->get('category_name', '');
        $where = [];
        $cateList = [];
        $dataList = [];
        if($category_name != ''){
            $where['category_name'] = $category_name;
        }
        $data = GiftCategory::find()->where($where)->orderBy('gift_category_id')->asArray()->all();
        foreach ($data as $val){
            if($val['parent_id'] == 0){
                $cateList[] = $val;
            }
        }
        foreach ($cateList as $key => $item){
            $dataList[] = $item; 
            foreach ($data as $value){
                if($item['gift_category_id'] == $value['parent_id']){
                    $dataList[] = $value;
                }
            }
        }
        $provider = new ArrayDataProvider([
            'allModels' => $dataList,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['gift_category_id'],
            ],
        ]);
        return $this->render('index',['data'=>$provider,'get'=>$get]);
    }
    
    /**
     * 新增礼品类别
     * @return json
     */
    public function actionAddcate(){
        $this->layout = false;
        if(Yii::$app->request->isGet){
            $cateList[0] = '根类别';
            $parents = GiftCategory::find()->select('gift_category_id,category_name')->where(['parent_id' => 0])->orderBy('gift_category_id')->asArray()->all();
            if(!empty($parents)){
                foreach ($parents as $val){
                    $cateList[$val['gift_category_id']] = $val['category_name'];
                }
            }
            return $this->render('addcate',['cate_list' => $cateList]);
        }elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $cateName = $request->post('cate_name','');
            $remark = $request->post('cate_remark','');
            $post = $request->post();
            if($cateName == ''){
                return $this->jsonResult(109,'类别名称不可为空');
            }
            $only = GiftCategory::find()->select('gift_category_id')->where(['category_name'=>$cateName])->asArray()->one();
            if(!empty($only)){
                return $this->jsonResult(109, '该类别已存在,不可重复');
            }
            $cateData = new GiftCategory();
            $cateData->category_name = $cateName;
            $cateData->parent_id = $post['cate_parent'];
            $cateData->opt_id = $session['admin_id'];
            if($remark != ''){
                $cateData->category_remark = $remark;
            }
            $cateData->create_time = date('Y-m-d H:i:s');
            if($cateData->validate()){
                $cateId = $cateData->save();
                if($cateId == false){
                    return $this->jsonResult(109,'类别新增失败');
                }
                return $this->jsonResult(600, '类别新增成功');
            }else{
                return $this->jsonResult(109, '表单验证失败');
            }
        }
    }
    
    /**
     * 编辑礼品类别
     * @return json
     */
    public function actionEditcate(){
        $this->layout = false;
        if(Yii::$app->request->isGet){
            $request = Yii::$app->request;
            $cateId = $request->get('cate_id','');
            if($cateId == ''){
                echo '参数错误';
                exit();
            }
            $cateData = GiftCategory::find()->where(['gift_category_id' => $cateId])->asArray()->one();
            $cateList[0] = '根类别';
            $parents = GiftCategory::find()->select('gift_category_id,category_name')->where(['parent_id' => 0])->orderBy('gift_category_id')->asArray()->all();
            if(!empty($parents)){
                foreach ($parents as $val){
                    $cateList[$val['gift_category_id']] = $val['category_name'];
                }
            }
            return $this->render('editcate',['cate_list' => $cateList, 'cate_data' => $cateData]);
        }elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $cateName = $request->post('cate_name','');
            $remark = $request->post('cate_remark','');
            $cateId = $request->post('cate_id','');
            if($cateId == ''){
                return $this->jsonResult(2, '参数有误');
            }
            $post = $request->post();
            if($cateName == ''){
                return $this->jsonResult(109,'类别名称不可为空');
            }
            $only = GiftCategory::find()->select('gift_category_id')->where(['category_name'=>$cateName])->andWhere(['!=', 'gift_category_id', $cateId])->asArray()->one();
            if(!empty($only)){
                return $this->jsonResult(109, '该类别已存在,不可重复');
            }
            $cateData = GiftCategory::find()->where(['gift_category_id' => $cateId])->one();
            $cateData->category_name = $cateName;
            if($cateData->parent_id != 0){
                $cateData->parent_id = $post['cate_parent'];
            }
            if($remark != ''){
                $cateData->category_remark = $remark;
            }
            $cateData->opt_id = $session['admin_id'];
            $cateData->modify_time = date('Y-m-d H:i:s');
            if($cateData->validate()){
                $cateId = $cateData->save();
                if($cateId == false){
                    return $this->jsonResult(109,'类别编辑失败');
                }
                return $this->jsonResult(600, '类别编辑成功');
            }else{
                return $this->jsonResult(109, '表单验证失败');
            }
        }
    }
    
    /**
     * 删除礼品类别
     * @return json
     */
    public function actionDelcate(){
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/gift-category/index');
        }

        $request = Yii::$app->request;
        $cateId = $request->post('cate_id','');
        
        if($cateId == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        $ableDel = GiftCategory::find()->where(['parent_id' => $cateId])->asArray()->one();
        if(!empty($ableDel)){
            return $this->jsonResult(109, '该类别已有子类,不可删除');
        }
        $gift = Gift::find()->select('gift_id')->where(['gift_category' => $cateId])->asArray()->one();
        if(!empty($gift)){
            return $this->jsonResult(109, '已有礼品属于此类,不可删除');
        }
        $result = GiftCategory::deleteAll(['gift_category_id'=>$cateId]);
        if($result != false) {
            return $this->jsonResult(600,'删除成功','');
        } else {
            return $this->jsonResult(109,'删除失败','');
        }
    }

    
}

