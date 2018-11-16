<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\agents\models\Store;
use app\modules\agents\models\StoreDetail;
use app\modules\agents\models\StoreOperator;
//use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\tools\helpers\Toolfun;
use app\modules\agents\models\User;
use app\modules\agents\models\UserFollow;
use app\modules\agents\services\IAgentsService;
use app\modules\agents\services\AgentsService;
use app\modules\common\models\TicketDispenser;
use app\modules\common\helpers\PublicHelpers;
use app\modules\common\models\PayRecord;
use app\modules\common\models\UserFunds;

class StoresController extends \yii\web\Controller {

    private $agentsService;

    public function __construct($id, $module, $config = [], IAgentsService $agentsService) {
        $this->agentsService = $agentsService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 门店信息列表
     * @return 
     */
    public function actionIndex() {
        $this->enableCsrfValidation = false;
        $get = \Yii::$app->request->get();
        $query = (new Query())->select("store.consignment_type,store.business_status,store.store_code,store.cust_no,store.store_name,store.phone_num,store.telephone,store.province,store.city,store.area,store.store_type,store.create_time,store.cert_status,store.status,store.store_id,user_funds.all_funds,user_funds.able_funds,user_funds.ice_funds,user_funds.no_withdraw,store.company_id, sd.consignee_name,store.invite_status,store.is_service_fee")
                ->from("store")
                ->join("left join", "user_funds", "user_funds.cust_no=store.cust_no")
                ->leftJoin('store_detail as sd', 'sd.store_id = store.store_id')
                ->leftJoin("user as u", "u.cust_no = store.cust_no")
                ->where(["in", "store.status", [1, 2]]);
        if (isset($get["store_info"]) && !empty($get["store_info"])) {
            $query = $query->andWhere("(store.store_code like '%{$get['store_info']}%' or store.store_name like '%{$get['store_info']}%' or store.cust_no like '%{$get['store_info']}%' or sd.consignee_name like '%{$get['store_info']}%')");
        }
        if (isset($get["store_type"]) && !empty($get["store_type"])) {
            $query = $query->andWhere(["store.store_type" => $get["store_type"]]);
        }
        if (isset($get["province"]) && !empty($get["province"])) {
            $query = $query->andWhere(["store.province" => $get["province"]]);
        }
        if (isset($get["info"]) && !empty($get["info"])) {
            $query = $query->andWhere("(store.phone_num like '%{$get['info']}%' or store.cust_no like '%{$get['info']}%' or u.user_name like '%{$get['info']}%' )");
        }
        if (isset($get["cert_status"]) && !empty($get["cert_status"])) {
            $query = $query->andWhere(["store.cert_status" => $get["cert_status"]]);
        }
        if (isset($get["startdate"]) && !empty($get["startdate"])) {
            $query = $query->andWhere([">", "store.create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (isset($get["enddate"]) && !empty($get["enddate"])) {
            $query = $query->andWhere(["<", "store.create_time", $get["enddate"] . " 23:59:59"]);
        }
        if (isset($get["company_id"]) && !empty($get["company_id"])) {
            if ($get["company_id"] == 1) {
                $query = $query->andWhere(["store.company_id" => $get["company_id"]]);
            } elseif ($get["company_id"] == 2) {
                $query = $query->andWhere(["or", ["store.company_id" => null], ["store.company_id" => 0]]);
            }
        }
        if (isset($get["store_sta"]) && !empty($get["store_sta"])) {
            $query = $query->andWhere(["store.status" => $get["store_sta"]]);
        }
        if (isset($get["business_status"]) && $get["business_status"] != "") {
            $query = $query->andWhere(["store.business_status" => $get["business_status"]]);
        }
        if (isset($get["telephone"]) && !empty($get["telephone"])) {
            $query = $query->andWhere(["store.telephone" => $get["telephone"]]);
        }
        if (isset($get["consignment_type"]) && !empty($get["consignment_type"])) {
            $query = $query->andWhere(["store.consignment_type" => $get["consignment_type"]]);
        }
        if (isset($get["is_service_fee"]) && !empty($get["is_service_fee"])) {
            $query = $query->andWhere(["store.is_service_fee" => $get["is_service_fee"]]);
        }
        $query = $query->orderBy("store.create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }

    /**
     * 门店编码唯一性验证
     * @return bool
     */
    public function actionValidate() {
        $post = \Yii::$app->request->post();
        if (isset($post['store_code']) && !empty($post['store_code'])) {
            $query = Store::find()->where(["store_code" => $post['store_code']]);

            if (isset($post['store_id'])) {
                $query = $query->andWhere(["<>", "store_id", $post['store_id']]);
            }
            $storeInfo = $query->one();
            if ($storeInfo != null) {
                echo $this->jsonResult(2, "门店编号不唯一", $storeInfo);
                exit();
            } else {
                return true;
            }
        }
    }

    /**
     * 删除门店
     * @return json
     */
    public function actionDeleteStore() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            $storeData = Store::findOne($post['store_id']);
            $delf = UserFollow::deleteAll(['store_no' => $storeData->cust_no]);
            if ($delf === false) {
                throw new Exception('关注列表删除失败');
            }
            $user = User::findOne(['user_id' => $storeData->user_id]);
            if (!$storeData->delete()) {
                throw new Exception($storeData->firstErrors);
            }
            StoreDetail::findOne(["store_id" => $post['store_id']])->delete();
            if (!empty($user)) {
                $user->user_type = 1;
                $user->modify_time = date('Y-m-d H:i:s');
                if (!$user->save()) {
                    throw new Exception('门店会员修改失败');
                }
            }
            $tran->commit();
            return $this->jsonResult(0, "删除成功", "");
        } catch (\yii\db\Exception $e) {
            return $this->jsonResult(2, "删除失败", $e->getMessage());
        }
    }

    /**
     * 编辑保存门店
     * @return json
     */
    public function actionSavestore() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            $store = Store::findOne($post['store_id']);
            $store->store_code = $post['store_code'];
            $store->store_name = $post['store_name'];
            $store->phone_num = $post['phone_num'];
            $store->telephone = $post['telephone'];
            $store->email = $post['email'];
            $store->province = $post['province'];
            $store->city = $post['city'];
            $store->area = isset($post['area']) ? $post['area'] : "";
            $store->address = $post['address'];
            $store->store_type = $post['store_type'];
            (isset($post["store_img_change"]) && !empty($post["store_img_change"])) ? $store->store_img = $this->uploadImg("store_img") : "";
            $store->support_bonus = $post['support_bonus'];
            $store->open_time = $post['open_time'];
            $store->close_time = $post['close_time'];
            $store->contract_start_date = $post['contract_start_date'];
            $store->contract_end_date = $post['contract_end_date'];
            $store->opt_id = \Yii::$app->session["admin_id"];
            $store->modify_time = date("Y-m-d H:i:s");
            if ($store->validate()) {
                $ret = $store->save();
                if ($ret === false) {
                    $tran->rollBack();
                    return $this->jsonResult(2, "修改失败");
                }
            } else {
                $tran->rollBack();
                return $this->jsonResult(2, "验证错误", $store->getFirstErrors());
            }
            $storeDetail = StoreDetail::findOne(["store_id" => $store->store_id]);
            $storeDetail->consignee_name = $post['consignee_name'];
            $storeDetail->consignee_card = $post['consignee_card'];
            $storeDetail->sports_consignee_code = $post['sports_consignee_code'];
            $storeDetail->welfare_consignee_code = $post['welfare_consignee_code'];
            $storeDetail->company_name = $post['company_name'];
            $storeDetail->business_license = $post['business_license'];
            $storeDetail->operator_name = $post['operator_name'];
            $storeDetail->operator_card = $post['operator_card'];
            $storeDetail->old_owner_name = $post['old_owner_name'];
            $storeDetail->old_owner_card = $post['old_owner_card'];
            $storeDetail->now_owner_name = $post['now_owner_name'];
            $storeDetail->now_owner_card = $post['now_owner_card'];
            $storeDetail->opt_id = \Yii::$app->session["admin_id"];
            $storeDetail->remark = $post['remark'];
            (isset($post["consignee_img_change"]) && !empty($post["consignee_img_change"])) ? $storeDetail->consignee_img = $this->uploadImg("consignee_img") : "";
            (isset($post["consignee_card_img1_change"]) && !empty($post["consignee_card_img1_change"])) ? $storeDetail->consignee_card_img1 = $this->uploadImg("consignee_card_img1") : "";
            (isset($post["consignee_card_img2_change"]) && !empty($post["consignee_card_img2_change"])) ? $storeDetail->consignee_card_img2 = $this->uploadImg("consignee_card_img2") : "";
            (isset($post["consignee_card_img3_change"]) && !empty($post["consignee_card_img3_change"])) ? $storeDetail->consignee_card_img3 = $this->uploadImg("consignee_card_img3") : "";
            (isset($post["consignee_card_img4_change"]) && !empty($post["consignee_card_img4_change"])) ? $storeDetail->consignee_card_img4 = $this->uploadImg("consignee_card_img4") : "";
            (isset($post["old_owner_card_img1_change"]) && !empty($post["old_owner_card_img1_change"])) ? $storeDetail->old_owner_card_img1 = $this->uploadImg("old_owner_card_img1") : "";
            (isset($post["old_owner_card_img2_change"]) && !empty($post["old_owner_card_img2_change"])) ? $storeDetail->old_owner_card_img2 = $this->uploadImg("old_owner_card_img2") : "";
            (isset($post["business_license_img_change"]) && !empty($post["business_license_img_change"])) ? $storeDetail->business_license_img = $this->uploadImg("business_license_img") : "";
            (isset($post["competing_img_change"]) && !empty($post["competing_img_change"])) ? $storeDetail->competing_img = $this->uploadImg("competing_img") : "";
            (isset($post["football_img_change"]) && !empty($post["football_img_change"])) ? $storeDetail->football_img = $this->uploadImg("football_img") : "";
            (isset($post["sports_nums_img_change"]) && !empty($post["sports_nums_img_change"])) ? $storeDetail->sports_nums_img = $this->uploadImg("sports_nums_img") : "";
            (isset($post["sports_fre_img_change"]) && !empty($post["sports_fre_img_change"])) ? $storeDetail->sports_fre_img = $this->uploadImg("sports_fre_img") : "";
            (isset($post["north_single_img_change"]) && !empty($post["north_single_img_change"])) ? $storeDetail->north_single_img = $this->uploadImg("north_single_img") : "";
            (isset($post["welfare_nums_img_change"]) && !empty($post["welfare_nums_img_change"])) ? $storeDetail->welfare_nums_img = $this->uploadImg("welfare_nums_img") : "";
            (isset($post["welfare_fre_img_change"]) && !empty($post["welfare_fre_img_change"])) ? $storeDetail->welfare_fre_img = $this->uploadImg("welfare_fre_img") : "";
            $storeDetail->modify_time = date("Y-m-d H:i:s");
            if ($storeDetail->validate()) {
                $ret = $storeDetail->save();
                if ($ret === false) {
                    $tran->rollBack();
                    return $this->jsonResult(2, "详情修改失败");
                }
            } else {
                $tran->rollBack();
                return $this->jsonResult(2, "详情验证错误", $storeDetail->getFirstErrors());
            }
            $tran->commit();
            return $this->jsonResult(0, "修改成功");
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "抛出错误", $e);
        }
    }

