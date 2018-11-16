<?php

namespace app\modules\lottery\controllers;

use yii\web\Controller;
use Yii;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\LotteryCategory;
use yii\data\ActiveDataProvider;
use app\modules\helpers\UploadForm;
use app\modules\lottery\models\LotteryOrder;
use app\modules\tools\helpers\Uploadfile;

class LotteryController extends Controller {

    public function actionIndex() {
        $model = new Lottery();
        $request = Yii::$app->request;
        $get = Yii::$app->request->get();
        $lotteryInfo = $request->get('lotteryInfo', '');
        $lottery = Lottery::find()->where("1=1");
        if ($lotteryInfo != '') {
            $lottery=$lottery->andWhere(["or",["lottery_code"=> $lotteryInfo],["lottery_name"=> $lotteryInfo]]);
        }
        if (isset($get["category_type"]) && !empty($get["category_type"])) {
            $lotteryCategory=new LotteryCategory;
            $Ary=$lotteryCategory->getLotteryType($get["category_type"]);
            $lottery=$lottery->andWhere(["in","lottery_category_id",$Ary]);
        }
        $lottery=$lottery->orderBy('lottery_sort, lottery_code');
        $provider = new ActiveDataProvider([
            'query' => $lottery,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_code'],
            ],
        ]);
       
