<?php

namespace app\modules\cron\controllers;

use app\modules\common\models\Bussiness;
use app\modules\tools\helpers\SmsTool;
use yii\web\Controller;

class BussinessController extends Controller {

    public $defaultAction = 'main';


    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex() {
        echo 'this is /cron/bussiness/index';
        die;
    }

    /**
     * 说明:定时通知流量商--低于警报金额时
     * @author chenqiwei
     * @date 2018/7/6 上午10:16
     * @param
     * @return
     */
    public function actionNoticeMoney(){
        $bussList = Bussiness::find()->select(['name','bussiness.cust_no','notice_tel','notice_money','uf.able_funds'])
            ->leftJoin('user_funds uf','uf.cust_no = bussiness.cust_no')
            ->where(['bussiness.status'=>1])->asArray()->all();
        $smsTool = new SmsTool();
        $redis = \Yii::$app->redis;
        $sendArr = [];
//print_r($bussList);die;
        foreach ($bussList as $buss){
            if($buss['able_funds']<$buss['notice_money']){//如果检测到小于通知余额，则发送短信
                if(!empty($buss['notice_tel'])){
                    //发送短信
                    $content = "{$buss['name']},您好！您在咕啦体育的可用余额({$buss['able_funds']})，已不足{$buss['notice_money']}元，请及时充值！";
                    $ret = $redis->incr('bussiness_notice:'.$buss['cust_no']);
                    if($ret>3){
                        continue;
                    }else{
                        $smsTool->sendSms($buss['notice_tel'],$content);
                        $sendArr[] = $buss['cust_no'].':'.$buss['notice_tel'];
                    }
                }
            }else{//如果大于则删除统计
                $redis->del('bussiness_notice:'.$buss['cust_no']);
            }
        }
        return $this->jsonResult(600,'发送成功',$sendArr);
    }

}