    /**
     * 新增门店、保存
     * @return json
     */
    public function actionAddstore() {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $this->actionValidate();
            $db = \Yii::$app->db;
            $tran = $db->beginTransaction();
            try {
                $store = new Store();
                $store->store_code = $post['store_code'];
                $store->store_name = $post['store_name'];
                $store->phone_num = $post['phone_num'];
                $store->telephone = $post['telephone'];
                $store->email = $post['email'];
                $store->province = $post['province'];
                $store->city = $post['city'];
                $store->area = isset($post['area']) ? $post['area'] : "";
                $store->address = $post['address'];
                $store->store_type = $post['store_type'];
                $store->store_img = $this->uploadImg("store_img");
                $store->cert_status = 1;
                $store->status = 2;
                $store->support_bonus = $post['support_bonus'];
                $store->open_time = $post['open_time'];
                $store->close_time = $post['close_time'];
                $store->contract_start_date = $post['contract_start_date'];
                $store->contract_end_date = $post['contract_end_date'];
                $store->opt_id = \Yii::$app->session["admin_id"];
                $store->modify_time = date("Y-m-d H:i:s");
                $store->create_time = date("Y-m-d H:i:s");
                if ($store->validate()) {
                    $ret = $store->save();
                    if ($ret === false) {
                        $tran->rollBack();
                        return $this->jsonResult(2, "新增失败");
                    }
                } else {
                    $tran->rollBack();
                    return $this->jsonResult(2, "验证错误", $store->getFirstErrors());
                }
                $storeDetail = new StoreDetail();
                $storeDetail->store_id = $store->store_id;
                $storeDetail->consignee_name = $post['consignee_name'];
                $storeDetail->consignee_card = $post['consignee_card'];
                $storeDetail->sports_consignee_code = $post['sports_consignee_code'];
                $storeDetail->welfare_consignee_code = $post['welfare_consignee_code'];
                $storeDetail->company_name = $post['company_name'];
                $storeDetail->business_license = $post['business_license'];
                $storeDetail->operator_name = $post['operator_name'];
                $storeDetail->operator_card = $post['operator_card'];
                $storeDetail->old_owner_name = $post['old_owner_name'];
                $storeDetail->old_owner_card = $post['old_owner_card'];
                $storeDetail->now_owner_name = $post['now_owner_name'];
                $storeDetail->now_owner_card = $post['now_owner_card'];
                $storeDetail->remark = $post['remark'];
                $storeDetail->consignee_img = $this->uploadImg("consignee_img");
                $storeDetail->consignee_card_img1 = $this->uploadImg("consignee_card_img1");
                $storeDetail->consignee_card_img2 = $this->uploadImg("consignee_card_img2");
                $storeDetail->consignee_card_img3 = $this->uploadImg("consignee_card_img3");
                $storeDetail->consignee_card_img4 = $this->uploadImg("consignee_card_img4");
                $storeDetail->old_owner_card_img1 = $this->uploadImg("old_owner_card_img1");
                $storeDetail->old_owner_card_img2 = $this->uploadImg("old_owner_card_img2");
                $storeDetail->business_license_img = $this->uploadImg("business_license_img");
                $storeDetail->competing_img = $this->uploadImg("competing_img");
                $storeDetail->football_img = $this->uploadImg("football_img");
                $storeDetail->sports_nums_img = $this->uploadImg("sports_nums_img");
                $storeDetail->sports_fre_img = $this->uploadImg("sports_fre_img");
                $storeDetail->north_single_img = $this->uploadImg("north_single_img");
                $storeDetail->welfare_nums_img = $this->uploadImg("welfare_nums_img");
                $storeDetail->welfare_fre_img = $this->uploadImg("welfare_fre_img");
                $storeDetail->opt_id = \Yii::$app->session["admin_id"];
                $storeDetail->modify_time = date("Y-m-d H:i:s");
                $storeDetail->create_time = date("Y-m-d H:i:s");
                if ($storeDetail->validate()) {
                    $ret = $storeDetail->save();
                    if ($ret === false) {
                        $tran->rollBack();
                        return $this->jsonResult(2, "详情新增失败");
                    }
                } else {
                    $tran->rollBack();
                    return $this->jsonResult(2, "详情验证错误", $storeDetail->getFirstErrors());
                }
                $tran->commit();
                return $this->jsonResult(0, "新增成功");
            } catch (yii\db\Exception $e) {
                $tran->rollBack();
                return $this->jsonResult(2, "抛出错误", $e);
            }
        } else {
            return $this->render("addstore");
        }
    }

    /**
     * 门店编辑
     * @return
     */
    public function actionEditstore() {
        $get = \Yii::$app->request->get();
        $store = (new Query())->select("store.*,store_detail.*")->from("store")->join("left join", "store_detail", "store.store_id=store_detail.store_id")->where(["store.store_id" => $get["store_id"]])->one();
        return $this->render("editstore", ["data" => $store]);
    }

    /**
     * 查看门店信息
     * @return
     */
    public function actionReadstore() {
        $get = \Yii::$app->request->get();
        $store = (new Query())->select("store.*,store_detail.*")
                ->from("store")
                ->join("left join", "store_detail", "store.store_id=store_detail.store_id")
                ->where(["store.store_id" => $get["store_id"]])
                ->one();
        $oldStore = (new Query())->select("s.cust_no,s.status,s.opt_id,s.modify_time,u.user_name,u.user_tel,sa.nickname")
                ->from('store as s')
                ->leftJoin("user as u", "u.cust_no=s.cust_no")
                ->leftJoin("sys_admin as sa", "sa.admin_id=s.opt_id")
                ->where(["s.store_code" => $store["store_code"]])
                ->andWhere(["s.status" => 3])
                ->all();
        $operator = (new Query())->select("store_operator.*,user.cust_no,user.user_name,user.user_tel")->from('store_operator')->join("left join", "user", "user.user_id=store_operator.user_id")->where(["store_operator.store_id" => $store["store_code"]])->all();
        return $this->render("readstore", ["data" => $store, "oldStore" => $oldStore, "operator" => $operator]);
    }

    /**
     * 图片上传
     * @param string $name
     * @return string
     */
    public function uploadImg($name) {
        $path = "";
        if (isset($_FILES[$name])) {
            $file = $_FILES[$name];
            $ret = Uploadfile::pic_host_upload($file, '/stores_img/');
            $result = json_decode($ret);
            if ($result->code == 600) {
                return $result->result->ret_path;
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    /**
     * 店铺状态修改：启用、禁用
     * @return json
     */
    public function actionStatusChange() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $store = Store::findOne(["store_id" => $post["store_id"]]);
        $user = User::findOne(["cust_no" => $store->cust_no]);
        //启用店铺需要判断当前运营者是否有其他店铺处于营业状态
        if ($post["status"] == 1) {
            $custStore = Store::findAll(["cust_no" => $store->cust_no, "status" => 1]);
            if (!empty($custStore)) {
                return $this->jsonResult(2, "修改失败，当前运营者有其他店铺在营业", "");
            }
            $storeCode = Store::findAll(["store_code" => $store->store_code, "status" => 1]);
            if (!empty($storeCode)) {
                return $this->jsonResult(2, "修改失败，当前店铺处于营业状态", "");
            }
            if ($user->user_type != 3) {
                $editType = User::updateAll(["user_type" => 3, "opt_id" => $session["admin_id"]], ["cust_no" => $store->cust_no]);
                if (!$editType) {
                    return $this->jsonResult(2, "修改失败，用户状态修改失败", "");
                }
            }
        } else {
            if ($user->user_type != 1) {
                $editType = User::updateAll(["user_type" => 1, "opt_id" => $session["admin_id"]], ["cust_no" => $store->cust_no]);
                if (!$editType) {
                    return $this->jsonResult(2, "修改失败，用户状态修改失败", "");
                }
            }
        }
        $store->status = $post["status"];
        $store->opt_id = $session["admin_id"];
        $ret = $store->save();
        if ($ret) {
            AgentsService::syncStore([$store->store_id]);
            return $this->jsonResult(0, "修改成功", "");
        } else {
            return $this->jsonResult(2, "修改失败", "");
        }
    }

    /**
     * 门店审核
     * @return json
     */
    public function actionReviewStore() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $store = Store::findOne(["store_id" => $post["store_id"]]);
            $store->cert_status = $post["cert_status"];
            $store->review_remark = $post["review_remark"];
            $store->opt_id = \Yii::$app->session["admin_id"];
            $store->password = "GL" . $store["store_code"];
            if ($post['cert_status'] == 3) {
                $store->business_status = 1;
            } else {
                $store->business_status = 0;
            }
            $user = User::findOne(['user_id' => $store->user_id]); //->where()->one();
            if (empty($user)) {
                return $this->jsonResult(109, '不是合法会员');
            }
            $userFollow = \app\modules\agents\models\UserFollow::find()->where(['cust_no' => $user->cust_no])->one();
            if (empty($userFollow)) {
                $userFollow = new \app\modules\agents\models\UserFollow;
                $userFollow->cust_no = $user->cust_no;
                $userFollow->store_no = $user->cust_no;
                $userFollow->store_id = $store->store_code;
                $userFollow->create_time = date('Y-m-d H:i:s');
            }
            if ($post['cert_status'] == 3) {
                UserFollow::deleteAll(['cust_no' => $user->cust_no]);
                $storeData = $store->attributes;
                if ($storeData['amap_id']) {
                    $amap = Toolfun::updateLbsAddress($storeData);
                } else {
                    $amap = Toolfun::setLbsAddress($storeData);
                }
                if ($amap['status'] != 1) {
//                    return $this->jsonResult(109, $amap['info'] . '地理位置不可为空');
                    $amap['_id'] = '';
                }
                if (empty($storeData['amap_id'])) {
                    $store->amap_id = $amap['_id'];
                }
                $user->user_type = 3;
                $userFollow->default_status = 2;
                $userFollow->follow_status = 1;
            } else {
                $user->user_type = 4;
                $userFollow->follow_status = 0;
            }
            $user->modify_time = date('Y-m-d H:i:s');
            $userFollow->modify_time = date('Y-m-d H:i:s');
            if (!$user->save()) {
                return $this->jsonResult(109, '保存失败');
            }
            if (!$userFollow->validate()) {
                return $this->jsonResult(109, '关注验证失败');
            }
            if (!$userFollow->save()) {
                return $this->jsonResult(109, '关注失败');
            }
            $ret = $store->save();
            if ($ret) {
                AgentsService::syncStore([$store->store_id]);
                return $this->jsonResult(600, "审核成功", "");
            } else {
                return $this->jsonResult(109, "审核失败", "");
            }
        } else {
            $get = \Yii::$app->request->get();
            $store = Store::findOne(["store_id" => $get["store_id"]]);
            return $this->render("review-store", ["data" => $store]);
        }
    }

    /**
     * 旗舰店认证
     * @return type
     */
    public function actionFlagshipStore() {
        $request = Yii::$app->request;
        $format = date('Y-m-d H:i:s');
        $storeId = $request->post('store_id', '');
        if (empty($storeId)) {
            return $this->jsonResult(100, '参数缺失');
        }
        $store = Store::findOne(['store_id' => $storeId]);
        if ($store['cert_status'] != 3) {
            return $this->jsonResult(109, '该门店还未通过门店认证');
        }
        $store->company_id = $store->company_id == 1 ? 0 : 1;
        $store->modify_time = $format;
        if (!$store->save()) {
            return $this->jsonResult(109, '设置失败');
        }
//        $db = \Yii::$app->db;
//        if ($store->company_id == 1) {
//            $update = "INSERT INTO user_follow (cust_no,store_no,store_id) (select cust_no, '{$store->cust_no}', {$store->store_code} from user a where a.register_from !=3 and NOT EXISTS (select b.cust_no from user_follow b where  a.cust_no = b.cust_no and b.store_id = {$store->store_code}));";
//            $db->createCommand($update)->execute();
//        }
        AgentsService::syncStore([$store->store_id]);
        return $this->jsonResult(600, '设置成功');
    }

    /**
     * 门店更换运营者
     */
    public function actionEditConsignee() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $session = \Yii::$app->session;
            $timer = date("Y-m-d H:i:s");
            $store_id = $post["store_id"];
            $cust_no = $post["cust_no"];
            $consignee_name = $post["consignee_name"];
            $consignee_card = $post["consignee_card"];
            $phone_num = $post["phone_num"];
            $userInfo = User::find()->where(["cust_no" => $cust_no])->asArray()->one();
            $javaStatus = $this->agentsService->javaGetStatus($cust_no);
            if ($javaStatus['code'] != 1) {
                return $this->jsonResult(404, '获取失败,未找到该用户');
            } elseif ($javaStatus['data']["checkStatus"] != 1) {
                return $this->jsonResult(109, '该用户尚未实名认证，请先实名认证');
            } elseif ($userInfo["status"] != 1) {
                return $this->jsonResult(2, '该用户已被禁止使用！');
            }
            $storeInfo = Store::find()->where(["store_id" => $store_id])->asArray()->one();
            //判断被更换的营业者和更换的运营者当前是否还有未结束交易流程的订单存在，如果有不允许更换
            $orderInfo = (new Query())->select("*")->from("lottery_order")->where(["or", ["store_id" => $storeInfo['user_id']], ["store_id" => $userInfo['user_id']]])->andWhere(["or", ["status" => 3], ["status" => 4, "deal_status" => 1]])->all();
            if (!empty($orderInfo)) {
                return $this->jsonResult(2, '运营者还存在未结束交易流程订单！');
            }
            $db = Yii::$app->db;
            $train = $db->beginTransaction();
            try {
                //如果新营运者名下有店铺，更改其店铺状态
                $store = Store::findOne(["cust_no" => $cust_no, "status" => 1]);
                $oldStoreId = '';
                if (!empty($store)) {
                    $store->status = 2;
                    $store->opt_id = $session["admin_id"];
                    $ret = $store->save();
                    if (!$ret) {
                        $train->rollBack();
                        return $this->jsonResult(2, "门店状态改为禁用操作失败");
                    }
                    $oldStoreId = $store->store_id;
                }
                //更改当前店铺营业状态
                $sRes = Store::find()->where(["store_id" => $store_id])->asArray()->one();
                if ($sRes["status"] != 3) {
                    $result = Store::updateAll(["status" => 3, "opt_id" => $session["admin_id"], "modify_time" => $timer], ["store_id" => $store_id]);
                    if (!$result) {
                        $train->rollBack();
                        return $this->jsonResult(2, "门店状态改为更换营运者操作失败");
                    }
                }
                //判断当前用户是否还存在其他在营门店
                $res = Store::find()->where(["cust_no" => $cust_no, "status" => 1])->asArray()->one();
                if (!empty($res)) {
                    $train->rollBack();
                    return $this->jsonResult(2, "新营运者有绑定其他门店");
                }
                //判断门店是否处于在营状态
                $res = Store::find()->where(["store_code" => $sRes["store_code"], "status" => 1])->asArray()->one();
                if (!empty($res)) {
                    $train->rollBack();
                    return $this->jsonResult(2, "当前门店有处于正常营业状态数据");
                }
                //修改用户为店家
                $oldUserInfo = User::find()->where(["cust_no" => $storeInfo['cust_no']])->asArray()->one();
                if ($oldUserInfo["user_type"] != 1) {
                    $oldUser = User::updateAll(["user_type" => 1, "is_operator" => 1, "opt_id" => $session["admin_id"]], ["cust_no" => $oldUserInfo['cust_no']]);
                    if (!$oldUser) {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，原运营者用户类型修改失败");
                    }
                }
                if ($userInfo["user_type"] != 3) {
                    $newUser = User::updateAll(["user_type" => 3, "is_operator" => 1, "opt_id" => $session["admin_id"]], ["cust_no" => $cust_no]);
                    if (!$newUser) {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，新运营者用户类型修改失败");
                    }
                }
                UserFollow::deleteAll(['store_id' => $storeInfo['store_code'], 'cust_no' => $oldUserInfo['cust_no']]);
                UserFollow::deleteAll(['cust_no' => $cust_no]);
                $userFollow = new \app\modules\agents\models\UserFollow;
                $userFollow->cust_no = $cust_no;
                $userFollow->store_no = $cust_no;
                $userFollow->store_id = $storeInfo['store_code'];
                $userFollow->follow_status = 1;
                $userFollow->default_status = 2;
                $userFollow->create_time = date('Y-m-d H:i:s');
                $userFollow->save();
                //如果是操作员更换为运营者，需禁用之前操作员状态
                $optInfo = StoreOperator::find()->where(["user_id" => $userInfo["user_id"]])->asArray()->one();
                if (!empty($optInfo)) {
                    $newRes = StoreOperator::updateAll(["status" => 2, "modify_time" => $timer], ["store_operator_id" => $optInfo["store_operator_id"]]);
                    if (!$newRes) {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，门店操作员状态禁用失败");
                    }
                }

                //判断是否有该店铺该新运营者数据存在
                $storeResult = Store::find()->where(["cust_no" => $cust_no, "store_code" => $sRes["store_code"], "status" => 3])->asArray()->one();
                if (!empty($storeResult)) {
                    $editStoreSta = Store::updateAll(["status" => 1, "opt_id" => $session["admin_id"], "modify_time" => $timer], ["store_id" => $storeResult["store_id"]]);
                    if (!$editStoreSta) {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，运营者更换失败");
                    }
                } else {
                    $store = new Store();
                    $store->cust_no = $cust_no;
                    $store->phone_num = $phone_num;
                    $store->telephone = $phone_num;
                    $store->user_id = $userInfo['user_id'];
                    //原店铺数据
                    $store->store_code = $storeInfo['store_code'];
                    $store->password = $storeInfo['password'];
                    $store->store_name = $storeInfo['store_name'];
                    $store->password = $storeInfo['password'];
                    $store->email = $storeInfo['email'];
                    $store->province = $storeInfo['province'];
                    $store->city = $storeInfo['city'];
                    $store->area = $storeInfo['area'];
                    $store->address = $storeInfo['address'];
                    $store->coordinate = $storeInfo['coordinate'];
                    $store->company_id = $storeInfo['company_id'];
                    $store->store_type = $storeInfo['store_type'];
                    $store->cert_status = $storeInfo['cert_status'];
                    $store->real_name_status = $storeInfo['real_name_status'];
                    $store->review_remark = $storeInfo['review_remark'];
                    $store->status = 1;
                    $store->store_remark = $storeInfo['store_remark'];
                    $store->opt_id = $storeInfo['opt_id'];
                    $store->amap_id = $storeInfo['amap_id'];
                    //门店新记录新增时间
                    $store->modify_time = $timer;
                    $store->create_time = $storeInfo['create_time'];
                    $store->update_time = $timer;
                    $store->support_bonus = $storeInfo['support_bonus'];
                    $store->open_time = $storeInfo['open_time'];
                    $store->close_time = $storeInfo['close_time'];
                    $store->contract_start_date = $storeInfo['contract_start_date'];
                    $store->contract_end_date = $storeInfo['contract_end_date'];
                    $store->store_img = $storeInfo['store_img'];
                    $store->store_qrcode = $storeInfo['store_qrcode'];
                    $store->store_grade = $storeInfo['store_grade'];
                    $store->his_win_nums = $storeInfo['his_win_nums'];
                    $store->his_win_amount = $storeInfo['his_win_amount'];
                    $store->made_nums = $storeInfo['made_nums'];
                    $store->consignment_type = $storeInfo['consignment_type'];
                    $store->sale_lottery = $storeInfo['sale_lottery'];
                    $store->business_status = $storeInfo['business_status'];
                    $store->weight = $storeInfo['weight'];
                    $store->invite_status = $storeInfo['invite_status'];
                    $store->is_service_fee = $storeInfo['is_service_fee'];
                    if ($store->validate()) {
                        $ret = $store->save();
                        if ($ret === false) {
                            $train->rollBack();
                            return $this->jsonResult(2, "失败，门店新增失败");
                        }
                    } else {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，门店表验证错误", $store->getFirstErrors());
                    }
                    $storeDetailInfo = StoreDetail::find()->where(["store_id" => $store_id])->asArray()->one();
                    $storeDetail = new StoreDetail();
                    $storeDetail->store_id = $store->store_id;
                    $storeDetail->cust_no = $cust_no;
                    $storeDetail->consignee_name = $consignee_name;
                    $storeDetail->consignee_card = $consignee_card;
                    //原店铺详细数据
                    $storeDetail->sports_consignee_code = $storeDetailInfo['sports_consignee_code'];
                    $storeDetail->welfare_consignee_code = $storeDetailInfo['welfare_consignee_code'];
                    $storeDetail->company_name = $storeDetailInfo['company_name'];
                    $storeDetail->business_license = $storeDetailInfo['business_license'];
                    $storeDetail->operator_name = $storeDetailInfo['operator_name'];
                    $storeDetail->operator_card = $storeDetailInfo['operator_card'];
                    $storeDetail->old_owner_name = $storeDetailInfo['old_owner_name'];
                    $storeDetail->old_owner_card = $storeDetailInfo['old_owner_card'];
                    $storeDetail->now_owner_name = $storeDetailInfo['now_owner_name'];
                    $storeDetail->now_owner_card = $storeDetailInfo['now_owner_card'];
                    $storeDetail->remark = $storeDetailInfo['remark'];
                    $storeDetail->consignee_img = $storeDetailInfo["consignee_img"];
                    $storeDetail->consignee_card_img1 = $storeDetailInfo["consignee_card_img1"];
                    $storeDetail->consignee_card_img2 = $storeDetailInfo["consignee_card_img2"];
                    $storeDetail->consignee_card_img3 = $storeDetailInfo["consignee_card_img3"];
                    $storeDetail->consignee_card_img4 = $storeDetailInfo["consignee_card_img4"];
                    $storeDetail->old_owner_card_img1 = $storeDetailInfo["old_owner_card_img1"];
                    $storeDetail->old_owner_card_img2 = $storeDetailInfo["old_owner_card_img2"];
                    $storeDetail->business_license_img = $storeDetailInfo["business_license_img"];
                    $storeDetail->competing_img = $storeDetailInfo["competing_img"];
                    $storeDetail->football_img = $storeDetailInfo["football_img"];
                    $storeDetail->sports_nums_img = $storeDetailInfo["sports_nums_img"];
                    $storeDetail->sports_fre_img = $storeDetailInfo["sports_fre_img"];
                    $storeDetail->north_single_img = $storeDetailInfo["north_single_img"];
                    $storeDetail->welfare_nums_img = $storeDetailInfo["welfare_nums_img"];
                    $storeDetail->welfare_fre_img = $storeDetailInfo["welfare_fre_img"];
                    $storeDetail->opt_id = $storeDetailInfo["opt_id"];
                    $storeDetail->modify_time = $storeDetailInfo["modify_time"];
                    $storeDetail->create_time = $storeDetailInfo["create_time"];
                    $storeDetail->update_time = $storeDetailInfo["update_time"];
                    $storeDetail->consignee_img2 = $storeDetailInfo["consignee_img2"];
                    if ($storeDetail->validate()) {
                        $ret = $storeDetail->save();
                        if ($ret === false) {
                            $train->rollBack();
                            return $this->jsonResult(2, "失败，门店详情新增失败");
                        }
                    } else {
                        $train->rollBack();
                        return $this->jsonResult(2, "失败，门店详情表验证错误", $storeDetail->getFirstErrors());
                    }
                }
                $train->commit();
                AgentsService::syncStore([$store->store_id, $oldStoreId]);
                return $this->jsonResult(600, "运营者更换成功");
            } catch (yii\db\Exception $ex) {
                $train->rollBack();
                return $this->jsonResult(109, "失败，抛出错误", $ex->getMessage());
            }
        } else {
            $get = \Yii::$app->request->get();
            $store = (new Query())->select("store_detail.consignee_name,store_detail.consignee_card,store_detail.cust_no,store.phone_num")->from("store_detail")->join("left join", "store", "store_detail.cust_no=store.cust_no")->where(["store_detail.store_id" => $get["store_id"]])->one();
            $store["store_id"] = $get["store_id"];
            return $this->render("edit-consignee", ["data" => $store]);
        }
    }

    /**
     * 查询新运营者信息
     */
    public function actionGetUserInfo() {
        $post = \Yii::$app->request->post();
        $info = $post["userInfo"];
        $user = (new Query())->select("*")->from("user")->where(["or", ["cust_no" => $info], ["user_tel" => $info]])->one();
        if (empty($user)) {
            return $this->jsonResult(109, '不是合法会员,请确保已注册咕啦会员');
        }
        $javaUserAccount = $this->agentsService->javaGetRealName($user["cust_no"]);
        if ($javaUserAccount['code'] != 1) {
            return $this->jsonResult(109, '获取失败,查不到身份信息');
        }
        $user["realName"] = $javaUserAccount["data"]["realName"];
        $user["card"] = $javaUserAccount["data"]["cardNo"];
        return $this->jsonResult(600, '获取成功', $user);
    }

    /**
     * 操作员使用状态修改
     * @return json
     */
    public function actionEditOperatorStatus() {
        $post = \Yii::$app->request->post();
        $store_operator = StoreOperator::findOne(["store_operator_id" => $post["store_operator_id"]]);
        if ($post["status"] == 1) {
            $store = Store::find()->where(["user_id" => $store_operator->user_id])->andWhere(["status" => 1])->asArray()->one();
            if (!empty($store)) {
                return $this->jsonError(2, "修改失败，该操作员属于在营店铺运营者");
            }
        }
        $store_operator->status = $post["status"];
        $ret = $store_operator->save();
        if ($ret) {
            $update = User::updateAll(['is_operator' => 2], ['user_id' => $store_operator->user_id]);
            return $this->jsonResult(600, "修改成功", "");
        } else {
            return $this->jsonResult(2, "修改失败", "");
        }
    }

    /**
     * 店铺营业状态修改：启用、禁用
     * @return json
     */
    public function actionBusinessChange() {
        $post = \Yii::$app->request->post();
        $store = Store::findOne(["store_id" => $post["store_id"]]);
        $store->business_status = $post["business_status"];
        $store->opt_id = \Yii::$app->session["admin_id"];
        $ret = $store->save();
        if ($ret) {
            return $this->jsonResult(0, "修改成功", "");
        } else {
            return $this->jsonResult(2, "修改失败", "");
        }
    }

    public function actionSetWeight() {
        $this->layout = false;
        $request = \Yii::$app->request;
        if (\Yii::$app->request->isPost) {
            $storeCode = $request->post('store_code', '');
            $weight = $request->post('store_weight', 1);
            if (empty($storeCode)) {
                return $this->jsonResult(100, '参数缺失');
            }
            $update = Store::updateAll(['weight' => $weight], ['store_code' => $storeCode]);
            if ($update === FALSE) {
                return $this->jsonResult(109, '修改失败');
            }
            return $this->jsonResult(600, '修改成功', true);
        } else {
            $storeCode = $request->get('store_code', '');
            if (empty($storeCode)) {
                return $this->jsonResult(100, '参数缺失');
            }
            $store = Store::find()->select(['store_code', 'store_name', 'weight'])->where(['store_code' => $storeCode, 'status' => 1])->asArray()->one();
            return $this->render('set-weight', ['data' => $store]);
        }
    }

    /**
     * 门店新增出票机器
     */
    public function actionAddDispenser() {
        $this->layout = false;
        $request = \Yii::$app->request;
        if (\Yii::$app->request->isPost) {
            $storeNo = $request->post('store_no', '');
            $type = $request->post('type', '');
            $dispenser_code = $request->post('dispenser_code', '');
            $pre_out_nums = $request->post('pre_out_nums', '');
            $vender_id = $request->post('vender_id', '');
            $sn_code = $request->post('sn_code', '');
            if (empty($storeNo) || empty($type) || empty($dispenser_code) || empty($pre_out_nums)) {
                return $this->jsonResult(109, '参数缺失');
            }
            $ticket = new TicketDispenser();
            $ticket->type = $type;
            $ticket->dispenser_code = $dispenser_code;
            $ticket->vender_id = $vender_id;
            $ticket->sn_code = $sn_code;
            $ticket->store_no = $storeNo;
            $ticket->pre_out_nums = $pre_out_nums;
            $ticket->mod_nums = $pre_out_nums;
            $ticket->create_time = date("Y-m-d,H:i:s");
            if ($ticket->validate()) {
                $ret = $ticket->save();
                if ($ret === false) {
                    return $this->jsonResult(109, "失败，门店机器新增失败");
                } else {
                    return $this->jsonResult(600, "门店机器新增成功");
                }
            } else {
                return $this->jsonResult(109, "失败，门店机器表验证失败");
            }
        } else {
            $storeCode = $request->get('store_code', '');
            if (empty($storeCode)) {
                return $this->jsonResult(109, '参数缺失');
            }
            $store = Store::find()->select(['store_code', 'store_name'])->where(['store_code' => $storeCode, 'status' => 1])->asArray()->one();
            return $this->render('add-dispenser', ['data' => $store]);
        }
    }

    /**
     * 门店不可提现金额转可提现金额
     */
    public function actionMoneyChange() {
        $post = \Yii::$app->request->post();
        $db = Yii::$app->db;
        $custNo = $post["cust_no"];
        $stick = $post["stick"];
        if (empty($custNo) || empty($stick)) {
            return $this->jsonResult(109, '参数缺失');
        }
        $userFunds = UserFunds::findOne(["cust_no" => $custNo]);
//        print_r($userFunds);
//        print_r($userFunds->all_funds);
//        die;
        $train = $db->beginTransaction();
        try {
            $time = date("Y-m-d H:i:s");
            $payRecord = new PayRecord();
            $payRecord->cust_no = $custNo;
            $payRecord->pay_no = PublicHelpers::getCode("YEBD", "ZC");
            $payRecord->pay_name = "余额";
            $payRecord->way_name = "余额";
            $payRecord->way_type = "YE";
            $payRecord->pay_way = 3;
            $payRecord->pay_money = $stick;
            $payRecord->pay_pre_money = $stick;
            $payRecord->balance = $userFunds->all_funds - $stick;
            $payRecord->pay_type_name = "不可提现金额转可提现金额";
            $payRecord->body = "不可提现金额转可提现金额";
            $payRecord->pay_type = 21;
            $payRecord->status = 1;
            $payRecord->pay_time = $time;
            $payRecord->create_time = $time;
            if ($payRecord->validate()) {
                $res = $payRecord->save();
                if ($res === false) {
                    $train->rollBack();
                    return $this->jsonResult(109, "失败，交易记录新增失败");
                }
            }
            $payRecord = new PayRecord();
            $payRecord->cust_no = $custNo;
            $payRecord->pay_no = PublicHelpers::getCode("YEBD", "ZJ");
            $payRecord->pay_name = "余额";
            $payRecord->way_name = "余额";
            $payRecord->way_type = "YE";
            $payRecord->pay_way = 3;
            $payRecord->pay_money = $stick;
            $payRecord->pay_pre_money = $stick;
            $payRecord->balance = $userFunds->all_funds;
            $payRecord->pay_type_name = "可提现";
            $payRecord->body = "可提现";
            $payRecord->pay_type = 22;
            $payRecord->status = 1;
            $payRecord->pay_time = $time;
            $payRecord->create_time = $time;
            if ($payRecord->validate()) {
                $res = $payRecord->save();
                if ($res === false) {
                    $train->rollBack();
                    return $this->jsonResult(109, "失败，交易记录新增失败");
                }
            }
            //更新用户资金表数据
            $result = UserFunds::updateAll(["no_withdraw" => new Expression('no_withdraw-' . $stick)], ["cust_no" => $custNo]);
            if (!$result) {
                $train->rollBack();
                return $this->jsonResult(109, "失败，用户资金表不可用余额变更失败");
            }
            $train->commit();
            return $this->jsonResult(600, "操作成功");
        } catch (yii\db\Exception $ex) {
            $train->rollBack();
            return $this->jsonResult(109, "操作失败，抛出错误", $ex->getMessage());
        }
    }

    /**
     * 店铺邀请状态修改：启用、禁用
     * @return json
     */
    public function actionInviteChange() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $store = Store::findOne(["store_id" => $post["store_id"]]);
        $store->invite_status = $store->invite_status == 1 ? 2 : 1;
        $store->modify_time = date("Y-m-d H:i:s");
        if (!$store->save()) {
            return $this->jsonResult(109, '设置失败');
        }
        return $this->jsonResult(600, '设置成功');
    }

    /**
     * 店铺收取服务费状态修改：1 收取、2 不收取
     * @return json
     */
    public function actionServiceChange() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $store = Store::findOne(["store_id" => $post["store_id"]]);
        $store->is_service_fee = $store->is_service_fee == 1 ? 2 : 1;
        $store->modify_time = date("Y-m-d H:i:s");
        if (!$store->save()) {
            return $this->jsonResult(109, '设置失败');
        }
        return $this->jsonResult(600, '设置成功');
    }

}
