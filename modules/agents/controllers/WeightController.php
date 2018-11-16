<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\modules\common\models\Store;
use app\modules\common\helpers\Constants;
use app\modules\agents\models\Agents;
use app\modules\common\models\ChannelWeight;
use app\modules\common\models\Bussiness;



class WeightController extends Controller {

    public function actionIndex() {
        $agent = [];
        $agent[""] = '请选择';
        $agent[0]="咕啦自营";
        $agent['TG'] = "推广";
        $agentsData = Agents::find()->select("agents_id,agents_name,agents_code")->where(["pass_status"=>3,"use_status"=>1])->asArray()->all();
        foreach ($agentsData as $k=>$v){
            $agent[$v["agents_id"]] =$v["agents_name"];
        }
        //渠道商
        $bussinessData = Bussiness::find()->select("name,cust_no")->where(["status"=>1])->asArray()->all();
        foreach ($bussinessData as $key=>$val){
            $agent[$val["cust_no"]] =$val["name"];
        }
        return $this->render('index', ['agents' => $agent]);
    }
    /**
     * 获取权重信息
     */
    public function actionGetWeightInfo(){
        $request = \Yii::$app->request;
        $channel_id = $request->post('id', '');
        $province = $request->post('province', '');
        //查找所有门店信息
        $store = Store::find()->select("store_code,store_name,province,city,company_id,store_type")
            ->where(["cert_status"=>3,"status"=>1]);
        if(!empty($province)){
            $store = $store->andWhere(["province"=>$province]);
        }
        $store=$store->orderBy("company_id desc")
            ->asArray()
            ->all();
        //查找到权重记录
        $weight = ChannelWeight::find()->where(["channel_id"=>$channel_id])->asArray()->all();
        if(!empty($weight)){
            foreach ($store as $k=>$v) {
                foreach ($weight as $key =>$val){
                    if($val["store_code"]==$v["store_code"]){
                        $store[$k]["weight"] = $val["weight"];
                        $store[$k]["check"] = 1;
                        break;
                    }else{
                        $store[$k]["weight"]= 0;
                        $store[$k]["check"]=0;
                    }
                }
            }
        }else{
            foreach ($store as $k=>$v) {
                $store[$k]["weight"]= 0;
                $store[$k]["check"]=0;
            }
        }

        return $this->jsonResult(600, '获取成功',$store);

    }

    public function actionSetOutLotWeight() {
        $request = \Yii::$app->request;
        $agents = $request->post('agents', '');
        $weightData = $request->post('weightData', '');
        if ($agents=="") {
            return $this->jsonResult(109, '请选择要配置的代理商！！');
        }
//        if(empty($weightData)){
//            return $this->jsonResult(109, '请选择要配置的门店！！');
//        }
        $trans = \Yii::$app->db->beginTransaction();
        try {
            //不传权重数据说明是清空删除所有数据
            $del = ChannelWeight::deleteAll(['channel_id' => $agents]);
            if ($del === false) {
                throw new Exception('数据错误！！');
            }
            if(!empty($weightData)){
                $info = [];
                $key = ['channel_id','channel_code','channel_name', 'store_code', 'store_province','weight','create_time'];
                foreach ($weightData as $val) {
                    $arr = explode(':', $val);
                    //查找门店信息
                    $store = Store::find()->select("store_code,store_name,province,city")->where(["store_code"=> $arr[0]])->asArray()->one();
                    //查找代理商信息或者合作商信息(咕啦编号长度为10)
                    if($agents!='0'){
                        if(strlen($agents)==10){
                            $bussinessInfo = Bussiness::find()->where(["cust_no"=>$agents])->asArray()->one();
                            $agentsInfo["agents_id"]=$agents;
                            $agentsInfo["agents_code"]=$agents;
                            $agentsInfo["agents_name"]=$bussinessInfo["name"];
                        } elseif ($agents == 'TG') {
                            $agentsInfo["agents_id"]='TG';
                            $agentsInfo["agents_code"]="TG";
                            $agentsInfo["agents_name"]="推广";
                        }else{
                            $agentsInfo = Agents::find()->where(["agents_id"=>$agents])->asArray()->one();
                        }
                    }else{
                        $agentsInfo["agents_id"]=0;
                        $agentsInfo["agents_code"]="GL";
                        $agentsInfo["agents_name"]="咕啦";
                    }
                    $info[] = [$agentsInfo["agents_id"],$agentsInfo["agents_code"],$agentsInfo["agents_name"],$arr[0],$store["province"]."-".$store["city"],$arr[1],date("Y-m-d H:i:s")];
                }
                $insertData = \Yii::$app->db->createCommand()->batchInsert('channel_weight', $key, $info)->execute();
                if ($insertData === false) {
                    throw new Exception('数据写入失败');
                }
            }
            $trans->commit();
            return $this->jsonResult(600, '数据写入成功');
        } catch (Exception $ex) {
            $trans->rollBack();
            return $this->jsonResult(109, $ex->getMessage());
        }
    }

}
