<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\Picture;
use yii\data\ArrayDataProvider;
use app\modules\tools\helpers\Uploadfile;

class PictureController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $data = Picture::find()->asArray()->all();
        $list = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => ['picture_id'],
            ],
        ]);
        return $this->render('index', ['data' => $list]);
    }

    public function actionAddPicture() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            return $this->render('add-picture');
        }
        $request = Yii::$app->request;
        $typeCode = $request->post('type_code', '');
        $typeName = $request->post('type_name');
        if ($typeCode == '' || $typeName == '') {
            return $this->jsonResult(100, '参数缺失');
        }
        $exist = Picture::find()->select(['picture_type_code'])->where(['picture_type_code' => $typeCode])->asArray()->one();
        if(!empty($exist)){
            return $this->jsonResult(109, '该图片类型已存在');
        }
        $picture = new Picture;
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            if ($file['name']) {
                $pic = $file['tmp_name'];
                $type = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
                if (!in_array($type, ['gif', 'jpg', 'jpeg', 'png'])) {
                    $this->jsonResult(440, '文件格式不正确');
                }
                $key = 'img/sys/paytype_pic/' . $typeCode;
                $url = Uploadfile::qiniu_upload($pic, $key); //上传至七牛服务器
                if ($url == 441) {
                    $this->jsonResult(441, '上传失败');
                }
                $picture->picture_url = $url;
            }
        } else {
            return $this->jsonResult(109, '请上传图片');
        }
        $picture->picture_type_code = $typeCode;
        $picture->picture_type_name = $typeName;
        $picture->create_time = date('Y-m-d H:i:s');
        if(!$picture->validate()){
            return $this->jsonResult(109, '数据验证失败', $picture->getFirstErrors());
        }
        if(!$picture->save()){
            return $this->jsonResult(109, '数据保存失败');
        }
        return $this->jsonResult(600, '提交成功');
    }

    public function actionDelete() {
        if(!Yii::$app->request->isPost){
            return $this->jsonResult(109, '操作有误');
        }
        $request = Yii::$app->request;
        $pictureId = $request->post('pictureId', '');
        if($pictureId == '') {
            return $this->jsonResult(109, '参数缺失');
        }
        $picture = Picture::deleteAll(['picture_id' => $pictureId]);
        if($picture == false) {
            return $this->jsonResult(109, '删除失败');
        }
        return $this->jsonResult(600, '删除成功', true);
    }
    
    public function actionEdit() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $picId = $request->get('pic_id', '');
            $picData = Picture::find()->where(['picture_id' => $picId])->asArray()->one();
            return $this->render('edit', ['data' => $picData]);
        }
        $request = Yii::$app->request;
        $picId = $request->post('pic_id');
        $picture = Picture::findOne(['picture_id' => $picId]);
        $typeCode = $picture->picture_type_code;
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            if ($file['name']) {
                $pic = $file['tmp_name'];
                $type = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
                if (!in_array($type, ['gif', 'jpg', 'jpeg', 'png'])) {
                    $this->jsonResult(440, '文件格式不正确');
                }
                $key = 'img/sys/paytype_pic/' . $typeCode;
                $url = Uploadfile::qiniu_upload($pic, $key); //上传至七牛服务器
                if ($url == 441) {
                    $this->jsonResult(441, '上传失败');
                }
                $picture->picture_url = $url;
            }
        } else {
            return $this->jsonResult(109, '请上传图片');
        }
        $picture->modify_time = date('Y-m-d H:i:s');
        if(!$picture->validate()){
            return $this->jsonResult(109, '数据验证失败', $picture->getFirstErrors());
        }
        if(!$picture->save()){
            return $this->jsonResult(109, '数据保存失败');
        }
        return $this->jsonResult(600, '提交成功');
    }
}
