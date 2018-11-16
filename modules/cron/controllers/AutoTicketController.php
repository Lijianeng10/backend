<?php

namespace app\modules\cron\controllers;

use app\modules\common\models\LotteryOrder;
use app\modules\lottery\models\AutoOutOrder;
use function GuzzleHttp\Psr7\build_query;
use Yii;
use yii\web\Controller;
use app\modules\tools\helpers\Zmf;
use app\modules\common\models\ZmfOrder;
use app\modules\common\services\KafkaService;

class AutoTicketController extends Controller {

    /**
     * 说明:主动查询竞彩出票结果接口 LV->LPS
     * @author chenqiwei
     * @date 2018/1/10 下午2:50
     * @param
     * @return json
     */
    public function actionAutoOrderCheck(){
        $request = \Yii::$app->request;
        $order_code = $request->get('order_code','');
        $source = $request->get('source','');
        if($order_code){
            $data = [
                'order_code'=>$order_code,//玩法代码
            	'source'=>$source,//来源
            ];
            KafkaService::addQue('AutoOrderCheck', $data);
            return $this->jsonResult(600,'队列添加成功',$data);
        }

        $startTime='2018-02-28 11:07:33';
        $where=[];
        array_push($where, 'and');
        array_push($where, "create_time >'{$startTime}'");
        array_push($where, 'status = 1');
        $maxTime = date('Y-m-d H:i:s', time() - 600);
        array_push($where, "create_time < '{$maxTime}'");
        $unCallBack=AutoOutOrder::find()->where($where)->limit(100)->asArray()->all();
        foreach ($unCallBack as $v){
        	$data = [
        		'order_code'=>$v['out_order_code'],//玩法代码
        		'source'=>$v['source'],//
        	];
        	KafkaService::addQue('AutoOrderCheck', $data);
        }
		echo 'complete';
		exit;
       // $this->jsonResult(600,'succ', $ret);

    }

}

