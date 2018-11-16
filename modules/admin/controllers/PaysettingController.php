<?php

namespace app\modules\admin\controllers;

use yii\db\Query;
use app\modules\member\models\PaySetting;

class PaysettingController extends \yii\web\Controller {

    public function actionIndex() {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
//            \Yii::$app->request->
            $paySetting = PaySetting::findOne(1);
            $paySetting->ali_app_id = $post["ali_app_id"];
            $paySetting->ali_merchant_private_key = $post["ali_merchant_private_key"];
            $paySetting->ali_charset = $post["ali_charset"];
            $paySetting->ali_sign_type = $post["ali_sign_type"];
            $paySetting->ali_gatewayUrl = $post["ali_gatewayUrl"];
            $paySetting->ali_alipay_public_key = $post["ali_alipay_public_key"];
            $paySetting->ali_switch = $post["ali_switch"];
            $paySetting->wx_switch = $post["wx_switch"];
            $paySetting->wx_APPID = $post["wx_APPID"];
            $paySetting->wx_MCHID = $post["wx_MCHID"];
            $paySetting->wx_KEY = $post["wx_KEY"];
            $paySetting->wx_APPSECRET = $post["wx_APPSECRET"];
            $paySetting->wxapp_switch = $post["wxapp_switch"];
            $paySetting->wxapp_APPID = $post["wxapp_APPID"];
            $paySetting->wxapp_MCHID = $post["wxapp_MCHID"];
            $paySetting->wxapp_KEY = $post["wxapp_KEY"];
            move_uploaded_file($_FILES["wx_SSLCERT"]["tmp_name"], \Yii::$app->basePath . "/web/upload/" . $_FILES["wx_APPSECRET"]["name"]);
            move_uploaded_file($_FILES["wx_SSLKEY"]["tmp_name"], \Yii::$app->basePath . "/web/upload/" . $_FILES["wxapp_switch"]["name"]);
            move_uploaded_file($_FILES["wxapp_SSLCERT"]["tmp_name"], \Yii::$app->basePath . "/web/upload/" . $_FILES["file"]["name"]);
            move_uploaded_file($_FILES["wxapp_SSLKEY"]["tmp_name"], \Yii::$app->basePath . "/web/upload/" . $_FILES["file"]["name"]);
            if ($paySetting->validate()) {
                $ret = $paySetting->save();
                if ($ret !== false) {
                    return $this->jsonResult(0, "修改成功");
                } else {
                    return $this->jsonResult(2, "修改失败");
                }
            } else {
                return $this->jsonResult(2, "数据错误", $paySetting->getFirstErrors());
            }
        }
        $data = (new Query())->select("*")->from("pay_setting")->where(["pay_setting_id" => 1])->one();

        return $this->render("index", ["data" => $data]);
    }

}
