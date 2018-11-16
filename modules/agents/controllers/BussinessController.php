<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db;
use yii\db\Query;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\common\models\User;
use app\modules\common\models\ApiIp;
use app\modules\common\models\ApiList;
use app\modules\common\models\Bussiness;
use app\modules\common\models\BussinessIpWhite;

class BussinessController extends \yii\web\Controller {

    /**
     * 合作商页面
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $query = (new Query())->select("*")
                ->from("bussiness");
        if (isset($get["bussiness_name"]) && !empty($get["bussiness_name"])) {
            $query = $query->andWhere(["or", ["name" => $get["bussiness_name"]], ["bussiness_appid" => $get["bussiness_name"]], ["secret_key" => $get["bussiness_name"]]]);
        }
        if (isset($get["startdate"]) && !empty($get["startdate"])) {
            $query = $query->andWhere([">", "create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (isset($get["enddate"]) && !empty($get["enddate"])) {
            $query = $query->andWhere(["<", "create_time", $get["enddate"] . " 23:59:59"]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        $query = $query->orderBy("create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }

    /**
     * 新增合作商
     */
    public function actionAddBussiness() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            return $this->render("add-bussiness");
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $contents = $post["contents"];
            if ($contents == "") {
                return $this->jsonResult(109, "合作商名称不能为空");
            }
            $bussinessRes = Bussiness::find()->where(["name" => $contents])->asArray()->one();
            if (!empty($bussinessRes)) {
                return $this->jsonResult(109, "合作商名称重复，请重新填写");
            }
            $bussiness = new Bussiness();
            $bussiness->name = $contents;
            $bussiness->bussiness_appid = $this->getAppid();
            $bussiness->secret_key =$this->getSecret();
            $bussiness->des_key =$this->getDeskey();
            $bussiness->create_time = date("Y-m-d H:i:s");
            if ($bussiness->validate()) {
                $res = $bussiness->save();
                if ($res) {
                    return $this->jsonResult(600, "新增成功");
                } else {
                    return $this->jsonResult(109, "新增失败");
                }
            } else {
                return $this->jsonResult(109, "新增失败,合作商表单验证失败");
            }
        }
    }

    /**
     * 合作商分配IP地址
     */
    public function actionAddBussinessIp() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $ipAddress = trim($post["ipAddress"]);
            $query = BussinessIpWhite::find()->where(["ip" => $ipAddress, "bussiness_id" => $post["bussiness_id"]])->one();
            if (!empty($query)) {
                return $this->jsonResult(109, "合作商IP地址重复，请重新填写");
            }
            $Ip = new BussinessIpWhite();
            $Ip->bussiness_id = $post["bussiness_id"];
            $Ip->ip = $ipAddress;
            if ($Ip->validate()) {
                $res = $Ip->save();
                if ($res) {
                    return $this->jsonResult(600, "新增成功");
                } else {
                    return $this->jsonResult(109, "新增失败");
                }
            } else {
                return $this->jsonResult(109, "新增失败,合作商IP白名单表单验证失败");
            }
        } else {
            $get = \Yii::$app->request->get();
            $Bussiness = Bussiness::findOne(["bussiness_id" => $get["bussiness_id"]]);
            return $this->render("add-bussiness-ip", ["data" => $Bussiness]);
        }
    }

    /**
     * 查看合作商信息
     * @return
     */
    public function actionReadBussiness() {
//        $this->layout = false;
        $get = \Yii::$app->request->get();
        $Bussiness = Bussiness::find()->where(["bussiness_id" => $get["bussiness_id"]])->asArray()->one();
        $query = (new Query())->select("i.bussiness_ip_white_id,i.ip,i.status,b.name")
                ->from("bussiness_ip_white as i")
                ->leftJoin("bussiness as b", "b.bussiness_id=i.bussiness_id")
                ->where(["b.bussiness_id" => $get["bussiness_id"]]);
        $BussinessIp = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ]
        ]);
        return $this->render("read-bussiness", ["data" => $Bussiness, "ipInfo" => $BussinessIp]);
    }

    /**
     * 合作商启用禁用
     */
    public function actionEdituse() {
        $post = \Yii::$app->request->post();
        if ($post["sta"] == 1) {
            $userInfo = Bussiness::find()->where(["bussiness_id" => $post["bussiness_id"]])->asArray()->one();
            if ($userInfo["user_id"] == "") {
                return $this->jsonResult(109, "修改失败，请先绑定咕啦会员");
            }
        }
        $res = Bussiness::updateAll(["status" => $post["sta"],], ["bussiness_id" => $post["bussiness_id"]]);
        if ($res) {
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(109, "修改失败");
        }
    }

    /**
     * 合作商IP地址启用禁用
     */
    public function actionEditIpSta() {
        $post = \Yii::$app->request->post();
        $res = BussinessIpWhite::updateAll(["status" => $post["sta"],], ["bussiness_ip_white_id" => $post["bussiness_ip_white_id"]]);
        if ($res) {
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(109, "修改失败");
        }
    }

    /**
     * 合作商ip分配接口权限
     */
    public function actionAllotmentApi() {
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $bussinessId = $get["bussiness_id"];
            if (empty($bussinessId)) {
                return $this->jsonResult(109, "参数缺失");
            }
            $bussinessIp = BussinessIpWhite::find()->where(["bussiness_id" => $bussinessId])->andWhere(["status" => 1])->asArray()->all();
            $bussinessIdAry = [];
            if (!empty($bussinessIp)) {
                foreach ($bussinessIp as $v) {
                    array_push($bussinessIdAry, $v["bussiness_ip_white_id"]);
                }
            }
            $apiList = ApiList::find()->where(["status" => 1])->asArray()->all();
            //当前合作商IP数组，用于判断当前ip地址是否在权限关系表
            $ipAry = [];
            $ip = ApiIp::find()->select("bussiness_ip_id")->distinct("bussiness_ip_id")->asArray()->all();
            if (!empty($ip)) {
                foreach ($ip as $k => $v) {
                    array_push($ipAry, $v["bussiness_ip_id"]);
                }
            }
            //当前合作商API数组
            $apiAry = [];
            $api = ApiIp::find()->select("api_list_id")->distinct("api_list_id")->where(["in", "bussiness_ip_id", $bussinessIdAry])->asArray()->all();
            if (!empty($api)) {
                foreach ($api as $k => $val) {
                    array_push($apiAry, $val["api_list_id"]);
                }
            }

            return $this->render("allotment-api", ["ipList" => $bussinessIp, "apiList" => $apiList, "api" => $apiAry, "ip" => $ipAry, "bussinessId" => $bussinessId]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $bussinessId = $post["bussinessId"];
            $ipAry = $post["ipAry"];
            $apiAry = $post["apiAry"];
            $timer = date("Y-m-d H:i:s");
            if (empty($ipAry) || empty($bussinessId)) {
                return $this->jsonResult(109, "参数缺失");
            }
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try {
                $bussinessIpIdAry = BussinessIpWhite::find()->select("bussiness_ip_white_id")->where(["bussiness_id" => $bussinessId])->asArray()->all();
                //先清空合作商之前权限，再录入新的权限关系
                $ipArray = [];
                $ip = ApiIp::find()->select("bussiness_ip_id")->distinct("bussiness_ip_id")->asArray()->all();
                foreach ($ip as $k => $v) {
                    array_push($ipArray, $v["bussiness_ip_id"]);
                }
                foreach ($bussinessIpIdAry as $v) {
                    if (in_array($v["bussiness_ip_white_id"], $ipArray)) {
                        $res = ApiIp::deleteAll(['bussiness_ip_id' => $v["bussiness_ip_white_id"]]);
                        if (!$res) {
                            throw new Exception('原权限关系删除失败');
                        }
                    }
                }
                if (!empty($apiAry)) {
                    //批量增加权限
                    $query = "insert into api_ip(bussiness_ip_id,api_list_id,create_time) values";
                    $num = 0;
                    foreach ($ipAry as $key => $val) {
                        foreach ($apiAry as $k => $v) {
                            $num++;
                            if ($num == count($apiAry) * count($ipAry)) {
                                $query.="(" . $val . "," . $v . ",'" . $timer . "')";
                            } else {
                                $query.="(" . $val . "," . $v . ",'" . $timer . "'),";
                            }
                        }
                    }
                    $res = $db->createCommand($query)->execute();
                    if (!$res) {
                        throw new Exception('新增失败');
                    }
                }
                $trans->commit();
                return $this->jsonResult(600, '新增成功');
            } catch (Exception $e) {
                $trans->rollBack();
                return $this->jsonResult(109, $e->getMessage());
            }
        }
    }

    /**
     * 合作商绑定咕啦会员
     */
    public function actionBindUser() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $bussinessId = $get["bussiness_id"];
            if (empty($bussinessId)) {
                return $this->jsonResult(109, "参数缺失");
            }
            return $this->render("bind-user", ["bussinessId" => $bussinessId]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $bussinessId = $post["bussinessId"];
            $user_id = $post["user_id"];
            $cust_no = $post["cust_no"];
            if (empty($bussinessId) || empty($user_id) || empty($cust_no)) {
                return $this->jsonResult(109, "参数缺失");
            }
            $res = Bussiness::updateAll(["user_id" => $user_id, "cust_no" => $cust_no], ["bussiness_id" => $bussinessId]);
            if ($res) {
                return $this->jsonResult(600, "绑定成功");
            } else {
                return $this->jsonResult(109, "绑定失败");
            }
        }
    }

    /**
     * 验证输入是否是已注册咕啦会员
     */
    public function actionGetUserInfo() {
        $post = Yii::$app->request->post();
        $user = User::find()->where(["or", ["user_name" => $post["userInfo"]], ["cust_no" => $post["userInfo"]], ["user_tel" => $post["userInfo"]]])->asArray()->one();
        if (!empty($user)) {
            return $this->jsonResult("600", "获取成功", $user);
        } else {
            return $this->jsonResult("109", "你输入的不是合法的咕啦会员", "");
        }
    }

    /**
     * 生成16位APPID
     */
    public function getAppid() {
        $str = md5(uniqid(md5(microtime(true)), true));
        $appid = "GL" . substr($str, 0, 14);
        return $appid;
    }

    /**
     * 生成32位唯一秘钥
     */
    public function getSecret() {
        $str = md5(uniqid(md5(microtime(true)), true));
        return $str;
    }
    /**
     * 生成24位des加密的key
     */
    public function getDeskey() {
        $str = md5(uniqid(md5(microtime(true)), true));
        $Deskey = strtoupper(substr($str, 0, 24));
        return $Deskey;
    }

    /**
     * 删除合作商信息
     */
    public function actionDelBussiness() {
        $post = Yii::$app->request->post();
        $bussinessId = $post["bussiness_id"];
        if (empty($bussinessId)) {
            return $this->jsonResult(109, "参数缺失");
        }
        $ipRes = BussinessIpWhite::find()->where(["bussiness_id" => $bussinessId])->asArray()->all();
        if (!empty($ipRes)) {
            return $this->jsonResult(109, "删除失败，该合作商存在绑定IP，请先删除名下IP");
        }
        $bussinessRes = Bussiness::deleteAll(["bussiness_id" => $bussinessId]);
        if ($bussinessRes) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }

    /**
     * 删除合作商IP地址
     */
    public function actionDelIp() {
        $post = Yii::$app->request->post();
        $bussiness_ip_white_id = $post["bussiness_ip_white_id"];
        if (empty($bussiness_ip_white_id)) {
            return $this->jsonResult(109, "参数缺失");
        }
        $apiIpRes = ApiIp::find()->where(["bussiness_ip_id" => $bussiness_ip_white_id])->one();
        if (!empty($apiIpRes)) {
            return $this->jsonResult(109, "删除失败，该IP存在接口权限，请先删除所分配权限");
        }
        $ipRes = BussinessIpWhite::deleteAll(["bussiness_ip_white_id" => $bussiness_ip_white_id]);
        if ($ipRes) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }

}
