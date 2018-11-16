<?php

namespace app\modules\member\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\member\models\Gift;
use app\modules\member\models\UserLevels;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\member\models\GiftCategory;
use app\modules\common\models\Coupons;
use app\modules\common\helpers\Constants;

class GiftListController extends Controller {

    /**
     * 礼品列表
     * @return 
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $get = $request->get();
        $giftName = $request->get('gift_name', '');
        $cate = $request->get('cate_name', '');
        $status = $request->get('status', '');
        $type = $request->get('gift_type', '');
        //分类
        $data = GiftCategory::find()->orderBy('gift_category_id')->asArray()->all();
        $tree = array();
        $tree[0] = "请选择";
        $this->childtree($data, $tree, 0, "");
        //状态
        $statusAry = [
            "" => "请选择",
            "1" => "在线",
            "2" => "下线"
        ];
        $giftType = Constants::GIFT_TYPE;
        $query = new Query;
        $dataList = $query->select('g.*,c.category_name')
                ->from('gift as g');
        if (!empty($cate) && $cate != '') {
            $Ary = $this->chooseType($cate);
            $dataList = $dataList->andWhere(["in", "c.gift_category_id", $Ary]);
        }
        if ($giftName != '') {
            $dataList = $dataList->andWhere(["or", ["g.gift_name" => $giftName], ["g.gift_code" => $giftName]]);
        }
        if ($status != '') {
            $dataList = $dataList->andWhere(["g.status" => $status]);
        }
        if (!empty($type)) {
            $dataList = $dataList->andWhere(["g.type" => $type]);
        }
        $dataList = $dataList->leftJoin('gift_category as c', 'c.gift_category_id = g.gift_category')
                ->orderBy('g.gift_id desc');
        $provider = new ActiveDataProvider([
            'query' => $dataList,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => ['gift_id'],
            ],
        ]);
        return $this->render('index', ['dataList' => $provider, 'cateList' => $tree, "get" => $get, "status" => $statusAry,'gift_type'=>$giftType]);
    }

    /**
     * 新增礼品
     * @return json
     */
    public function actionAddgift() {
        if (Yii::$app->request->isGet) {
            //礼品类型
            $giftType = Constants::GIFT_TYPE;
            //礼品标识
            $mark = [
                "0" => "无标识",
                "1" => "新品",
                "2" => "热门",
            ];
            //礼品分类
            $data = GiftCategory::find()->orderBy('gift_category_id')->asArray()->all();
            $tree = array();
            $tree[0] = "请选择";
            $this->childtree($data, $tree, 0, "");
            //用户等级
            $userLevel = UserLevels::find()->select("user_level_id,level_name")->indexBy('user_level_id')->asArray()->all();
            $levelAry = array();
            $levelAry[0] = "无等级限制";
            foreach ($userLevel as $k => $v) {
                $levelAry[$k] = $v["level_name"];
            }
            //优惠券批次:类型为用户兑换类，不在兑换礼品表的，未过期的，有剩余数量的才可以被添加
            $timer = date("Y-m-d H:i:s");
            $coupons = Coupons::find()->select("batch")
                        ->where(["application_type" => 2])
                        ->andWhere("numbers > send_num")
                        ->andWhere([">","end_date",$timer])
                        ->asArray()
                        ->all();
            $batch = array();
            $batch[0] = "请选择";
            foreach ($coupons as $k => $v) {
                $giftRes = Gift::find()->select("batch")->where(["batch" =>$v["batch"]])->asArray()->one();
                if(empty($giftRes)){
                   $batch[$k + 1] = $v["batch"];  
                }
            }
            return $this->render('addgift', ['giftType' => $giftType, 'mark' => $mark, 'cateList' => $tree, "levelAry" => $levelAry, "batch" => $batch]);
        } elseif (Yii::$app->request->isAjax) {
            $format = 'Y-m-d H:i:s';
            $request = Yii::$app->request;
            $post = Yii::$app->request->post();
            $type = $request->post('giftType', '');
            $name = $request->post('gift_name', '');
            $stock = $request->post('in_stock', '');
            $cate = $request->post('gift_cate',0);
            $gift_level = $request->post('gift_leave',0);
            $gift_glcoin = $request->post('gift_glcoin',0);
            $gift_integral = $request->post('gift_integral',0);
            $remark = $request->post('gift_remark', '');
            $startdate = $request->post('startdate', '');
            $enddate = $request->post('enddate', '');
            $subtitle = $request->post('subtitle', '');
            if ($type == "" || $name == "" || $cate == '0' || $stock == ''|| $remark == "" || $startdate == "" || $enddate == "") {
                return $this->jsonResult(109, '请将参数填写完整');
            }
            if($gift_glcoin==0&&$gift_integral==0){
                return $this->jsonResult(109, '请至少填写一种兑换价格');
            }
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/gift/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $pic1name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$pic1name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $giftPicture = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传缩略图', '');
            }
            
            if (isset($_FILES['upfile2'])) {
                $file = $_FILES['upfile2'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/gift/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $pic2name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$pic2name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $giftPicture2 = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传详情图', '');
            }

            $session = Yii::$app->session;
            //生成礼品简码
            $giftInfo = Gift::find()->select("max(gift_id) Mid")->asArray()->one();
            if (empty($giftInfo["Mid"])) {
                $giftInfo["Mid"] = 1;
            } else {
                $giftInfo["Mid"] = $giftInfo["Mid"] + 1;
            }
            $No = str_pad($giftInfo["Mid"], 6, "0", STR_PAD_LEFT);
            $code = "GLGIFT" . $No;
            $giftModel = new Gift();
            $giftModel->gift_name = $name;
            $giftModel->gift_code = $code;
            $giftModel->gift_category = $cate;
            $giftModel->type = $type;
            if ($type == 1) {
                $giftRes = Gift::find()->select("batch")->where(["batch" => $request->post('batch')])->asArray()->one();
                if (!empty($giftRes)) {
                    return $this->jsonResult(109, '该批次已经是礼品，请勿重复添加');
                } else {
                    $giftModel->batch = $request->post('batch');
                }
                //礼品兑换时间应在优惠券有效期内（即活动结束时间小于优惠券结束时间）
                $couponsRes=Coupons::find()->select("start_date,end_date")->where(["batch" => $request->post('batch')])->asArray()->one();
                if(strtotime($startdate. " 00:00:00")>=strtotime($couponsRes["end_date"])){
                     return $this->jsonResult(109, '礼品活动开始时间应小于优惠券截止时间');
                }elseif(strtotime($enddate . " 23:59:59")>=strtotime($couponsRes["end_date"])){
                    return $this->jsonResult(109, '礼品活动结束时间应小于优惠券截止时间');
                }
                
            }
            $giftModel->gift_level = $gift_level;
            $giftModel->gift_glcoin = $gift_glcoin;
            $giftModel->gift_integral = $gift_integral;
            $giftModel->in_stock = $stock;
            $giftModel->agent_code = $session['agent_code'];
            $giftModel->agent_name = $session['admin_name'];
            $giftModel->gift_remark = $remark;
            $giftModel->gift_picture = $giftPicture;
            $giftModel->gift_picture2 = $giftPicture2;
            $giftModel->opt_id = $session['admin_id'];
            $giftModel->create_time = date($format);
            $giftModel->start_date = $startdate . " 00:00:00";
            $giftModel->end_date = $enddate . " 23:59:59"; 
            $giftModel->subtitle = $subtitle;
            if ($giftModel->validate()) {
                $giftId = $giftModel->save();
                if ($giftId == false) {
                    return $this->jsonResult(109, '礼品新增失败');
                }
                return $this->jsonResult(600, '礼品新增成功');
            } else {
                return $this->jsonResult(109, '新增数据验证失败', $giftModel->errors);
            }
        }
    }

