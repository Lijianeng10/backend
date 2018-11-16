<?php

namespace app\modules\member\controllers;

use Yii;
use yii\web\Controller;
use app\modules\member\models\User;
use app\modules\member\models\ExchangeRecord;
use app\modules\member\models\ExgiftRecord;
use yii\db\Query;
use app\modules\member\models\Gift;
use app\modules\agents\services\IAgentsService;

class RedeemController extends Controller {
//    private $agentsService;
//    public function __construct($id, $module, $config = [], IAgentsService $agentsService) {
//        $this->agentsService = $agentsService;
//        parent::__construct($id, $module, $config);
//    }
    /**
     * 积分兑换
     * @return type
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $data = [];
//        where(['agent_code' => $session['agent_code']])->
        $timer = date("Y-m-d H:i:s");
        $giftList = Gift::find()
            ->where([">=","in_stock",1])
            ->andWhere(["and",[">","end_date",$timer],["status"=>1],["<>","gift_glcoin",0]])
            ->asArray()
            ->all();
        $data['opt_name'] = $session['admin_name'];
        $data['ex_time'] = date('Y-m-d H:i:s');
        $data['ex_order'] = 'GLGIFT'.date('YmdHis').substr(time(),-2).substr(microtime(),2,4);
        return $this->render('index', ['exData' => $data, 'gift' => $giftList]);
    }
    
    /**
     * 兑换会员基础信息,礼品基础信息
     * @return json
     */
    public function actionSearch() {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $member = [];
            $userInform = $request->post('user_inform', '');
            $giftInform = $request->post('gift_inform', '');
            $session = Yii::$app->session;
            if ($userInform != '') {
                $statusArr = [3, 4, 5];
                $member = (new Query())->select('u.*,f.user_glcoin,f.all_funds')
                        ->from('user as u')
                        ->leftJoin('user_funds as f', 'f.cust_no = u.cust_no')
                        ->andWhere(["or",["u.cust_no"=>$userInform],["u.user_name"=>$userInform],["u.user_tel"=>$userInform]])
                        ->one();
                if(empty($member)){
                     return $this->jsonResult(109, '未查询到该会员记录！');
                }
//                $javaGetRealName = $this->agentsService->javaGetRealName($member->cust_no);
//                var_dump($javaGetRealName);
//                die;
                $member["paytotal"]=(new Query())->select("u.cust_no,sum(l.bet_money) pay")
                        ->from("lottery_order as l")
                        ->leftJoin("user as u","u.cust_no=l.cust_no")
                        ->where(["in","l.status",$statusArr])
                        ->andWhere(["or",["u.cust_no"=>$userInform],["u.user_name"=>$userInform],["u.user_tel"=>$userInform]])
                        ->one();
                return $this->jsonResult(600, '', $member);
            }
            $timer = date("Y-m-d H:i:s");
            if ($giftInform != '') {
                $giftList = Gift::find()->where([">=","in_stock",1])->andWhere(["or",["gift_name"=>$giftInform],["gift_code"=>$giftInform]])->andWhere([">","end_date",$timer])->asArray()->one();
                return $this->jsonResult(600, '', $giftList);
            } else {               
                $giftList = Gift::find()->where([">=","in_stock",1])->andWhere([">","end_date",$timer])->asArray()->all();
                return $this->jsonResult(600, '', $giftList);
            }
        } else {
            return $this->redirect('/member/redeem/index');
        }
    }

    /**
     * 保存兑换信息
     * @return json
     * @throws Exception
     */
    public function actionAddExgift() {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $userId = $request->post('user_id', '');
            $exTime = $request->post('ex_time', '');
            $exOrder = $request->post('ex_order', '');
            $optName = $request->post('opt_name', '');
            $exGift = $request->post('ex_gift', '');
            $allNums = 0;
            $payInt = 0;
            if ($userId == '' || $exTime == '' || $exOrder == '' || $optName == '' || $exGift == '') {
                return $this->jsonResult(109, '参数有误');
            }
            foreach ($exGift as $val) {
                $giftInfo=  Gift::find()->select("in_stock")->where(["gift_code"=>$val["gift_code"]])->asArray()->one();
                if($val['ex_nums']>$giftInfo["in_stock"]){
                    return $this->jsonResult(109, '礼品库存不足以兑换');
                }
                $allNums += $val['ex_nums'];
                $payInt += $val['all_int'];
            }
            $userData = (new Query())->select('u.*,f.user_glcoin')
                    ->from('user as u')
                    ->leftJoin('user_funds as f', 'f.cust_no = u.cust_no')
                    ->where(['u.user_id' => $userId])
                    ->one();
            if (empty($userData)) {
                return $this->jsonResult(109, '该会员不存在');
            }
            if ($payInt > $userData['user_glcoin']) {
                return $this->jsonResult(109, '该会员咕币不够兑换已选礼品');
            }
            $exRecord = new ExchangeRecord();
            $exRecord->exch_code = $exOrder;
            $exRecord->platform = 1;//订单来源平台 1 咕啦 2 电信
            $exRecord->cust_no = $userData['cust_no'];
            $exRecord->pay_type = 1;//兑换类型：1 咕币 2 积分
            $exRecord->exch_nums = $allNums;
            $exRecord->exch_value = $payInt;
            $exRecord->exch_type = 2;//兑换途径 1:会员俱乐部兑换;2:后台兑换；
            $exRecord->order_status = 2;
            $exRecord->review_status = 1;
            $exRecord->create_time = date('Y-m-d H:i:s');
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try {
                if ($exRecord->validate()) {
                    $exId = $exRecord->save();
                    if ($exId == false) {
                        throw new Exception('兑换订单新增失败');
                    } 
                } else {
                    throw new Exception('兑换订单验证失败');
                }
                
                foreach ($exGift as $val){
                    $detail = new ExgiftRecord();
                    $detail->exchange_id = $exRecord->exchange_record_id;
                    $detail->exch_code = $exOrder;
                    $detail->gift_code = $val['gift_code'];
                    $detail->gift_name = $val['gift_name'];
                    $detail->gift_nums = $val['ex_nums'];
                    $detail->exch_int = $val['need_int'];
                    $detail->all_int = $val['all_int'];
                    $detail->create_time = date('Y-m-d H:i:s');
                    if($detail->validate()){
                        $exgiftId = $detail->save();
                        if($exgiftId == false){
                            throw new Exception('详情表新增失败');
                        }
                    }else {
                        throw new Exception('详情验证失败');
                    }
                }
                $trans->commit();
                return $this->jsonResult(600, '预申请成功');
            } catch (Exception $ex) {
                $trans->rollBack();
                return $this->jsonResult(109, $ex);
            }
        } else {
            return $this->redirect('/member/redeem/index');
        }
    }

}
