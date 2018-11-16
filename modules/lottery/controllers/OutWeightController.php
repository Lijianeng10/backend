<?php

namespace app\modules\lottery\controllers;

use yii\web\Controller;
use app\modules\common\helpers\Constants;
use app\modules\common\models\WeightLotteryOut;
use app\modules\common\models\Lottery;
use yii\base\Exception;
use app\modules\admin\models\AutoOutThird;
use Yii;
use app\modules\common\models\Store;

class OutWeightController extends Controller {

    public function actionIndex() {
        $lotteryData = Lottery::find()->select(['lottery_code', 'lottery_name'])->where(['result_status' => 1])->indexBy('lottery_code')->asArray()->all();
        $weightData = WeightLotteryOut::find()->select(['lottery_code', 'out_code', 'weight'])->asArray()->all();
        $autoOutThird = AutoOutThird::find()->select(['third_code', 'third_name', 'out_lottery'])->asArray()->all();
        $storeData = Store::find()->select(['store_code third_code', 'store_name third_name', 'sale_lottery out_lottery'])->where(['status' => 1, 'company_id' => 1])->asArray()->all();
        $outThird = array_merge($autoOutThird, $storeData);
        
        $lotteryList[0] = '请选择';
        $list = [];
        foreach ($lotteryData as $lottery) {
            $lotteryList[$lottery['lottery_code']] = $lottery['lottery_name'];
            foreach ($outThird as $auto) {
                $outLottery = explode(',', $auto['out_lottery']);
                if (in_array($lottery['lottery_code'], $outLottery)) {
                    $weightNum = 0;
                    $outCode = '';
                    if (!empty($weightData)) {
                        foreach ($weightData as $weight) {
                            if ($weight['lottery_code'] == $lottery['lottery_code'] && $weight['out_code'] == $auto['third_code']) {
                                $weightNum = $weight['weight'];
                                $outCode = $weight['out_code'];
                            }
                        }
                    }
                    $list[$lottery['lottery_code']][] = ['third_code' => $auto['third_code'], 'third_name' => $auto['third_name'], 'weight' => $weightNum, 'out_code' => $outCode];
                }
            }
        }
        return $this->render('index', ['lotteryList' => $lotteryList, 'lotteryData' => $list]);
    }

    public function actionSetOutLotWeight() {
        $request = \Yii::$app->request;
        $lotteryCode = $request->post('lotteryCode', '');
        $weightData = $request->post('weightData', '');
        if (empty($lotteryCode)) {
            return $this->jsonResult(109, '请选择要配置彩种！！');
        }
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $del = WeightLotteryOut::deleteAll(['lottery_code' => $lotteryCode]);
            if ($del === false) {
                throw new Exception('数据错误！！');
            }
            $info = [];
            $key = ['lottery_code', 'out_code', 'weight'];
            foreach ($weightData as $val) {
                $arr = explode(':', $val);
                $info[] = [$lotteryCode, $arr[0], $arr[1]];
            }
            $insertData = \Yii::$app->db->createCommand()->batchInsert('weight_lottery_out', $key, $info)->execute();
            if ($insertData === false) {
                throw new Exception('数据写入失败');
            }
            $trans->commit();
            return $this->jsonResult(600, '数据写入成功');
        } catch (Exception $ex) {
            $trans->rollBack();
            return $this->jsonResult(109, $ex->getMessage());
        }
    }

}