    /**
     * 获取优惠券批次信息:优惠券名称，库存
     */
    public function actionGetCouponsDetail() {
        $post = Yii::$app->request->post();
        if (empty($post["batch"])) {
            return $this->jsonResult(109, '参数缺失');
        }
        $coupons = Coupons::find()->select("coupons_name,numbers,send_num,start_date,end_date")->where(["batch" => $post["batch"]])->asArray()->one();
        return $this->jsonResult(600, '', $coupons);
    }

    /**
     * 编辑礼品
     * @return json
     */
    public function actionEditgift() {
        if (Yii::$app->request->isGet) {
            $giftType = Constants::GIFT_TYPE;
            //用户等级
            $userLevel = UserLevels::find()->select("user_level_id,level_name")->indexBy('user_level_id')->asArray()->all();
            $levelAry = array();
            $levelAry[0] = "无等级限制";
            foreach ($userLevel as $k => $v) {
                $levelAry[$k] = $v["level_name"];
            }
            $data = GiftCategory::find()->orderBy('gift_category_id')->asArray()->all();
            $tree = array();
            $tree[0] = "请选择";
            $this->childtree($data, $tree, 0, "");
            $request = Yii::$app->request;
            $giftId = $request->get('gift_id', '');
            if ($giftId == '') {
                echo '参数错误';
                return $this->redirect('/member/gift-list/index');
            }
            $giftData = Gift::find()->where(['gift_id' => $giftId])->asArray()->one();
            return $this->render('editgift', ['cateList' => $tree, 'data' => $giftData,'giftType'=>$giftType,"levelAry" => $levelAry,]);
        } elseif (Yii::$app->request->isAjax) {
            $session = Yii::$app->session;
            $request = Yii::$app->request;
            $post = Yii::$app->request->post();
            $giftId = $request->post('gift_id', '');
            if ($giftId == '') {
                return $this->jsonResult(109, '参数有误,请返回原页面');
            }
//            $name = $request->post('gift_name', '');
//            $cate = $request->post('gift_cate', '0');
//            $stock = $request->post('in_stock', '');
//            $gift_glcoin = $request->post('gift_glcoin', '');
            $remark = $request->post('gift_remark', '');
            if ($remark == "") {
                return $this->jsonResult(109, '请填写礼品介绍');
            }
            $giftModel = Gift::find()->where(['gift_id' => $giftId])->one();
            //缩略图
            if (isset($_FILES['upfile'])&&$_FILES['upfile']!="Undefined") {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/gift/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            }else{
                $giftInfo = Gift::find()->where(["gift_id"=>$giftId])->one();
                $picUrl=$giftInfo->gift_picture;
            }
            //详情图
            if (isset($_FILES['upfile2'])&&$_FILES['upfile2']!="Undefined") {
                $file = $_FILES['upfile2'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/gift/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $giftPicture2 = $path['result']['ret_path'];
            }else{
                $giftInfo = Gift::find()->where(["gift_id"=>$giftId])->one();
                $giftPicture2 = $giftInfo->gift_picture2;
            }
            $updateData = [
                "gift_picture"=>$picUrl,
                "gift_picture2"=>$giftPicture2,
                "gift_remark"=>$remark,
                "opt_id"=>$session['admin_id'],
                "modify_time"=>date('Y-m-d H:i:s'),
            ];
            $res = Gift::updateAll($updateData,["gift_id"=>$giftId]);
            if ($res) {
                return $this->jsonResult(600, '礼品编辑成功');
            }else{
                return $this->jsonResult(109, '礼品编辑失败');
            }
        }
    }

    /**
     * 删除礼品
     * @return json
     */
    public function actionDelgift() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/gift-list/index');
        }

