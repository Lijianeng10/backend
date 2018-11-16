<?php

namespace app\modules\website\controllers;

use yii\web\Controller;
use app\modules\common\models\JdUrl;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;

class ToJdController extends Controller {

    public function actionIndex() {
        $urlData = JdUrl::find()->select(['jd_url_id', 'url', 'pic_url', 'opt_name'])->asArray()->one();
        return $this->render('index', ['data' => $urlData]);
    }

    public function actionEditUrl() {
        $request = \Yii::$app->request;
        $url = $request->post('url', '');
        if (empty($url)) {
            return $this->jsonError(109, '请填写有效的跳转链接！');
        }
        $urlId = $request->post('jd_url_id', '');
        if (empty($urlId)) {
            $urlModel = new JdUrl();
            $urlModel->create_time = date('Y-m-d H:i:s');
        } else {
            $urlModel = JdUrl::findOne(['jd_url_id' => $urlId]);
            $urlModel->modify_time = date('Y-m-d H:i:s');
        }
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            $check = UploadForm::getUpload($file);
            if ($check['code'] != 600) {
                return $this->jsonResult($check['code'], $check['msg']);
            }
            $saveDir = '/tojd/';
            $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
            $path = json_decode($pathJson, true);
            if ($path['code'] != 600) {
                return $this->jsonResult($path['code'], $path['msg']);
            }
            $urlModel->pic_url = $path['result']['ret_path'];
        } else {
            $urlModel->pic_url = $urlModel->pic_url;
        }
        $urlModel->url = $url;
        $urlModel->opt_name = \Yii::$app->session['admin_name'];
        if (!$urlModel->save()) {
            return $this->jsonError(109, '编辑失败');
        }
        return $this->jsonResult(600, '编辑成功', true);
    }

}