        $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
        $tree = array();
        $tree[0] = "请选择";
        $this->childtree($data, $tree, 0, "");
        return $this->render('index', ['result' => $provider, 'category' => $tree]);
    }

    /**
     * 新增新的彩种
     * @return type
     */
    public function actionAddlottery() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
            $tree = array();
            $tree[0] = "请选择";
            $this->childtree($data, $tree, 0, "");
            return $this->render("addlottery", ["model" => $tree]);
        }
        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $only = Lottery::find()->select('lottery_id,lottery_name,lottery_code')->where(['lottery_code' => $post['lottery_code']])->orWhere(['lottery_name' => $post['lottery_name']])->asArray()->one();
        if (!empty($only)) {
            if ($only['lottery_name'] == $post['lottery_name']) {
                return $this->jsonResult(2, '此彩种名称已存在', '');
            }
            if ($only['lottery_code'] == $post['lottery_code']) {
                return $this->jsonResult(2, '此彩种编码已存在', '');
            }
        }
        $cate = $request->post('category_id', '');
        if ($cate == '' || $cate == 0) {
            return $this->jsonResult(2, '请选择有效彩种类型');
        }
        $model = new Lottery();
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            $check = UploadForm::getUpload($file);
            if($check['code'] != 600){
                return $this->jsonResult($check['code'], $check['msg']);
            }
            $saveDir = '/lottery/';
            $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
            $path = json_decode($pathJson,true);
            if($path['code'] != 600){
                return $this->jsonResult($path['code'], $path['msg']);
            }
            $model->lottery_pic = $path['result']['ret_path'];
        } else {
            return $this->jsonResult(2, '请上传图片', '');
        }
        $model->lottery_code = $post["lottery_code"];
        $model->lottery_name = $post["lottery_name"];
        $model->description = $post['lottery_des'];
        $model->lottery_category_id = $post["category_id"];
        if(isset($post['sort'])) {
            $model->lottery_sort = $post['sort'];
        }
        $model->status = 1;
        $model->opt_id = \Yii::$app->session["admin_id"];
        $model->create_time = date($format);
        if ($model->validate(["lottery_code", "lottery_name", "lottery_category_id", "create_time", "status"])) {
            $id = $model->save();
            if ($id == false) {
                return $this->jsonResult(2, '新增失败', $model->getFirstErrors());
            }
            return $this->jsonResult(1, '新增成功', '');
        } else {
            return $this->jsonResult(2, '编辑失败,参数有误，不可为空', $model->getFirstErrors());
        }
    }

    /**
     * 删除彩种
     * @return type
     */
    public function actionDellottery() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/lottery/index');
        }

        $request = Yii::$app->request;
        $id = $request->post('id', '');
        if ($id == '') {
            return $this->jsonResult(2, '参数有误', '');
        }

        $model = Lottery::findOne($id);
        $code = Lottery::find()->select('lottery_code')->where(['lottery_id' => $id])->asArray()->one();
        if (empty($code)) {
            return $this->jsonResult(2, '此彩种已不存在', '');
        }
        $has = LotteryOrder::find()->select('lottery_order_id')->where(['lottery_id' => $code['lottery_code']])->asArray()->one();
        if (!empty($has)) {
            return $this->jsonResult(2, '已有投注单不能删除！', '');
        }
        $result = $model->delete();
        if ($result != false) {
            return $this->jsonResult(1, '删除成功', '');
        } else {
            return $this->jsonResult(2, '删除失败', '');
        }
    }

    /**
     * 修改彩种状态
     * @return json
     */
    public function actionEditsta() {
        $request = Yii::$app->request;
        $status = $request->post('status', '');
        $id = $request->post('id', '');
        if ($id == '' || $status == '') {
            return $this->jsonResult(2, '参数有误', '');
        }

        $model = Lottery::find()->where(['lottery_id' => $id])->one();
        $model->status = $status;
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->save()) {
            return $this->jsonResult(1, '修改成功', '');
        } else {
            return $this->jsonResult(2, '修改失败', '');
        }
    }
    
    /**
     * 修改彩种状态
     * @return json
     */
    public function actionEditsale() {
        $request = Yii::$app->request;
        $status = $request->post('sale_status', '');
        $id = $request->post('id', '');
        if ($id == '' || $status == '') {
            return $this->jsonResult(2, '参数有误', '');
        }

        $model = Lottery::find()->where(['lottery_id' => $id])->one();
        $model->sale_status = $status;
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->save()) {
            return $this->jsonResult(1, '修改成功', '');
        } else {
            return $this->jsonResult(2, '修改失败', '');
        }
    }
    
    
    /**
     * 编辑页面
     * @return type
     */
    public function actionEdit() {
        $this->layout = false;
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $get = Yii::$app->request->get();
        $model = Lottery::find()
                ->where(["lottery_id" => $get['lottery_id']])
                ->asArray()
                ->one();
        $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
        $tree = array();
        $tree[0] = "请选择";
        $this->childtree($data, $tree, 0, "");
        $model["category"] = $tree;
        return $this->render('edit', ['model' => $model]);
    }

    /**
     * 编辑保存
     */
    public function actionEditlottery() {
        $this->layout = false;
        if (!Yii::$app->request->post()) {
            return $this->redirect('/lottery/lottery/edit');
        }

        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = $request->post();
        $model = Lottery::findOne(["lottery_id" => $post['lottery_id']]);
        if ($model == NULL || $model == false) {
            return $this->jsonResult(2, '操作失败,此彩种不存在', '');
        }
        $only = Lottery::find()->where(['lottery_code' => $post['lottery_code']])->orWhere(['lottery_name' => $post['lottery_name']])->andWhere(['!=', 'lottery_id', $post['lottery_id']])->asArray()->one();
        if (!empty($only)) {
            if ($only['lottery_name'] == $post['lottery_name']) {
                return $this->jsonResult(2, '此彩种名称已存在', '');
            }
            if ($only['lottery_code'] == $post['lottery_code']) {
                return $this->jsonResult(2, '此彩种编码已存在', '');
            }
        }
        $cate = $request->post('category_id', '');
        if ($cate == '' || $cate == 0) {
            return $this->jsonResult(2, '请选择有效彩种类型');
        }
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            $check = UploadForm::getUpload($file);
            if($check['code'] != 600){
                return $this->jsonResult($check['code'], $check['msg']);
            }
            $saveDir = '/lottery/';
            $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
            $path = json_decode($pathJson,true);
            if($path['code'] != 600){
                return $this->jsonResult($path['code'], $path['msg']);
            }
            $model->lottery_pic = $path['result']['ret_path'];
        } else {
             if ($model['lottery_pic'] == null) {
                return $this->jsonResult(2, '请上传图片', '');
            }
        }
        $model->lottery_name = $post["lottery_name"];
        $model->lottery_code = $post['lottery_code'];
        $model->description = $request->post('lottery_des', '');
        $model->lottery_category_id = $post["category_id"];
        if(isset($post['sort'])){
            $model->lottery_sort = $post['sort'];
        }
        $model->modify_time = date($format);
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->validate(["lottery_code", "lottery_name", "lottery_category_id", "modify_time"])) {
            $id = $model->save();
            if ($id != false) {
                return $this->jsonResult(1, '编辑成功', '');
            } else {
                return $this->jsonResult(2, '编辑失败', '');
            }
        } else {
            return $this->jsonResult(2, '编辑失败,参数有误，不可为空', $model->getFirstErrors());
        }
    }

    public function actionReadlottery() {
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $get = Yii::$app->request->get();
        $loCate = new LotteryCategory;
        $category = $loCate->getCategoryList();
        $model = Lottery::find()
                ->where(["lottery_id" => $get['lottery_id']])
                ->asArray()
                ->one();
        return $this->render('readlottery', ['model' => $model]);
    }

     /**
     * 修改彩种状态
     * @return json
     */
    public function actionEditResult() {
        $request = Yii::$app->request;
        $status = $request->post('result_status', '');
        $id = $request->post('id', '');
        if ($id == '' || $status == '') {
            return $this->jsonResult(2, '参数有误', '');
        }

        $model = Lottery::find()->where(['lottery_id' => $id])->one();
        $model->result_status = $status;
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->save()) {
            return $this->jsonResult(1, '修改成功', '');
        } else {
            return $this->jsonResult(2, '修改失败', '');
        }
    }
    /**
     * 生成分类子集效果
     */
    public function childtree($info, &$tree, $pid = 0, $str){
        $str.="-";
        if (!empty($info)){
            foreach ($info as $k => &$v) {
                if ($v['parent_id'] == $pid) {
                    $tree[$v["lottery_category_id"]] = $str . $v["cp_category_name"];
                    $this->childtree($info, $tree, $v["lottery_category_id"], $str);
                    unset($info[$k]);
                }
            }
        }
    }
    /**
     * 新增彩种类别
     */
    public function actionAddCategory(){
        $this->layout=false;
        if (!Yii::$app->request->isPost) {
            $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
            $tree = array();
            $tree[0] = "顶级类别";
            $this->childtree($data, $tree, 0, "");
            return $this->render("add-category", ["model" => $tree]);
        }
        $post = Yii::$app->request->post();
        $name = $post["cp_category_name"];
        $parent_id = $post["parent_id"];
        if(empty($name)){
            return $this->jsonResult(109, '请填写类别名');
        }else{
            $only = LotteryCategory::find()->select('cp_category_name')->where(['cp_category_name' => $name])->asArray()->one();
            if (!empty($only)) {
                return $this->jsonResult(109, '此彩种分类名已存在');
            }
        }
        $category = new LotteryCategory();
        $category->cp_category_name =$name;
        $category->parent_id =$parent_id;
        $category->create_time =date("Y-m-d H:i:s");
        if($category->validate()){
            $res = $category->save();
            if($res){
               return $this->jsonResult(600, '新增类别成功'); 
            }else{
               return $this->jsonResult(109, '新增类别失败');
            }
        }else{
           return $this->jsonResult(109,$category->getFirstError()); 
        }
        
    }
}