        $request = Yii::$app->request;
        $giftId = $request->post('gift_id', '');

        if ($giftId == '') {
            return $this->jsonResult(109, '参数有误', '');
        }
        $giftRes=Gift::find()->select("status")->where(['gift_id' => $giftId])->asArray()->one();
        if($giftRes["status"]==1){
            return $this->jsonResult(109, '线上礼品不允许删除，如需删除请先下线', '');
        }
        $result = Gift::deleteAll(['gift_id' => $giftId]);
        if ($result != false) {
            return $this->jsonResult(600, '删除成功', '');
        } else {
            return $this->jsonResult(109, '删除失败', '');
        }
    }

    /**
     * 礼品分类生成子集效果
     */
    public function childtree($info, &$tree, $pid = 0, $str) {
        $str.="--";
        if (!empty($info)) {
            foreach ($info as $k => &$v) {
                if ($v['parent_id'] == $pid) {
                    $tree[$v["gift_category_id"]] = $str . $v["category_name"];
                    $this->childtree($info, $tree, $v["gift_category_id"], $str);
                    unset($info[$k]);
                }
            }
        }
    }

    /**
     * 筛选类别
     */
    public function chooseType($cId) {
        $categoryInfo = GiftCategory::find()->select("gift_category_id,parent_id")->where(["gift_category_id" => $cId])->asArray()->one();
        $newAry = [];
        if ($categoryInfo["parent_id"] == 0) {
            array_push($newAry, $categoryInfo["gift_category_id"]);
            $childInfo = GiftCategory::find()->select("gift_category_id")->where(["parent_id" => $categoryInfo["gift_category_id"]])->asArray()->all();
            if (!empty($childInfo)) {
                foreach ($childInfo as $v) {
                    array_push($newAry, $v["gift_category_id"]);
                }
            }
        } else {
            array_push($newAry, $categoryInfo["gift_category_id"]);
        }
        return $newAry;
    }

    /**
     * 修改礼品上下线状态
     * @return json
     */
    public function actionEditStatus() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/gift-list/index');
        }
        $request = Yii::$app->request;
        $giftId = $request->post('gift_id', '');
        $status = $request->post('status', '');
        if ($giftId == "" || $status == "") {
            return $this->jsonResult(2, '参数有误', '');
        }
        $result = Gift::updateAll(['status' => $status], ['gift_id' => $giftId]);
        if ($result != false) {
            return $this->jsonResult(600, '状态修改成功', '');
        } else {
            return $this->jsonResult(109, '状态修改失败', '');
        }
    }
    /**
     * 查看礼品信息
     */
    public function actionReadGift(){
       if (Yii::$app->request->isGet) {
            $this->layout=false;
            $data = GiftCategory::find()->orderBy('gift_category_id')->asArray()->all();
            $tree = array();
            $tree[0] = "请选择";
            $this->childtree($data, $tree, 0, "");
            $request = Yii::$app->request;
            $giftId = $request->get('gift_id', '');
            if ($giftId == '') {
                echo '参数错误';
                return $this->redirect('/member/gift-list/index');
            }
            $giftData = Gift::find()->select("gift.*,c.category_name")
                    ->where(['gift_id' => $giftId])
                    ->leftJoin('gift_category as c', 'c.gift_category_id = gift.gift_category')
                    ->asArray()
                    ->one();
            if(!empty($giftData["batch"])){
                $giftData["coupons"]=Coupons::find()->where(["batch"=>$giftData["batch"]])->asArray()->one();
            }
            return $this->render('readgift', ['cateList' => $tree, 'data' => $giftData]);
        } 
    }

}
