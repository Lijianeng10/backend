<?php

namespace app\modules\cron\controllers;

use yii;
use yii\web\Controller;
use app\modules\common\helpers\WechatTool;
use app\modules\common\services\ApiSysService;
use app\modules\common\helpers\Commonfun;

/**
 * Default controller for the `index` module
 */
class NoticeController extends Controller
{
    /**
     * 核对用户金额微信通知
     */
    public function actionCheckDoubleAmount(){
        $data = json_encode(["type"=>"get_diff_all_funds"]);
        $checkAmount = ApiSysService::getCustSettleInfo($data);
        if(empty($checkAmount)){
            return true;
        }
        $errorInfo ="\n";
        foreach ($checkAmount["data"] as $k=>$v){
            $errorInfo.=$v["cust_no"].' ; '.$v["all_funds"].' ; '.$v["all_funds_php"]." | ";
        }
        $remark ="请尽快处理！";
        Commonfun::sysAlert('金额核对通知', '用户金额有误通知', $errorInfo, "待处理", $remark);
    }
}