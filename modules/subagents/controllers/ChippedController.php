<?php

namespace app\modules\subagents\controllers;

use yii\web\Controller;
use app\modules\lottery\models\LotteryOrder;
use yii\data\ArrayDataProvider;
use app\modules\lottery\models\Lottery;
use Yii;
use yii\db\Query;
use app\modules\lottery\models\Schedule;
use app\modules\lottery\helpers\Constant;
use app\modules\lottery\models\LotteryAdditional;
use app\modules\member\models\User;
use app\modules\lottery\models\FootballFourteen;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\LanSchedule;
use app\modules\lottery\models\OptionalSchedule;
use app\modules\lottery\models\BettingDetail;
use app\modules\lottery\models\Programme;
use app\modules\common\services\TogetherService;

class ChippedController extends Controller {

    /**
     * 合买订单列表
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $session = Yii::$app->session;
        //判断当前登录用户是代理商还是咕啦内部用户,代理商是本账号还是所属操作账号
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
                $custNo = User::find()->select("cust_no")->where(["agent_code"=>$session["admin_name"]])->asArray()->all();
            }else{
                $custNo = User::find()->select("cust_no")->where(["agent_code"=>$session["agent_code"]])->asArray()->all();
            }
        }elseif($session["type"]==0){
            $custNo = User::find()->select("cust_no")->where(["agent_code"=>'gl00015788'])->asArray()->all();
        }
        $custAry=[];
        foreach ($custNo as $k=>$v){
            $custAry[$k]=$v["cust_no"];
        }
        $query = Lottery::find()->select("lottery_code");
        $detail = (new Query())->select(["programme.*", 's.store_name', 's.phone_num', 'sd.consignee_name', 'u.user_name'])
                ->from("programme")
                //只能查看自己名下的会员发起的合买订单
                ->where(["in", "programme.expert_no", $custAry])
                ->leftJoin('store as s', 's.store_code = programme.store_no and s.user_id=programme.store_id')
                ->leftJoin('store_detail as sd', 'sd.store_id = s.store_id ')
                ->leftJoin('user as u', 'u.cust_no = programme.expert_no');
        if (isset($get["programme_code"]) && !empty($get["programme_code"])) {
            $detail = $detail->andWhere(["like", "programme.programme_code", $get['programme_code']]);
        }
        if (isset($get["user_info"]) && !empty($get["user_info"])) {
            $detail = $detail->andWhere(["or", ["u.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $detail = $detail->andWhere(["programme.status" => $get["status"]]);
        }
        if (isset($get["lottery_code"]) && !empty($get["lottery_code"])) {
            $detail = $detail->andWhere(["programme.lottery_code" => $get["lottery_code"]]);
        }
        if (isset($get["security"]) && !empty($get["security"])) {
            $detail = $detail->andWhere(["programme.security" => $get["security"]]);
        }
        $detail = $detail->orderBy("programme.create_time desc");
        $lottery = new lottery();
        $lotteryNames = $lottery->getLotterynamelist();
        $lotteryNames[0] = "请选择";
        $orderStatus = [
            "" => "请选择",
            "1" => "未发布",
            "2" => "招募中",
            "3" => "处理中",
            "4" => "待开奖",
            "5" => "未中奖",
            "6" => "中奖",
            "7" => "未满员撤单",
            "8" => "方案失败",
            "9" => "过点撤销",
            "10" => "拒绝出票",
            "11" => "未上传方案撤单"
        ];
        $security=[
             "" => "请选择",
            "1" => "完全公开",
            "2" => "跟单公开",
            "3" => "截止后公开",
        ];
        $pageSize = isset($get["pageSize"]) ? $get["pageSize"] : 15;
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        return $this->render('index', ["data" => $data, "lotteryNames" => $lotteryNames, "orderStatus" => $orderStatus,"security"=>$security, "get" => $get,]);
    }
    /**
     * 方案详情页面
     */
    public function actionReaddetail(){
        $this->layout = false;
        return $this->render("readdetail");
    }

    /**
     * 获取方案详情
     * @return type
     */
    public function actionGetProgrammeDetail() {
        $request = Yii::$app->request;
        $programmeCode = $request->post('programme_code', '');
        $listType = $request->post('list_type', '');
        if ($listType == '') {
            return $this->jsonResult(100, '参数缺失');
        }
        if ($programmeCode != '') {
            $where['programme_code'] = $programmeCode;
        }
//        $where['cust_no'] = $custNo;
//        if (!empty($custNo)) {
//            $withData = ProgrammeUser::find()->select(['sum(bet_money) as bet_money', 'sum(buy_number) as buy_number', 'create_time'])->where($where)->asArray()->one();
//            if (!empty($withData)) {
//                $isWith = 1;
//            } else {
//                $isWith = 0;
//            }
//        } else {
//            $isWith = 0;
//        }
        if ($listType == 1) {
            $data = TogetherService::getListDetail($programmeCode);
        } elseif ($listType == 2) {
            $data = TogetherService::getSubscribeDetail($pId, $userId, $isWith, $programmeCode);
        }
        if ($data['code'] != 600) {
            return $this->jsonResult($data['code'], $data['msg']);
        }
//        $detailList = $data['data'];
//        if (empty($withData)) {
//            $detailList['with_number'] = 0;
//            $detailList['with_money'] = 0;
//            $detailList['with_time'] = '';
//        } else {
//            $detailList['with_number'] = $withData['buy_number'];
//            $detailList['with_money'] = $withData['bet_money'];
//            $detailList['with_time'] = $withData['create_time'];
//        }
//        $dataList['data'] = $detailList;
//        print_r($data);
//        die;
        return $this->jsonResult(600, '方案详情', $data["data"]);
    }

}
