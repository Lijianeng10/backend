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

class ZmfController extends Controller {

//    /**
//     * 说明:竞彩投注接口  LV->LPS
//     * @author chenqiwei
//     * @date 2018/1/10 下午2:18
//     * @param
//     * @return json
//     */
//    public function actionZmf1000()
//    {
////        echo phpinfo();die;
//        $request = \Yii::$app->request;
////        $id = $request->post('id');
////        $code = $request->post('code');
////        if(!$id){
////            $this->jsonResult(100,'Order Code not null');
////        }
//        $data = [
//            'lotteryId'=>"D14",//玩法代码
//            'issue'=>'18022',//期号（竞彩玩法忽略此字段）
//            'records'=>[
//                'record'=>[
//                    'id'=>'GLCAUTO18021213AI0000001',//投注序列号(不可重复)订单编号
//                    'lotterySaleId'=>'0',//销售代码(竞彩自由过关，过关方式以^分开)
//                    'freelotterySaleId'=>0,//1:自由过关 0:非自由过关
////                    'phone'=>'13960774169',//手机号（可不填）
////                    'idCard'=>'350681199002095254',//身份证号（可不填）
//                    'code'=>"3*3*3*3*3*3*0*3*1*1*0*0*1*1^",//注码。投注内容
//                    'money'=>200,//金额
//                    'timesCount'=>1, //倍数
//                    'issueCount'=>1,//期数
//                    'investCount'=>1,//注数
//                    'investType'=>0,//投注方式
//                ]
//            ]
//        ];
//        $zmfObj = new Zmf();
//        $ret = $zmfObj->to1000($data);
//
//        $this->jsonResult(600,'succ', $ret);
//    }

    /**
     * 说明:主动查询竞彩出票结果接口 LV->LPS
     * @author chenqiwei
     * @date 2018/1/10 下午2:50
     * @param
     * @return json
     */
    public function actionZmf1019(){
        $request = \Yii::$app->request;
        $messageId = $request->get('messageId','');
        if($messageId){
            $data = [
                'messageId'=>$messageId,//玩法代码
            ];
            KafkaService::addQue('ZmfOrderCheck', $data);
            return $this->jsonResult(600,'队列添加成功',$messageId);
        }

        $startTime='2018-02-28 11:07:33';
        $where=[];
        array_push($where, 'and');
        array_push($where, "create_time >'{$startTime}'");
        array_push($where, 'status = 0');
        $maxTime = date('Y-m-d H:i:s', time() - 600);
        array_push($where, "create_time < '{$maxTime}'");
        array_push($where, "version = 1500");
        $unCallBack=ZmfOrder::find()->where($where)->limit(100)->asArray()->all();
        foreach ($unCallBack as $v){
        	$data = [
        		'messageId'=>$v['messageId'],//玩法代码
        	];
        	KafkaService::addQue('ZmfOrderCheck', $data);
        }
		echo 'complete';
		exit;
       // $this->jsonResult(600,'succ', $ret);

    }
    /**
     * 
     */
    public function actionTestRd(){
    	var_dump(new \RdKafka\Conf());
    }

    public function actionZmf1002(){
        $request = \Yii::$app->request;
        $messageId = $request->post('messageId');
        if(!$messageId){
            return '参数缺失';
        }
        $data = [
            'messageId'=>$messageId,//玩法代码
        ];
        $zmfObj = new Zmf();
        $ret = $zmfObj->to1002($data);

        $this->jsonResult(600,'succ', $ret);
    }

    public function actionCheckWinMoney(){
        $orders = LotteryOrder::find()
            ->select(['lottery_order_code','auto_type','zmf_award_money'])
//            ->leftJoin()
            ->where(['lottery_order.auto_type'=>2,'status' =>4,'zmf_award_money'=>0])
            ->limit(10)
            ->asArray()
            ->all();
        $ret=[];
        foreach ($orders as $order){
            //加入队列
            $ret[] = $this->updateWinMoney($order['lottery_order_code']);
        }
        return $this->jsonResult(600,'succ',$ret);
    }

    public function updateWinMoney($lotteryOrderCode){
        $db = \Yii::$app->db;
        $messageIds = AutoOutOrder::find()
            ->select(['out_order_id','auto_out_order.order_code','zmf_order.messageId'])
            ->leftJoin('zmf_order','zmf_order.order_code = auto_out_order.out_order_code')
            ->where(['auto_out_order.order_code'=>$lotteryOrderCode])
            ->asArray()
            ->all();
        $sql = "";
        $totalMoney =0;
        foreach ($messageIds as $messageId) {
            $data = [
                'messageId'=>$messageId['messageId'],//玩法代码
            ];
            $zmfObj = new Zmf();
            $ret = $zmfObj->to1002($data);
            if($ret['records']){//如果成功返回
                $money=$ret['records']['record']['bonusValue'];
                $sql .= "update auto_out_order set zmf_award_money = {$money} where out_order_id = {$messageId['out_order_id']};";
                $totalMoney += $money;
            }
        }
        $db->createCommand($sql)->execute();
        $totalMoney = $totalMoney/100;
        $db->createCommand("update lottery_order set zmf_award_money = {$totalMoney} where lottery_order_code = '{$lotteryOrderCode}'")->execute();
        return ['lottery_order_code'=>$lotteryOrderCode,'zmf_award_money'=>$totalMoney];
    }

}

