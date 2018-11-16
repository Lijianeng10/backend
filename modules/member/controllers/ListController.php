<?php

namespace app\modules\member\controllers;

use app\modules\common\services\ApiSysService;
use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\member\helpers\Constants;
use app\modules\member\models\User;
use app\modules\member\models\UserLevels;
use app\modules\member\models\UserFunds;
use app\modules\member\models\IntergalRecord;
use app\modules\agents\services\IAgentsService;
use app\modules\agents\services\AgentsService;
use app\modules\common\models\UserFollow;
use app\modules\common\models\Store;
use app\modules\agents\models\Agents;
use app\modules\common\models\Coupons;
use app\modules\common\models\CouponsDetail;
use app\modules\common\models\UserRateRecord;
use app\modules\common\models\ExpertSource;

class ListController extends Controller {

    private $agentsService;

    public function __construct($id, $module, $config = [], IAgentsService $agentsService) {
        $this->agentsService = $agentsService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 会员列表
     * @return type
     */
    public function actionIndex() {
        $authen = Constants::AUTHEN_STATUS;
        $memberType = Constants::MEMBR_TYPE;
        $compar = Constants::COMPAR;
        $regform = Constants::REGFORM;
        $spreadType = Constants::SPREAD_TYPE;
        $inviteStatus = Constants::INVITE_USER;

        $vxstatus = [
            "" => "请选择",
            "1" => "已绑定",
            "2" => "未绑定",
        ];

        $where = [];
        $and1 = [];
        $and2 = [];
        $and3 = [];
        $and4 = [];
        $and5 = [];
        $where1 = [];
        $request = Yii::$app->request;
        $get = $request->get();
        $userInfo = $request->get('user_info', '');
        $agentsInfo = $request->get('agents_info', '');
        $userLevel = $request->get('user_level', '');
        $vxStatus = $request->get('vxstatus', '');
//        $agentCode = $request->get('agent_code', '');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $authenStatus = $request->get('authen_status', '');
//        $userType = $request->get('user_type', '');
        $glcoin = $request->get('glcoin', '');
        $user_glcoin = $request->get('user_glcoin', '');
        $balance = $request->get('balance', '');
        $balanceVal = $request->get('balance_val', '');
        $spread_type = $request->get('spread_type', '');
        $is_inviter = $request->get('is_inviter', '');
        $register_from = $request->get('register_from', '');
        $from_id = $request->get('from_id', '');
        if ($userLevel != '' && $userLevel != '0') {
            $where['u.level_id'] = $userLevel;
        }
        if ($start != '') {
            $and1 = ['>=', 'u.create_time', $start . ' 00:00:00'];
        }
        if ($end != '') {
            $and2 = ['<=', 'u.create_time', $end . ' 23:59:59'];
        }
        if ($authenStatus != '') {
            $where['u.authen_status'] = $authenStatus;
        }
        if ($glcoin != '' && $glcoin != '0') {
            if ($user_glcoin != '') {
                $and3 = [$glcoin, 'f.user_glcoin', $user_glcoin];
            }
        }
        if ($balance != '' && $balance != '0' && $balanceVal != '0') {
            if ($balanceVal != '') {
                $and4 = [$balance, 'f.all_funds', $balanceVal];
            }
        }
        if ($vxStatus != '') {
            if ($vxStatus == 1) {
                $and5 = "t.third_uid!=''and t.type = 1";
            } else {
                $and5 = "t.third_uid is null";
            }
        }
        $session = Yii::$app->session;
        $levels = (new UserLevels())->getLevelsList($session['agent_code']);
        $query = (new Query())->select('u.*, f.all_funds,f.able_funds, f.ice_funds, f.user_integral, f.no_withdraw, f.user_glcoin,t.third_uid,f.withdraw_status,us.user_name as agentName')
                ->from('user as u')
                ->leftJoin('user_funds as f', 'f.user_id = u.user_id')
                ->leftJoin('third_user as t', 't.uid = u.user_id  and t.type = 1')
                ->leftJoin("user as us", "u.agent_code =us.cust_no")
                ->where($where)
                ->andWhere($and1)
                ->andWhere($and2)
                ->andWhere($and3)
                ->andWhere($and4)
                ->andWhere($and5)
                ->andWhere($where1);
//                ->andWhere(['u.agent_code' => $session['agent_code']]);


        if ($userInfo != '') {
            $query = $query->andWhere(["or", ["u.cust_no" => $userInfo], ["u.user_name" => $userInfo], ["u.user_tel" => $userInfo]]);
        }
        if ($agentsInfo != '') {
            $query = $query->andWhere(["or", ["u.agent_code" => $agentsInfo], ["u.agent_name" => $agentsInfo]]);
        }
        if ($spread_type != '') {
            if ($spread_type == 4) {
                $query = $query->andWhere("u.spread_type!=0");
            } else {
                $query = $query->andWhere(["u.spread_type" => $spread_type]);
            }
        }
        //注册来源
        if (!empty($register_from)) {
            switch ($register_from) {
                case 1:
                    if (!empty($from_id)) {
                        if ($from_id == 3) {
                            $new = [];
                            $store = Store::find()->select("store_code")->where(["store_type" => 3, "cert_status" => 3])->asArray()->all();
                            foreach ($store as $v) {
                                array_push($new, $v['store_code']);
                            }
                            $query = $query->andWhere(['in', "u.from_id", $new]);
                        } else {
                            $query = $query->andWhere(["u.register_from" => $from_id]);
                        }
                    }
                    break;
                case 2:
                    if (!empty($from_id)) {
                        if ($from_id == 3) {
                            $new = [];
                            $store = Store::find()->select("store_code")->where(["in", "store_type", [1, 2]])->andWhere(["cert_status" => 3])->asArray()->all();
                            foreach ($store as $v) {
                                array_push($new, $v['store_code']);
                            }
                            $query = $query->andWhere(['in', "u.from_id", $new]);
                        }
                    }
                    break;
                case 3:
                    if (!empty($from_id)) {
                        if ($from_id == 3) {
                            $new = [];
                            $store = Store::find()->select("store_code")->where(["in", "store_type", [4]])->andWhere(["cert_status" => 3])->asArray()->all();
                            foreach ($store as $v) {
                                array_push($new, $v['store_code']);
                            }
                            $query = $query->andWhere(['in', "u.from_id", $new]);
                        }
                    }
                    break;
                default:
                    if (!empty($from_id)) {
                        $query = $query->andWhere(["u.register_from" => $register_from, "u.from_id" => $from_id]);
                    } else {
                        $query = $query->andWhere(["u.register_from" => $register_from]);
                    }
                    break;
            }
            $platform = $this->getRegisterAry($register_from);
        } else {
            $platform = [];
            $platform[''] = "请选择";
        }
        if ($is_inviter != "") {
            $query = $query->andWhere(["u.is_inviter" => $is_inviter]);
        }
        $data = $query->groupBy('u.user_id')
                ->orderBy('u.create_time desc');
        $provider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['user_id'],
            ],
        ]);
        return $this->render('index', ['data' => $provider, 'levels' => $levels, 'authen' => $authen, 'user_type' => $memberType, 'compar' => $compar, 'vxstatus' => $vxstatus, 'get' => $get, 'spreadType' => $spreadType, 'platform' => $platform, 'regform' => $regform, "inviteStatus" => $inviteStatus]);
    }

    /**
     * 新增会员
     * @return json
     * @throws Exception
     */
    public function actionAddMember() {
        $gender = Constants::GENDER;
        if (Yii::$app->request->isGet) {
//            $status = Constants::MEMBER_STATUS;
            $gender = Constants::GENDER;
            return $this->render('add-member', ['gender' => $gender]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $post = $request->post();
            $name = $request->post('user_name', '');
            $tel = $request->post('user_tel', '');
            $land = $request->post('user_land', '');
            $province = $request->post('province', '0');
            $city = $request->post('city', '0');
            $area = $request->post('area', '0');
            $address = $request->post('address', '');
            $sex = $request->post('user_gender', '0');
            $zsIntergal = $request->post('zs_intergal', 0);
            $remark = $request->post('user_remark', '');
            if ($name == '' || $tel == '' || $email == '' || $province == '0' || $city == '0' || $address == '') {
                return $this->jsonResult(2, '参数有误,请重新填写', '');
            }
            $only = User::find()->select('user_id, user_tel, user_email')->where(['user_tel' => $tel])->orWhere(['user_email' => $email])->asArray()->one();
            if (!empty($only)) {
                if ($only['user_tel'] == $tel) {
                    return $this->jsonResult(2, '此电话已被使用,请重新填写', '');
                }
                if ($only['user_email'] == $email) {
                    return $this->jsonResult(2, '此邮箱已被使用,请重新填写');
                }
            }
            $data = new User();
            $session = Yii::$app->session;
            $levels = UserLevels::find()->where(['agent_id' => $session['admin_id']])->orderBy('user_level_id')->asArray()->one();
            if ($area != '0') {
                $data->area = $area;
            }
            if ($sex != '0') {
                $data->user_sex = $gender[$sex];
            }
            if ($land != '') {
                $data->user_land = $land;
            }
            if ($remark != '') {
                $data->user_remark = $remark;
            }
            $data->user_name = $name;
            $data->password = '123456';
            $data->user_tel = $tel;
            $data->status = 1;
            $data->province = $province;
            $data->city = $city;
            $data->address = $address;
            $data->account_time = date('Y-m-d H:i:s');
            $data->level_id = $levels['user_level_id'];
            $data->level_name = $levels['level_name'];
            $data->agent_id = $session['admin_id'];
            $data->agent_name = $session['admin_name'];
            $data->agent_code = $session['agent_code'];
            $data->authen_status = 1;
            $data->create_time = date('Y-m-d H:i:s');
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try {
                if ($data->validate()) {
                    $insertId = $data->save();
                    if ($insertId == false) {
                        throw new Exception('新增会员失败');
                    }
                } else {
                    return $this->jsonResult(109, '', $data->errors);
                }

                if ($zsIntergal != 0) {
                    $intergal = new IntergalRecord();
                    $intergal->user_id = $data->user_id;
                    $intergal->user_name = $name;
                    $intergal->intergal_source = '赠送';
                    $intergal->intergal_value = $zsIntergal;
                    if ($intergal->validate()) {
                        $recordId = $intergal->save();
                        if ($recordId == false) {
                            throw new Exception('记录表新增失败,');
                        }
                    } else {
                        throw new Exception('操作失败,记录表验证失败');
                    }
                    $funds = UserFunds::find()->where(['user_id' => $data->user_id])->one();
                    if (!empty($funds)) {
                        $sql = "UPDATE user_funds SET user_intergal = user_intergal + {$zsIntergal},modify_time = '" . date('Y-m-d H:i:s') . "' where user_id = {$data->user_id}";
                        $fundId = $db->createCommand($sql)->execute();
                    } else {
                        $funds = new UserFunds();
                        $funds->user_integral = $zsIntergal;
                        $funds->create_time = date('Y-m-d H:i:s');
                        if ($funds->validate()) {
                            $fundId = $funds->save();
                        } else {
                            throw new Exception('操作失败,资金表验证失败');
                        }
                    }
                    if ($fundId == false) {
                        throw new Exception('资金表更新失败');
                    }
                }
                $trans->commit();
                return $this->jsonResult(600, '新增成功', '');
            } catch (Exception $e) {
                $trans->rollBack();
                return $this->jsonResult(109, $e->getMessage());
            }
        } else {
            echo '操作错误';
            exit();
        }
    }

    /**
     * 编辑会员信息
     * @return json
     */
    public function actionEditMember() {
        $gender = Constants::GENDER;
        $status = Constants::MEMBER_STATUS;
        $session = Yii::$app->session;
        $levels = UserLevels::find()->where(['agent_code' => $session['agent_code']])->orderBy('user_level_id')->asArray()->all();
        $levelsList = [];
        foreach ($levels as $val) {
            $levelsList[$val['user_level_id']] = $val['level_name'];
        }
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            if (!isset($get['user_id'])) {
                echo '参数错误';
                return $this->redirect('/member/list/index');
            }
            $user = User::find()->where(['user_id' => $get['user_id']])->asArray()->one();
            if (empty($user)) {
                echo '该会员不存在，请返回列表';
                return $this->redirect('/member/list/index');
            }
            return $this->render('edit-member', ['user_data' => $user, 'gender' => $gender, 'user_status' => $status, 'levels' => $levelsList]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $userId = $request->post('user_id', '');
            if ($userId == '') {
                return $this->jsonResult(2, '参数有误');
            }
            $post = $request->post();
            $name = $request->post('user_name', '');
            $tel = $request->post('user_tel', '');
            $land = $request->post('user_land', '');
            $province = $request->post('province', '0');
            $city = $request->post('city', '0');
            $area = $request->post('area', '0');
            $address = $request->post('address', '');
            $sex = $request->post('user_gender', '0');
            $remark = $request->post('user_remark', '');
            $userSta = $request->post('user_status', '0');
            $userLevel = $request->post('user_level', '0');
            if ($name == '' || $tel == '' || $province == '0' || $city == '0' || $address == '' || $userSta == '0' || $userLevel == '0') {
                return $this->jsonResult(2, '参数有误,请重新填写', '');
            }
            $only = User::find()->select('user_id, user_tel')->where(['user_tel' => $tel])->andWhere(['!=', 'user_id', $userId])->asArray()->one();
            if (!empty($only)) {
                if ($only['user_tel'] == $tel) {
                    return $this->jsonResult(2, '此电话已被使用,请重新填写', '');
                }
//                if ($only['user_email'] == $email) {
//                    return $this->jsonResult(2, '此邮箱已被使用,请重新填写');
//                }
            }
            $userData = User::find()->where(['user_id' => $userId])->one();

            if ($area != '0') {
                $userData->area = $area;
            }
            if ($sex != '0') {
                $userData->user_sex = $gender[$sex];
            }
            if ($land != '') {
                $userData->user_land = $land;
            }
            if ($remark != '') {
                $userData->user_remark = $remark;
            }
            $userData->user_name = $name;
            $userData->user_tel = $tel;
            $userData->status = $status[$userSta];
            $userData->province = $province;
            $userData->city = $city;
            $userData->address = $address;
            $userData->level_id = $userLevel;
            $userData->level_name = $levelsList[$userLevel];
            $userData->status = intval($userSta);
            $userData->opt_id = $session['admin_id'];
            $userData->modify_time = date('Y-m-d H:i:s');
            if ($userData->validate()) {
                $updateId = $userData->save();
                if ($updateId == false) {
                    return $this->jsonResult(109, '会员编辑失败');
                } else {
                    return $this->jsonResult(600, '编辑成功');
                }
            } else {
                return $this->jsonResult(109, '会员验证失败', $userData->errors);
            }
        } else {
            echo '操作错误';
            return $this->redirect('/member/list/index');
        }
    }

    /**
     * 查看会员信息
     * @return array
     */
    public function actionViewMember() {
        if (Yii::$app->request->isGet) {
            $gender = Constants::GENDER;
            $status = Constants::MEMBER_STATUS;
            $authen = Constants::AUTHEN_STATUS;
            $session = Yii::$app->session;
            $levels = UserLevels::find()->where(['agent_code' => $session['admin_code']])->orderBy('user_level_id')->asArray()->all();
            $levelsList = [];
            foreach ($levels as $val) {
                $levelsList[$val['user_level_id']] = $val['level_name'];
            }
            $get = Yii::$app->request->get();
            if (!isset($get['user_id'])) {
                echo '参数错误';
                return $this->redirect('/member/list/index');
            }
            $user = User::find()->select("user.*,f.all_funds,f.able_funds,f.ice_funds,f.no_withdraw,f.user_integral,f.user_glcoin,f.user_growth")
                    ->where(['user.user_id' => $get['user_id']])
                    ->leftJoin('user_funds as f', 'f.user_id = user.user_id')
                    ->asArray()
                    ->one();
            if (empty($user)) {
                return $this->jsonResult(109, '该会员不存在');
            }
            //获取会员收款账户信息
            $user["detail"] = $this->agentsService->javaGetUserAccountDetail($user["cust_no"]);
            //获取会员身份证信息
            $user["card"] = $this->agentsService->javaGetRealName($user["cust_no"]);
            //获取会员是否实名认证
            $user["javaStatus"] = $this->agentsService->javaGetStatus($user["cust_no"]);
            //获取会员绑定门店信息
            $storeInfo = UserFollow::find()->select("user_follow.*,s.store_name,s.province,s.city,s.area,s.address")
                    ->where(["user_follow.cust_no" => $user["cust_no"], "user_follow.follow_status" => 1])
                    ->leftJoin("store as s", "s.store_code=user_follow.store_id")
                    ->asArray()
                    ->all();
            return $this->render('view-member', ['user_data' => $user, 'storeInfo' => $storeInfo, 'gender' => $gender, 'user_status' => $status, 'levels' => $levelsList, 'authen' => $authen]);
        } else {
            return $this->jsonResult(109, '操作错误');
        }
    }

    /**
     * 会员审核
     * @return json
     */
    public function actionReviewMember() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            if (!isset($get['user_id'])) {
                echo '参数错误';
                return $this->redirect('/member/list/index');
            }
            $user = User::find()->where(['user_id' => $get['user_id']])->asArray()->one();
            if (empty($user)) {
                echo '该会员不存在，请返回列表';
                return $this->redirect('/member/list/index');
            }
            return $this->render('review-member', ['user_id' => $get['user_id']]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $userId = $request->post('user_id', '');
            $authen = $request->post('user_authen', '');
            $remark = $request->post('authen_remark', '');
            if ($userId == '' || $authen == '') {
                return $this->jsonResult(109, '参数有误,请重新操作');
            }
            $data = User::find()->where(['user_id' => $userId])->one();
            if (empty($data)) {
                return $this->jsonResult(109, '该会员不存在');
            }
            if ($remark != '') {
                $data->authen_remark = $remark;
            }
            $data->authen_status = $authen;
            $data->opt_id = $session['admin_id'];
            if ($data->validate('authen_status')) {
                $updateId = $data->save();
                if ($updateId == false) {
                    return $this->jsonResult(109, '审核失败');
                }
                return $this->jsonResult(600, '审核成功');
            } else {
                return $this->jsonResult(109, '审核失败，参数有误');
            }
        } else {
            echo '操作错误';
            return $this->redirect('/member/list/index');
        }
    }

    /**
     * 删除会员
     * @return json
     */
    public function actionDeleteMember() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/member/list/index');
        }

        $request = Yii::$app->request;
        $userId = $request->post('user_id', '');

        if ($userId == '') {
            return $this->jsonResult(2, '参数有误', '');
        }

        $result = User::deleteAll(['user_id' => $userId]);
        if ($result != false) {
            return $this->jsonResult(600, '删除成功', '');
        } else {
            return $this->jsonResult(109, '删除失败', '');
        }
    }

    /**
     * 会员是否有购彩权限
     * @return json
     */
    public function actionChangeUserType() {
        $post = Yii::$app->request->post();
        $user = User::findOne(["user_id" => $post["user_id"]]);
        if ($user == null) {
            return $this->jsonResult(109, '修改失败');
        }
        $user->user_type = (($user->user_type == 1) ? 2 : 1);
        $ret = $user->save();
        if ($ret === false) {
            return $this->jsonResult(109, '修改失败');
        } else {
            return $this->jsonResult(600, '修改成功');
        }
    }

    /**
     * 会员启用、禁用
     * @return json
     */
    public function actionEditStatus() {
        $post = Yii::$app->request->post();
        $user = User::findOne(["user_id" => $post["user_id"]]);
        if ($user == null) {
            return $this->jsonResult(109, '修改失败');
        }
        $user->status = (($user->status == 1) ? 2 : 1);
        $ret = $user->save();
        if ($ret === false) {
            return $this->jsonResult(109, '修改失败');
        } else {
            if ($user->status == 2) {
                $key = 'user_token:' . $user->cust_no;
                $token = \Yii::tokenGet($key);
                if ($token) {
                    $k = 'token_user:' . $token;
                    \Yii::tokenDel($k);
                }
            }
            if (isset($post['active_type']) && $post['active_type'] == 2) {
                ApiSysService::cancelCard($user->cust_no);
            }
            return $this->jsonResult(600, '修改成功');
        }
    }

    /**
     * 用户新增绑定门店
     */
    public function actionAddstore() {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $get = Yii::$app->request->get();
            $cust_no = $get["cust_no"];
            if (!empty($cust_no)) {
                $store = array();
                $store[0] = "请选择";
                $storeInfo = Store::find()->select("store_code,store_name")->where(["status" => 1, "cert_status" => 3])->asArray()->all();
                foreach ($storeInfo as $k => $v) {
                    $store[$v["store_code"]] = $v["store_name"];
                }
                return $this->render('add-store', ["cust_no" => $cust_no, "store" => $store]);
            }
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $cust_no = $post["cust_no"];
            $store = $post["store"];
            if ($store == 0) {
                return $this->jsonResult(109, '请选择需要绑定的彩店');
            }
            $result = UserFollow::find()->where(["cust_no" => $cust_no, "follow_status" => 1])->one();
            if (!empty($result)) {
                return $this->jsonResult(109, '该用户已绑定彩店，不可重复绑定');
            }
            $followRes = UserFollow::find()->where(["cust_no" => $cust_no, "store_id" => $store])->one();
            if (!empty($followRes)) {
                return $this->jsonResult(109, '该用户已绑定该彩店，请勿重复绑定');
            }
            $storeInfo = Store::find()->select("cust_no")->where(["store_code" => $store, "status" => 1])->asArray()->one();
            $userFollow = new UserFollow;
            $userFollow->cust_no = $cust_no;
            $userFollow->store_no = $storeInfo["cust_no"];
            $userFollow->store_id = $store;
            $userFollow->default_status = 2;
            $userFollow->create_time = date('Y-m-d H:i:s');
            if ($userFollow->validate()) {
                $res = $userFollow->save();
                if ($res == false) {
                    return $this->jsonResult("109", "门店绑定新增失败");
                } else {
                    return $this->jsonResult("600", "门店绑定新增成功");
                }
            } else {
                var_dump($userFollow->getErrors());
                return $this->jsonResult("109", "门店表单验证失败");
            }
        }
    }

    /**
     * 推广员类型转换
     */
    public function actionEditSpreadType() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $time = date("Y-m-d H:i:s");
            $post = Yii::$app->request->post();
            $user = User::findOne(["user_id" => $post["user_id"]]);
            if ($user == null) {
                return $this->jsonResult(109, '修改失败');
            }
            if ($post["type"] == 1) {
                $invite_code = $this->getSpreadMark(6);
                $user->invite_code = $invite_code;
                $user->rebate = $post["rebate"];
                $user->is_profit = 1;
                $user->spread_type = $post["type"];
                //新增返点记录
                $record = new UserRateRecord();
                $record->cust_no = $user->cust_no;
                $record->rate_value = $post["rebate"];
                $record->start_time = $time;
                $record->create_time = $time;
                $record->save();
            } elseif ($post['type'] == 11) {
                $invite_code = $this->getSpreadMark(6);
                $user->invite_code = $invite_code;
                $user->spread_type = $post["type"];
                $source = ExpertSource::findOne(['user_id' => $post["user_id"]]);
                if (empty($source)) {
                    $source = new ExpertSource();
                    $source->create_time = $time;
                } else {
                    $source->modify_time = date('Y-m-d H:i:s');
                }
                $source->user_id = $post['user_id'];
                $source->source_name = $user->user_name;
                $source->status = 1;
                $source->save();
            } else {
                $user->invite_code = "daiding";
                $user->rebate = "0";
                $user->is_profit = "0";
                if ($user->spread_type != 11) {
                    //结束返点记录
                    $recordRes = UserRateRecord::findOne(["cust_no" => $user->cust_no, "end_time" => null]);
                    if (!empty($recordRes)) {
                        $recordRes->end_time = $time;
                        $recordRes->save();
                    }
                } else {
                    $source = ExpertSource::findOne(['user_id' => $post['user_id']]);
                    $source->status = 0;
                    $source->modify_time = $time;
                    $source->save();
                }
                $user->spread_type = $post["type"];
            }
            $ret = $user->save();
            if ($ret === false) {
                return $this->jsonResult(109, '修改失败', $user->errors);
            } else {
                return $this->jsonResult(600, '修改成功');
            }
        } else {
            $request = \Yii::$app->request;
            $userId = $request->get('userId', '');
            $userData = User::find()->select(['user_id', 'cust_no', 'user_name'])->where(['user_id' => $userId])->asArray()->one();
            if (empty($userId)) {
                echo '无效参数！请重新加载！';
            }
            return $this->render('set-spread', ['user' => $userData]);
        }
    }

    /**
     * 生成推广员邀请码
     */
    public function getSpreadMark($nums) {
        $str = md5(uniqid(microtime(true), true));
        $mark = strtoupper(substr($str, 0, $nums));
        //保证邀请码不会重复
        while (User::findOne(['invite_code' => $mark])) {
            $str = md5(uniqid(microtime(true), true));
            $mark = strtoupper(substr($str, 0, $nums));
        }
        return $mark;
    }

    /**
     * 会员启用、禁用
     * @return json
     */
    public function actionBanWithdraw() {
        $request = Yii::$app->request;
        $userId = $request->post('user_id', '');
        if (empty($userId)) {
            return $this->jsonError(109, '参数缺失');
        }
        $user = User::findOne(["user_id" => $userId]);
        if (empty($user)) {
            return $this->jsonResult(109, '修改失败, 该会员不存在');
        }
        $userFunds = UserFunds::findOne(['cust_no' => $user->cust_no, 'user_id' => $userId]);
        $userFunds->withdraw_status = (($userFunds->withdraw_status == 1) ? 2 : 1);
        if (!$userFunds->save()) {
            return $this->jsonResult(109, '修改失败!');
        } else {
            return $this->jsonResult(600, '修改成功!');
        }
    }

    /**
     * 查看优惠券
     */
    public function actionReadUserCoupons() {
        $get = Yii::$app->request->get();
        $send_status = [
            "" => "请选择",
            "1" => "未发送",
            "2" => "已发送",
        ];
        $use_status = [
            "" => "请选择",
            "0" => "未领取",
            "1" => "未使用",
            "2" => "已使用",
        ];
        $status = [
            "" => "请选择",
            "1" => "激活",
            "2" => "锁定",
        ];
        $couponsList = (new Query())->select("*")
                ->from("coupons_detail")
                ->where(["send_user" => $get["cust_no"]]);
        $data = new ActiveDataProvider([
            'query' => $couponsList,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render('view-coupons', ["data" => $data, "send_status" => $send_status, "use_status" => $use_status, "status" => $status, "get" => $get]);
    }

    /**
     * 修改推广员返点信息
     */
    public function actionEditSpread() {
        $syncData = [];
        $time = date("Y-m-d H:i:s");
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $get = Yii::$app->request->get();
            $user_id = $get["user_id"];
            $info = User::find()->where(["user_id" => $user_id])->asArray()->one();
            return $this->render('edit-spread', ["data" => $info]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $user_id = $post["user_id"];
            $rebate = $post["rebate"];
            $is_profit = $post["is_profit"];
            $user = User::findOne(["user_id" => $user_id]);
            if (empty($user)) {
                return $this->jsonResult(109, '修改失败,用户不存在');
            }
            if ($rebate == $user->rebate && $is_profit == $user->is_profit) {
                return $this->jsonResult(109, '修改失败,数据无需更新');
            }
            if ($rebate != $user->rebate) {
                //结束旧的返点记录
                $recordRes = UserRateRecord::findOne(["cust_no" => $user->cust_no, "end_time" => null]);
                if (!empty($recordRes)) {
                    $recordRes->end_time = $time;
                    $recordRes->save();
                    $syncData[] = $recordRes->attributes;
                }
                //新增新的返点记录
                $record = new UserRateRecord();
                $record->cust_no = $user->cust_no;
                $record->rate_value = $rebate;
                $record->start_time = $time;
                $record->create_time = $time;
                $record->save();
                $syncData[] = $record->attributes;
            }
            //更新用户返点信息
            $user->rebate = $rebate;
            $user->is_profit = $is_profit;
            $ret = $user->save();
            if ($ret === false) {
                return $this->jsonResult(109, '修改失败');
            } else {
                ApiSysService::sycnRateRecord($syncData);
                return $this->jsonResult(600, '修改成功');
            }
        }
    }

    /**
     * 新增用户推广下级
     */
    public function actionAddSpread() {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $get = Yii::$app->request->get();
            $cust_no = $get["cust_no"];
            return $this->render('add-spread', ["cust_no" => $cust_no]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $cust_no = $post["cust_no"];
            $user_info = $post["user_info"];
            if (empty($user_info)) {
                return $this->jsonResult(109, '请输入用户信息');
            }
            $userRes = User::find()->where(["or", ["user_tel" => $user_info], ["cust_no" => $user_info]])->one();
            if (empty($userRes)) {
                return $this->jsonResult(109, '未查找到该用户，请检查输入是否有误');
            }
            if ($userRes->register_from == 7) {
                return $this->jsonResult(109, '该用户已是推广人员推广，不可绑定');
            }
            //查找自己的用户信息
            $user = User::find()->where(["cust_no" => $cust_no])->asArray()->one();

            $userRes->register_from = 7;
            $userRes->from_id = $user["user_id"];
            $userRes->p_tree = $user["p_tree"] . "-" . $userRes->cust_no;
            $userRes->agent_code = $user["cust_no"];
            $userRes->agent_name = $user["user_name"];
            $res = $userRes->save();
            if ($res == false) {
                return $this->jsonResult(109, "下级用户新增失败");
            } else {
                return $this->jsonResult(600, "下级用户新增成功");
            }
        }
    }

    /**
     * 我的推广用户
     */
    public function actionReadUser() {
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $get = $request->get();
            $spread_type = $request->get('spread_type', '');
            $spreadType = Constants::SPREAD_TYPE;
            $user = User::find()->where(['agent_code' => $get['cust_no']]);
            if (!empty($get["user_info"])) {
                $user = $user->andWhere(["or", ["cust_no" => $get["user_info"]], ["user_name" => $get["user_info"]], ["user_tel" => $get["user_info"]]]);
            }
            if ($spread_type != '') {
                if ($spread_type == 4) {
                    $user = $user->andWhere("spread_type!=0");
                } else {
                    $user = $user->andWhere(["spread_type" => $spread_type]);
                }
            }
            $user = $user->orderBy("create_time desc");
            $data = new ActiveDataProvider([
                'query' => $user,
                'pagination' => [
                    'pageSize' => 20,
                ]
            ]);
            return $this->render('read-user', ['data' => $data, "cust_no" => $get['cust_no'], 'get' => $get, "spreadType" => $spreadType]);
        }
    }

    /**
     * 成为邀请人
     */
    public function actionInviteChange() {
        $post = \Yii::$app->request->post();
        $user = User::findOne(["user_id" => $post["user_id"]]);
        $user->is_inviter = $user->is_inviter == 0 ? 1 : 0;
        if (!$user->save()) {
            return $this->jsonResult(109, '设置失败');
        }
        return $this->jsonResult(600, '设置成功');
    }

    /**
     * 获取注册来源
     * 3 门店注册 5 代理商 7 推广人员 8 平台推广
     */
    public function actionGetRegisterFrom() {
        $post = \Yii::$app->request->post();
        $from = $post["register_from"];
        $fromAry = [];
        switch ($from) {
            case 1:
                $fromAry = [
                    0 => [
                        "id" => "1",
                        "name" => "APP注册"
                    ],
                    1 => [
                        "id" => "2",
                        "name" => "H5注册"
                    ],
                    2 => [
                        "id" => "3",
                        "name" => "彩店二维码注册"
                    ],
                    3 => [
                        "id" => "4",
                        "name" => "咕啦社区"
                    ],
                ];
                break;
            case 2:
                $fromAry = [
                    0 => [
                        "id" => "3",
                        "name" => "彩店二维码注册"
                    ]
                ];
                break;
            case 3:
                $fromAry = [
                    0 => [
                        "id" => "3",
                        "name" => "彩店二维码注册"
                    ]
                ];
//                $store = Store::find()->select("store_code,store_name")->where(["cert_status"=>3])->asArray()->all();
//                foreach ($store as $k=>$v){
//                    $fromAry[$k]["id"]=$v['store_code'];
//                    $fromAry[$k]["name"]= $v['store_name'];
//                }
                break;
            case 5:
                $agents = Agents::find()->select("agents_id,agents_name")->where(["pass_status" => 3])->asArray()->all();
                foreach ($agents as $k => $v) {
                    $fromAry[$k]["id"] = $v['agents_id'];
                    $fromAry[$k]["name"] = $v['agents_name'];
                }
                break;
            case 7:
                $user = User::find()->select("user_id,user_name")->where(["<>", "spread_type", 0])->asArray()->all();
                foreach ($user as $k => $v) {
                    $fromAry[$k]["id"] = $v['user_id'];
                    $fromAry[$k]["name"] = $v['user_name'];
                }
                break;
            case 8:
                $fromAry = [
                    0 => [
                        "id" => "meitu",
                        "name" => "美图平台"
                    ],
                    1 => [
                        "id" => "txty",
                        "name" => "腾讯体育"
                    ],
                    2 => [
                        "id" => "qj",
                        "name" => "全景"
                    ],
                ];
                break;
        }
        return $this->jsonResult(600, '获取成功', $fromAry);
    }

    public function getRegisterAry($from) {
        $fromAry = [];
        $fromAry[''] = "请选择";
        switch ($from) {
            case 1:
                $fromAry = [
                    "1" => "APP注册",
                    "2" => "H5注册",
                    "3" => "彩店二维码注册",
                    "4" => "咕啦社区",
                ];
                break;
            case 2:
                $fromAry = [
                    "3" => "彩店二维码注册",
                ];
                break;
            case 3:
                $fromAry = [
                    "3" => "彩店二维码注册",
                ];
                break;
            case 5:
                $agents = Agents::find()->select("agents_id,agents_name")->where(["pass_status" => 3])->asArray()->all();
                foreach ($agents as $k => $v) {
                    $fromAry[$v["agents_id"]] = $v['agents_name'];
                }
                break;
            case 7:
                $user = User::find()->select("user_id,user_name")->where(["<>", "spread_type", 0])->asArray()->all();
                foreach ($user as $k => $v) {
                    $fromAry[$v["user_id"]] = $v['user_name'];
                }
                break;
            case 8:
                $fromAry = [
                    "meitu" => "美图平台",
                    "txty" => "腾讯体育",
                    "qj" => "全景"
                ];
                break;
        }
        return $fromAry;
    }

    /**
     * 解绑门店
     */
    public function actionDelUserFollow() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"])) {
            return $this->jsonError(109, "参数缺失");
        }
        $res = UserFollow::deleteAll(["user_follow_id" => $post["id"]]);
        if (!$res) {
            return $this->jsonError(109, "解绑失败");
        }
        return $this->jsonResult(600, "解绑成功", "");
    }

    /**
     * 会员是否有购彩权限
     * @return json
     */
    public function actionChangeLimitLottery() {
        $post = Yii::$app->request->post();
        $user = User::findOne(["user_id" => $post["user_id"]]);
        if ($user == null) {
            return $this->jsonResult(109, '修改失败');
        }
        $user->limit_lottery = (($user->limit_lottery == 1) ? 0 : 1);
        $ret = $user->save();
        if ($ret === false) {
            return $this->jsonResult(109, '修改失败');
        } else {
            return $this->jsonResult(600, '修改成功');
        }
    }

}
