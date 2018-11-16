<?php

namespace app\modules\member\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\db\Exception;
use yii\data\ActiveDataProvider;
use app\modules\member\models\Gift;
use app\modules\common\models\CouponsDetail;
use app\modules\member\models\ExchangeRecord;
use app\modules\member\models\ExgiftRecord;
use app\modules\member\models\IntergalRecord;
use app\modules\member\helpers\Constants;
use app\modules\common\models\UserGlCoinRecord;
use app\modules\common\models\UserFunds;
use app\modules\member\models\User;

class ExchangeCheckController extends Controller {

    /**
     * 兑换审核
     * @return 
     * 
     */
    public function actionIndex() {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $get = $request->get();
        $exCode = $request->get('ex_code', '');
        $userInfo = $request->get('user_info', '');
        $review_status = $request->get('review_status', '');
        $start = $request->get('start_date', '');
        $end = $request->get('end_date', '');
//        andWhere(['agent_code'=>$session['agent_code']])->
        $exData =(new Query())->select("e.*,u.user_name,u.user_tel")
            ->from("exchange_record as e")
            ->leftJoin("user as u","u.cust_no = e.cust_no")
            ->where(["e.exch_type"=>2]);
         if ($exCode != '') {
             $exData = $exData->andWhere(["e.exch_code"=>$exCode]);
         }
        if ($userInfo != '') {
            $exData = $exData->andWhere(["or",["u.user_tel" => $userInfo], ["u.cust_no" => $userInfo], ["u.user_name" => $userInfo]]);
        }
        if ($review_status != 0) {
            $exData = $exData->andWhere(["e.review_status"=>$review_status]);
        }
        if ($start != '') {
            $exData = $exData->andWhere(['>=', 'e.create_time', $start . ' 00:00:00']);
        }
        if ($end != '') {
            $exData = $exData->andWhere(['<=', 'e.create_time', $end . ' 23:59:59']);
        }
        $exData =$exData->orderBy('e.exchange_record_id desc');
        $provider = new ActiveDataProvider([
            'query' => $exData,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['data' => $provider, 'get' => $get]);
    }

    /**
     * 兑换订单审核
     * @return json
     * @throws Exception
     * 
     */
    public function actionReview() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            if (!isset($get['exchange_id'])) {
                echo '参数错误';
                return $this->redirect('/member/exchange-check/index');
            }
            $order = ExchangeRecord::find()->where(['exchange_record_id' => $get['exchange_id']])->asArray()->one();
            if (empty($order)) {
                echo '该兑换订单不存在，请返回原页面';
                return $this->redirect('/member/exchange-check/index');
            }
            return $this->render('review', ['exchange_id' => $get['exchange_id']]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $exId = $request->post('exchange_id', '');
            $authen = $request->post('ex_authen', '');
            $remark = $request->post('authen_remark', '');
            if ($exId == '' || $authen == '') {
                return $this->jsonResult(109, '参数有误,请重新操作');
            }
            //兑换订单记录
            $data = ExchangeRecord::find()->where(['exchange_record_id' => $exId])->one();
            $userInfo = User::find()->where(["cust_no"=>$data->cust_no])->one();
            $allInt = $data->exch_value;
            $custNo = $data->cust_no;
            $format = date('Y-m-d H:i:s');
            if (empty($data)) {
                return $this->jsonResult(109, '该兑换订单不存在');
            }
            if ($remark != '') {
                $data->review_remark = $remark;
            }
            $data->review_status = $authen;
            $data->opt_id = $session['admin_id'];
            $data->modify_time = $format;
            //审核通过
            if ($authen == 2) {
                $userFunds = UserFunds::find()->select("user_glcoin")->where(["cust_no" => $custNo])->asArray()->one();
                if ($userFunds["user_glcoin"] < $allInt) {
                    return $this->jsonResult(109, '该会员咕币余额不够兑换礼品！');
                }
                $db = Yii::$app->db;
                $trans = $db->beginTransaction();
                try {
                    if ($data->validate('review_status')) {
                        $updateId = $data->save();
                        if ($updateId == false) {
                            throw new Exception('审核失败');
                        }
                    } else {
                        throw new Exception('审核验证失败');
                    }
                    $detail = ExgiftRecord::find()->select('gift_nums,gift_code')->where(['exchange_id' => $data->exchange_record_id])->asArray()->all();
                    //更新礼品表数据、如果是优惠券还需更新优惠券表数据
                    foreach ($detail as $val) {
                        $nums = $val['gift_nums'];
                        $gift = Gift::find()->select("in_stock")->where(["gift_code" => $val["gift_code"]])->asArray()->one();
                        if ($gift["in_stock"] < $nums) {
                            return $this->jsonResult(109, '该礼品库存不够兑换！');
                        }
                        //更新礼品表数据
                        $giftUp = "UPDATE gift SET in_stock = in_stock - {$nums}, exchange_nums = exchange_nums + {$nums} , opt_id = '" . $session['admin_id'] . "', modify_time = '" . $format . "' where gift_code = '" . $val['gift_code'] . "'";
                        $updateGift = $db->createCommand($giftUp)->execute();
                        if (!$updateGift) {
                            throw new Exception('失败，礼品表数据更新失败');
                        }
                        //判断是否为优惠券
                        $giftInfo=Gift::find()->select("type,batch")->where(["gift_code"=>$val['gift_code']])->asArray()->one();
                        if($giftInfo["type"]==1){
                            //更新优惠券主表
                            $upCoupons = "UPDATE coupons SET send_num = send_num + {$nums}, opt_id = '" . $session['admin_id'] . "' where batch = '" . $giftInfo['batch'] . "'";
                            $updateRes = $db->createCommand($upCoupons)->execute();
                            if (!$updateRes) {
                                throw new Exception('失败，优惠券主表数据更新失败');
                            }
                            //更新优惠券明细表(可能一次兑换多张优惠券)
                            $sendAry = [];
                            for($i=0;$i<$nums;$i++){
                                $couponsDetail=CouponsDetail::find()->where(["coupons_batch"=>$giftInfo['batch'],"send_user"=>NUll,"send_status"=>1,"status"=>1])->orderBy("coupons_detail_id")->one();
                                $couponsDetail->send_status=2;
                                $couponsDetail->send_user=$data->cust_no;
                                $couponsDetail->send_time=$format;
                                $couponsDetail->use_status=1;
                                $res=$couponsDetail->save();
                                if ($res == false) {
                                    throw new Exception('失败，优惠券明细表更新失败');
                                }
                                array_push($sendAry,$couponsDetail->coupons_no);
                            }
                            //更新礼品兑换记录表
                            $exgiftAry=[
                                "send_gift"=>json_encode($sendAry),
                            ];
                            $exgiftRes = ExgiftRecord::updateAll($exgiftAry, ['exchange_id'=>$data->exchange_record_id,'gift_code' =>$val["gift_code"]]);
                            if($exgiftRes == false) {
                                throw new Exception('失败,礼品兑换记录更新失败');
                            }
                        }elseif($giftInfo["type"]==2){
                            //前台下单接口
                            $surl = \Yii::$app->params['userDomain']."/api/user/userclub/send-dlt-order?num=".$nums."&custNo=".$userInfo->cust_no."&userId=".$userInfo->user_id;
                            $ret = \Yii::sendCurlGet($surl);
                            $sendAry = [];
                            array_push($sendAry,$ret["result"]);
                            //更新礼品兑换记录表
                            $exgiftAry=[
                                "send_gift"=>json_encode($sendAry),
                            ];
                            $exgiftRes = ExgiftRecord::updateAll($exgiftAry, ['exchange_id'=>$data->exchange_record_id,'gift_code' =>$val["gift_code"]]);
                            if($exgiftRes == false) {
                                throw new Exception('失败,礼品兑换记录更新失败');
                            }
                        }
                    }
                    $glCoinRecord = new UserGlCoinRecord();
                    $glCoinRecord->order_code = $data->exch_code;
                    $glCoinRecord->user_id = $userInfo->user_id;
                    $glCoinRecord->cust_no = $userInfo->cust_no;
                    $glCoinRecord->type =2;
                    $glCoinRecord->transaction_type = 1;
                    $glCoinRecord->exchange_type = 1;
                    $glCoinRecord->coin_value = $allInt;
                    $glCoinRecord->totle_balance = intval($userFunds["user_glcoin"] - $allInt);
                    $glCoinRecord->remark = "会员兑换礼品成功，消费咕币：[".$allInt."]";
                    $glCoinRecord->status = 1;
                    $glCoinRecord->create_time = $format;
                    if ($glCoinRecord->validate()) {
                        $res = $glCoinRecord->save();
                        if ($res == false) {
                            throw new Exception('咕币变化记录新增失败');
                        }
                    } else {
                        throw new Exception('咕币变化记录表验证失败');
                    }
                    $sql = "UPDATE user_funds set user_glcoin = user_glcoin - {$allInt},modify_time = '" . $format . "' where user_id = {$userInfo->user_id}";
                    $updateId = $db->createCommand($sql)->execute();
                    if ($updateId == false) {
                        throw new Exception('会员资金表咕币账户更新失败');
                    }
                    $trans->commit();
                    return $this->jsonResult(600, '审核成功');
                } catch (Exception $ex) {
                    $trans->rollBack();
                    return $this->jsonResult(109, $ex->getMessage());
                }
                //审核不通过
            }elseif($authen==3){
                if ($data->validate('review_status')) {
                       $updateId = $data->save();
                       if ($updateId == false) {
                            return $this->jsonResult(109, '审核失败,请重新操作');
                       }else{
                           return $this->jsonResult(600, '审核成功');
                       }
                   } else{
                        return $this->jsonResult(109, '表单验证未通过');
                   }
            }
        } else {
            echo '操作错误';
            return $this->redirect('/member/list/index');
        }
    }

    /**
     * 查看兑换订单
     * @return 
     */
    public function actionView() {
        if (Yii::$app->request->isGet) {
            $exType = Constants::EX_TYPE;
            $get = Yii::$app->request->get();
            if (!isset($get['exchange_id'])) {
                echo '参数错误';
                return $this->redirect('/member/exchange-check/index');
            }
            $query = new Query;
            $data = $query->select('e.*,u.province,u.city,u.area,u.address,u.agent_name,u.user_id,u.user_name,u.user_tel')
                    ->from('exchange_record as e')
                    ->leftJoin('user as u', 'u.cust_no = e.cust_no')
                    ->where(['exchange_record_id' => $get['exchange_id']])
                    ->one();
            $detail = ExgiftRecord::find()->where(['exchange_id' => $get['exchange_id']])->asArray()->all();
            if (empty($data)) {
                echo '该订单表不存在，请返回原页';
                return $this->redirect('/member/exchange-check/index');
            }
            return $this->render('view', ['data' => $data, 'detail' => $detail, 'exType' => $exType]);
        } else {
            echo '操作错误';
            return $this->redirect('/member/exchange-check/index');
        }
    }

}
