<?php

namespace app\modules\expert\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\helpers\InterfaceDock;
use app\modules\admin\models\SysAdmin;
use app\modules\admin\models\SysAuth;
use app\modules\helpers\Commonfun;
use app\modules\helpers\Constant;
use app\modules\expert\models\Expert;
use yii\db\Expression;
use app\modules\common\models\ExpertSource;

class ExpertController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

    /**
     * 专家列表
     * @return type
     */
    public function actionIndex() {
        $session = Yii::$app->session;
        $this->layout = 'pageframe';
        $menus = Commonfun::getAuthurls("exp");
        $menus = Commonfun::getChildrens(0, $menus);
        $session["inPort"] = "exp";
        return $this->render('index', ["admin_name" => $session["nickname"], "menus" => $menus]);
    }

    /**
     * 专家列表
     * @return type
     */
    public function actionList() {
        $get = \Yii::$app->request->get();
        $loginType = \Yii::$app->session['type'];
//        $query = Expert::find()->select(['*', 
//                new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_name from user u where u.user_id = expert.user_id) when expert_source = 2 or expert_source = 3 or expert_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = expert.user_id and a.third_type = expert.expert_source) end as user_name'),
//                new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_tel from user u where u.user_id = expert.user_id) end as user_tel')])
//                    ->where(['!=', 'expert_status', 4]);
        $query = (new Query())->select(["e.*", 's.source_name',
                new Expression('case when expert_source != 2 and expert_source != 3 and expert_source != 4 then (select u.user_name from user u where u.user_id = e.user_id) when expert_source = 2 or expert_source = 3 or expert_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = e.user_id and a.third_type = e.expert_source) end as user_name'),
                new Expression('case when expert_source != 2 and expert_source != 3 and expert_source != 4 then (select u.user_tel from user u where u.user_id = e.user_id) end as user_tel')])
                ->from("expert e")
                ->leftJoin('expert_source s', 's.source_id = e.expert_source')
                ->leftJoin('user u', 'u.user_id = e.user_id')
                ->where(["!=", "e.expert_status", 4]);
        if ($loginType != 0) {
            $identityType = \Yii::$app->session['type_identity'];
            $query = $query->andWhere(['expert_source' => $identityType]);
        }

//        
        if (isset($get["expertInfo"]) && !empty($get["expertInfo"])) {
            $query = $query->andWhere(["or", ["e.cust_no" => $get["expertInfo"]], ["u.user_name" => $get["expertInfo"]], ["u.user_tel" => $get["expertInfo"]]]);
        }
        if (isset($get["createTimeStart"]) && !empty($get["createTimeStart"])) {
            $query = $query->andWhere([">=", "e.create_time", $get["createTimeStart"] . " 00:00:00"]);
        }
        if (isset($get["createTimeEnd"]) && !empty($get["createTimeEnd"])) {
            $query = $query->andWhere(["<=", "e.create_time", $get["createTimeEnd"] . " 23:59:59"]);
        }
        if (isset($get["expertStatus"]) && !empty($get["expertStatus"])) {
            $query = $query->andWhere(["e.expert_status" => $get["expertStatus"]]);
        }
        if (isset($get["pactStatus"]) && !empty($get["pactStatus"])) {
            $query = $query->andWhere(["e.pact_status" => $get["pactStatus"]]);
        }
        if (isset($get["stick"]) && !empty($get["stick"])) {
            if ($get["stick"] == "999") {
                $query = $query->andWhere(["e.stick" => $get["stick"]]);
            } else {
                $query = $query->andWhere(["<>", "e.stick", 999]);
            }
        }
        $query = $query->orderBy("e.create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render("list", ["data" => $data, "get" => $get]);
    }

    /**
     * 专家列表(系统后台)
     * @return type
     */
    public function actionListSys() {
        $get = \Yii::$app->request->get();
        $query = (new Query())->select(["e.*", 's.source_name',
            new Expression('case when expert_source != 2 and expert_source != 3 and expert_source != 4 then (select u.user_name from user u where u.user_id = e.user_id) when expert_source = 2 or expert_source = 3 or expert_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = e.user_id and a.third_type = e.expert_source) end as user_name'),
             new Expression('case when expert_source != 2 and expert_source != 3 and expert_source != 4 then (select u.user_tel from user u where u.user_id = e.user_id) end as user_tel')])
                ->from("expert e")
                ->leftJoin('expert_source s', 's.source_id = e.expert_source')
                ->leftJoin('user u', 'u.user_id = e.user_id')
                ->where(["!=", "e.expert_status", 4]);
        if (isset($get["expertInfo"]) && !empty($get["expertInfo"])) {
            $query = $query->andWhere(["or", ["e.cust_no" => $get["expertInfo"]], ["u.user_name" => $get["expertInfo"]], ["u.user_tel" => $get["expertInfo"]]]);
        }
        if (isset($get["createTimeStart"]) && !empty($get["createTimeStart"])) {
            $query = $query->andWhere([">=", "e.create_time", $get["createTimeStart"] . " 00:00:00"]);
        }
        if (isset($get["createTimeEnd"]) && !empty($get["createTimeEnd"])) {
            $query = $query->andWhere(["<=", "e.create_time", $get["createTimeEnd"] . " 23:59:59"]);
        }
        if (isset($get["expertStatus"]) && !empty($get["expertStatus"])) {
            $query = $query->andWhere(["e.expert_status" => $get["expertStatus"]]);
        }
        if (isset($get["pactStatus"]) && !empty($get["pactStatus"])) {
            $query = $query->andWhere(["e.pact_status" => $get["pactStatus"]]);
        }
        if (isset($get["stick"]) && !empty($get["stick"])) {
            if ($get["stick"] == "999") {
                $query = $query->andWhere(["e.stick" => $get["stick"]]);
            } else {
                $query = $query->andWhere(["<>", "e.stick", 999]);
            }
        }
        if (isset($get['expertSource']) && !empty($get['expertSource'])) {
            $query = $query->andWhere(["e.expert_source" => $get["expertSource"]]);
        }
        $query = $query->orderBy("e.create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        $source = ExpertSource::find()->select(['source_id', 'source_name'])->where(['status' => 1])->asArray()->all();
        $sourceData = ['' => '全部'];
        foreach ($source as $val) {
            $sourceData[$val['source_id']] = $val['source_name'] . '专家';
        }
        return $this->render("listsys", ["data" => $data, "get" => $get, 'sourceArr' => $sourceData]);
    }

    /**
     * 专家详情
     * @return type
     */
    public function actionReadDetail() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $data = [];
        $pactStatusNames = [
            '1' => '未签',
            '2' => '正常',
            '3' => '失效'
        ];
        $data["expertInfo"] = (new Query())->select("*")->from("expert")->where(["expert_id" => $get["expert_id"]])->one();
        if ($data["expertInfo"] == null) {
            echo "未找到该专家";
            exit();
        } else {
            if (isset($pactStatusNames[$data["expertInfo"]["pact_status"]])) {
                $data["expertInfo"]["pactName"] = $pactStatusNames[$data["expertInfo"]["pact_status"]];
            } else {
                $data["expertInfo"]["pactName"] = "未知状态";
            }
        }
        $data["userInfo"] = (new Query())->select("*")->from("user")->where(["cust_no" => $data["expertInfo"]["cust_no"]])->one();
        if ($data["userInfo"] == null) {
            echo "未找到该用户信息";
            exit();
        }
        $retRealName = InterfaceDock::javaGetRealName($data["expertInfo"]["cust_no"]);
        if ($retRealName["code"] != 1) {
            $data["realInfo"]["province"] = "";
            $data["realInfo"]["city"] = "";
            $data["realInfo"]["country"] = "";
            $data["realInfo"]["address"] = "";
            $data["realInfo"]["realName"] = "";
            $data["realInfo"]["cardNo"] = "";
            $data["realInfo"]["cardFrontImg"] = "";
            $data["realInfo"]["cardBackImg"] = "";
            $data["realInfo"]["cardWithPeopleImg"] = "";
            $data["realInfo"]["bankCardImg"] = "";
        } else {
            $data["realInfo"] = $retRealName["data"];
        }
        $retStatus = InterfaceDock::javaGetAuthInfo($data["expertInfo"]["cust_no"]);
        if ($retStatus["code"] != "1") {//0、未认证  1、认证成功   2、待审核  3、审核失败
            echo "未能获取实名信息！";
            exit();
        } else {
            if ($retStatus["data"]["checkStatus"] == "0") {
                $data["authStatusName"] = "未认证";
            } elseif ($retStatus["data"]["checkStatus"] == "1") {
                $data["authStatusName"] = "认证成功";
            } elseif ($retStatus["data"]["checkStatus"] == "2") {
                $data["authStatusName"] = "待审核";
            } elseif ($retStatus["data"]["checkStatus"] == "3") {
                $data["authStatusName"] = "审核失败 - " . $retRealName["data"]["remark"];
            }
        }
        return $this->render("readdetail", ["data" => $data]);
    }

    /**
     * 专家详情(系统后台)
     * @return type
     */
    public function actionReadDetailSys() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $data = [];
        $pactStatusNames = [
            '1' => '未签',
            '2' => '正常',
            '3' => '失效'
        ];
        $data["expertInfo"] = (new Query())->select("*")->from("expert")->where(["expert_id" => $get["expert_id"]])->one();
        if ($data["expertInfo"] == null) {
            echo "未找到该专家";
            exit();
        } else {
            if (isset($pactStatusNames[$data["expertInfo"]["pact_status"]])) {
                $data["expertInfo"]["pactName"] = $pactStatusNames[$data["expertInfo"]["pact_status"]];
            } else {
                $data["expertInfo"]["pactName"] = "未知状态";
            }
        }
        $data["userInfo"] = (new Query())->select("*")->from("user")->where(["cust_no" => $data["expertInfo"]["cust_no"]])->one();
        if ($data["userInfo"] == null) {
            echo "未找到该用户信息";
            exit();
        }
        $data["fundsInfo"] = (new Query())->select("*")->from("user_funds")->where(["cust_no" => $data["expertInfo"]["cust_no"]])->one();
        if ($data["fundsInfo"] == null) {
            echo "未找到该资产信息";
            exit();
        }
        $retRealName = InterfaceDock::javaGetRealName($data["expertInfo"]["cust_no"]);
        if ($retRealName["code"] != 1) {
            $data["realInfo"]["province"] = "";
            $data["realInfo"]["city"] = "";
            $data["realInfo"]["country"] = "";
            $data["realInfo"]["address"] = "";
            $data["realInfo"]["realName"] = "";
            $data["realInfo"]["cardNo"] = "";
            $data["realInfo"]["cardFrontImg"] = "";
            $data["realInfo"]["cardBackImg"] = "";
            $data["realInfo"]["cardWithPeopleImg"] = "";
            $data["realInfo"]["bankCardImg"] = "";
        } else {
            $data["realInfo"] = $retRealName["data"];
        }
        $ret = InterfaceDock::javaGetAccountDetail($data["expertInfo"]["cust_no"]);
        if ($ret["code"] != 1) {
            $data["bankInfo"]["realName"] = "";
            $data["bankInfo"]["bankNo"] = "";
            $data["bankInfo"]["bankOutlets"] = "";
            $data["bankInfo"]["depositBank"] = "";
        } else {
            $data["bankInfo"] = $ret["data"];
        }
        $retStatus = InterfaceDock::javaGetAuthInfo($data["expertInfo"]["cust_no"]);
        if (!isset($retStatus["code"]) || $retStatus["code"] != "1") {//0、未认证  1、认证成功   2、待审核  3、审核失败
            echo "未能获取实名信息！";
            exit();
        } else {
            if ($retStatus["data"]["checkStatus"] == "0") {
                $data["authStatusName"] = "未认证";
            } elseif ($retStatus["data"]["checkStatus"] == "1") {
                $data["authStatusName"] = "认证成功";
            } elseif ($retStatus["data"]["checkStatus"] == "2") {
                $data["authStatusName"] = "待审核";
            } elseif ($retStatus["data"]["checkStatus"] == "3") {
                $data["authStatusName"] = "审核失败 - " . $retRealName["data"]["remark"];
            }
        }
        return $this->render("readdetailsys", ["data" => $data]);
    }

    /**
     * 专家审核
     * @return type
     */
    public function actionReview() {
        $this->layout = false;
        $request = \Yii::$app->request;
        $get = $request->get();
        $loginType = \Yii::$app->session['type'];
        $where = ['and'];
        if ($loginType != 0) {
            $identityType = \Yii::$app->session['type_identity'];
            $where = ['source_id' => $identityType];
        } else {
            $where = ['status' => 1];
        }
        $source = ExpertSource::find()->select(['source_id', 'source_name'])->where($where)->asArray()->all();
        $sourceData = [];
        foreach ($source as $val) {
            $sourceData[$val['source_id']] = $val['source_name'];
        }
        return $this->render("review", ['get' => $get, 'expertTypeSource' => $sourceData]);
    }

    /**
     * 审核通过
     * @return type
     */
    public function actionPass() {//expert_status
        $post = \Yii::$app->request->post();
        $expertTypeNames = Constant::EXPERT_TYPE_NAME;
//        $expertTypeSource = Constant::EXPERT_TYPE_SOURCE;
        if (!isset($post["reviewContent"]) || empty($post["reviewContent"])) {
            $reviewContent = "";
        } else {
            $reviewContent = $post["reviewContent"];
        }
        if (isset($post["expert_source"]) && !empty($post["expert_source"])) {
            $expertSource = $post["expert_source"];
//            $expertTypeName = $expertTypeSource[$post["expert_source"]];
        } else {
            return $this->jsonResult(109, "请选择专家来源");
        }
        $source = ExpertSource::find()->select(['source_id'])->where(['source_id' => $expertSource, 'status' => 1])->asArray()->all();
        if(empty($source)) {
            return $this->jsonError(109, '请选择有效来源');
        }
        if (isset($post["expert_type"]) && !empty($post["expert_type"]) && isset($expertTypeNames[$post["expert_type"]])) {
            $expertType = $post["expert_type"];
            $expertTypeName = $expertTypeNames[$post["expert_type"]];
        } else {
            return $this->jsonResult(109, "请选择专家身份类型");
        }
        if (!isset($post["identity"]) || !$post["identity"]) {
            return $this->jsonResult(109, "请选择专家类型");
        }
        $expertInfo = (new Query())->select("*")->from("expert")->where(["expert_id" => $post["expert_id"]])->one();

        $retStatus = InterfaceDock::javaGetAuthInfo($expertInfo["cust_no"]);
        if ($retStatus["code"] != "1") {
            return $this->jsonResult(109, "未能获取实名认证情况！");
        }
        if ($retStatus["data"]["checkStatus"] != "1") {
            return $this->jsonResult(109, "未通过实名认证,审核不可通过！");
        }
        $ret = \Yii::$app->db->createCommand()->update("expert", ["expert_status" => 2, "remark" => $reviewContent, "expert_type" => $expertType, 'expert_source' => $expertSource, "expert_type_name" => $expertTypeName, "modify_time" => date("Y-m-d H-i-s"), 'identity' => $post["identity"]], ["expert_id" => $post["expert_id"], "expert_status" => 1])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "审核通过");
    }

    /**
     * 审核未通过
     * @return type
     */
    public function actionNoPass() {//expert_status
        $post = \Yii::$app->request->post();
        if (!isset($post["reviewContent"]) || empty($post["reviewContent"])) {
            return $this->jsonResult(109, "审核原因");
        }
        $ret = \Yii::$app->db->createCommand()->update("expert", ["expert_status" => 3, "remark" => $post["reviewContent"], "modify_time" => date("Y-m-d H-i-s")], ["expert_id" => $post["expert_id"], "expert_status" => 1])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "审核未通过");
    }

    /**
     * 专家后台登录
     * @return type
     */
    public function actionLogin() {
        $session = Yii::$app->session;
        $this->layout = FALSE;
        if (isset($session["admin_name"]) && !empty($session["admin_name"]) && isset($session["admin_id"]) && !empty($session["admin_id"]) && isset($session["login_port"]) && !empty($session["login_port"])) {
            return $this->redirect('/expert/expert/');
        }

        if (isset($_POST["admin_name"]) && isset($_POST["password"])) {
            $model = new SysAdmin();
            $model->admin_name = $_POST["admin_name"];
            $model->password = md5($_POST["admin_name"] . $_POST["password"]);
            if ($model->validate(["admin_name", "password"])) {
                $ret = $model->validatePassword();
                if ($ret === true) {
                    $roles = (new Query())->select("*")->from("sys_admin_role sar")->join("left join", "sys_role sr", "sar.role_id=sr.role_id")->where(["sar.admin_id" => $model->admin_id, "sr.login_port" => "exp"])->all();
                    if ($roles == null) {
                        return $this->render('login', ["msg" => "账号或密码有误，请重新输入！"]);
                    }
                    $session["admin_name"] = $model->admin_name;
                    $session["admin_id"] = $model->admin_id;
                    $session["nickname"] = $model->nickname;
                    $session['agent_code'] = 'EXP30000';
                    $session['login_port'] = 'exp';
                    $authModel = new SysAuth();
                    $session["authUrls"] = $authModel->getAuthurls();
                    $format = 'y/m/d h:i:s';
                    SysAdmin::updateAll(["last_login" => date($format)], ["admin_id" => $session["admin_id"]]);
                    return $this->redirect('/expert/expert/');
                } elseif ($ret === false) {
                    return $this->render('login', ["msg" => "账号或密码有误，请重新输入！"]);
                } else {
                    return $this->render('login', ["msg" => $ret]);
                }
            } else {
                return $this->render('login', ["msg" => "账号或密码有误，请重新输入！"]);
            }
        } else {
            return $this->render('login');
        }
    }

    /**
     * 协议修改界面
     * @return type
     */
    public function actionPactStatus() {
        $get = \Yii::$app->request->get();
        $this->layout = FALSE;
        $expertInfo = (new Query())->select("*")->from("expert")->where(["expert_id" => $get["expert_id"]])->one();
        return $this->render("pactstatus", ["data" => $expertInfo]);
    }

    /**
     * 协议修改接口
     * @return type
     */
    public function actionUpdatePactStatus() {
        $post = \Yii::$app->request->post();
        $ret = \Yii::$app->db->createCommand()->update("expert", ["pact_status" => $post["pactStatus"], "modify_time" => date("Y-m-d H-i-s")], ["expert_id" => $post["expert_id"]])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "修改成功");
    }

    /**
     * 取消专家身份
     * @return type
     */
    public function actionCacelExpertStatus() {
        $post = \Yii::$app->request->post();
        $ret = \Yii::$app->db->createCommand()->update("expert", ["expert_status" => 5, "modify_time" => date("Y-m-d H-i-s")], ["expert_id" => $post["expert_id"], "expert_status" => 2])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "禁用专家身份失败");
        }
        return $this->jsonResult(600, "禁用专家身份成功");
    }

    /**
     * 启用专家身份
     * @return type
     */
    public function actionEnableExpertStatus() {
        $post = \Yii::$app->request->post();
        $ret = \Yii::$app->db->createCommand()->update("expert", ["expert_status" => 2, "modify_time" => date("Y-m-d H-i-s")], ["expert_id" => $post["expert_id"], "expert_status" => 5])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "启用专家身份失败");
        }
        return $this->jsonResult(600, "启用专家身份成功");
    }

    /**
     * 退出
     */
    public function actionLogout() {
        $session = Yii::$app->session;
        unset($session["admin_name"]);
        unset($session["admin_id"]);
        unset($session["authUrls"]);
        unset($session["nickname"]);
        unset($session["login_port"]);
        return $this->redirect('/expert/expert/login');
    }

    public function actionEditIdentity() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $userId = $request->post('userId', '');
            $expertType = $request->post('expertType', '');
            if (empty($userId) || empty($expertType)) {
                return $this->jsonError(109, '参数缺失');
            }
            $expert = Expert::findOne(['user_id' => $userId]);
            $expert->identity = $expertType;
            if (!$expert->save()) {
                return $this->jsonError(109, '身份修改失败！！');
            }
            return $this->jsonResult(600, '身份修改成功！！');
        } else {
            $request = \Yii::$app->request;
            $userId = $request->get('user_id', '');
            if (empty($userId)) {
                echo '参数有误！请重新操作！';
                exit;
            }
            $expertInfo = Expert::find()->select(['user_id', 'identity'])->where(['user_id' => $userId])->asArray()->one();
            if (empty($expertInfo)) {
                echo '该专家不存在,请重新操作';
                exit;
            }
            return $this->render("edit-identity", ['data' => $expertInfo]);
        }
    }

    /**
     * 专家置顶、取消置顶
     * @return type
     */
    public function actionEditStick() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $type = $post["type"];
        switch ($type) {
            case 1:
                $data = [
                    "modify_time" => date("Y-m-d H-i-s"),
                    "stick" => $post["stick"]
                ];
                break;
            case 2:
                $data = [
                    "modify_time" => date("Y-m-d H-i-s"),
                    "stick" => 999
                ];
                break;
        }
        $ret = Expert::updateAll($data, ["expert_id" => $post["expert_id"]]);
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "修改成功");
    }

    public function actionEditSource() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $userId = $request->post('userId', '');
            $expertSource = $request->post('expertSource', '');
            if (empty($userId) || empty($expertSource)) {
                return $this->jsonError(109, '参数缺失');
            }
            $expert = Expert::findOne(['user_id' => $userId]);
            $expert->expert_source = $expertSource;
            if (!$expert->save()) {
                return $this->jsonError(109, '专家来源修改失败！！');
            }
            return $this->jsonResult(600, '专家来源修改成功！！');
        } else {
            $request = \Yii::$app->request;
            $userId = $request->get('user_id', '');
            if (empty($userId)) {
                echo '参数有误！请重新操作！';
                exit;
            }
            $expertInfo = Expert::find()->select(['user_id', 'expert_source'])->where(['user_id' => $userId])->asArray()->one();
            if (empty($expertInfo)) {
                echo '该专家不存在,请重新操作';
                exit;
            }
            $source = ExpertSource::find()->select(['source_id', 'source_name'])->where(['status' => 1])->asArray()->all();
            $sourceData = [];
            foreach ($source as $val) {
                $sourceData[$val['source_id']] = $val['source_name'];
            }
            return $this->render("edit-source", ['data' => $expertInfo, 'expertTypeSource' => $sourceData]);
        }
    }

    public function actionEditType() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $userId = $request->post('userId', '');
            $expertType = $request->post('expertType', '');
            if (empty($userId) || empty($expertType)) {
                return $this->jsonError(109, '参数缺失');
            }
            $typeName = Constant::EXPERT_TYPE_NAME;
            $expert = Expert::findOne(['user_id' => $userId]);
            $expert->expert_type = $expertType;
            $expert->expert_type_name = $typeName[$expertType];
            if (!$expert->save()) {
                return $this->jsonError(109, '身份修改失败！！');
            }
            return $this->jsonResult(600, '身份修改成功！！');
        } else {
            $request = \Yii::$app->request;
            $userId = $request->get('user_id', '');
            if (empty($userId)) {
                echo '参数有误！请重新操作！';
                exit;
            }
            $expertInfo = Expert::find()->select(['user_id', 'expert_type'])->where(['user_id' => $userId])->asArray()->one();
            if (empty($expertInfo)) {
                echo '该专家不存在,请重新操作';
                exit;
            }
            return $this->render("edit-type", ['data' => $expertInfo]);
        }
    }

}
