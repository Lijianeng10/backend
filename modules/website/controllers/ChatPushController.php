<?php

namespace app\modules\website\controllers;

use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\common\helpers\PublicHelpers;
use app\modules\common\models\ChatPush;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\common\services\ApiSysService;


/**
 * Default controller for the `index` module
 */
class ChatPushController extends Controller
{
    public function actionIndex()
    {
        $get = \Yii::$app->request->get();
        $type = PublicHelpers::PUSH_TYPE;
        $sendType = PublicHelpers::PUSH_USER_TYPE;
        $status = PublicHelpers::PUSH_STATUS;
        $chatPush = ChatPush::find();
        if (!empty($get["push_info"])) {
            $chatPush = $chatPush->where(["or", ["like", "title", $get["push_info"]], ["like", "content", $get["push_info"]]]);
        }
        if (!empty($get["status"])) {
            $chatPush = $chatPush->where(["status" => $get["status"]]);
        }
        if (!empty($get["startdate"])) {
            $chatPush = $chatPush->andWhere([">", "create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (!empty($get["enddate"])) {
            $chatPush = $chatPush->andWhere(["<", "create_time", $get["enddate"] . " 23:59:59"]);
        }
        if (!empty($get["type"])) {
            $chatPush = $chatPush->where(["type" => $get["type"]]);
        }
        $chatPush = $chatPush->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $chatPush,
        ]);
        return $this->render('index', ['data' => $data, 'get' => $get, 'status' => $status, "type" => $type, "sendType" => $sendType]);
    }

    /**
     * 新增app推送消息
     */
    public function actionAddChatPush()
    {
        if (Yii::$app->request->isGet) {
//           $this->layout = false;
            $type = PublicHelpers::PUSH_TYPE;
            $sendType = PublicHelpers::PUSH_USER_TYPE;
            $picJump = PublicHelpers::JUMP_TYPE;
            return $this->render("add", ["type" => $type, "sendType" => $sendType, "picJump" => $picJump]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $title = $post["title"];
            $content = $post["content"];
            $type = $post["type"];
            $jumpUrl = $post["jump_url"];
            $jumpType = $post["jumpType"];
            $timer = date('Y-m-d H:i:s');
            if ($title == "" || $content == "" || empty($type)) {
                return $this->jsonResult(109, '参数有误，请检查重新上传');
            }
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/bananer/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0, 99) . '.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir, $name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            } else {
                $picUrl = "";
//               return $this->jsonResult(109, '请上传广告图', '');
            }
            $chatPush = new ChatPush();
            $chatPush->title = $title;
            $chatPush->content = $content;
            $chatPush->img_url = $picUrl;
            $chatPush->jump_type = $jumpType;
            $chatPush->jump_url = $jumpUrl;
            $chatPush->type = $type;
            $chatPush->send_type = 2;
            $chatPush->create_time = $timer;
            $chatPush->opt_name = $session['nickname'];
            if ($chatPush->validate()) {
                $result = $chatPush->save();
                if ($type == 21 || $jumpType == 1) {
                    $id = $chatPush->attributes['chat_push_id'];
                    $url = \Yii::$app->params["userDomain"] . "/article/push_id_" . $id;
                    ChatPush::updateAll(["jump_url" => $url], ["chat_push_id" => $id]);
                }
                if ($result == false) {
                    return $this->jsonResult(109, '新增失败');
                }
                return $this->jsonResult(600, '新增成功');
            } else {
                return $this->jsonResult(109, '表单验证失败', $chatPush->errors);
            }

        }
    }

    /**
     * app消息推送商审核页面
     */
    public function actionAuditAppLog()
    {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $res = JpushRecord::find()->where(["jpush_notice_id" => $post["jpush_notice_id"]])->one();
            if (empty($res)) {
                return $this->jsonResult(109, "操作失败，未找到该条数据！");
            }
            if ($post["pass_status"] == 3) {
                if (empty($post["review_remark"])) {
                    return $this->jsonResult(109, "未通过审核备注不得为空！");
                }
            }
            $result = JpushRecord::updateAll(["status" => $post["pass_status"], "remark" => $post["review_remark"], "remark_name" => \Yii::$app->session["admin_name"]], ["jpush_notice_id" => $post["jpush_notice_id"]]);
            if ($result) {
                return $this->jsonResult(600, "操作成功！");
            } else {
                return $this->jsonResult(109, "操作失败！");
            }
        } else {
            $get = \Yii::$app->request->get();
            $result = JpushRecord::findOne(["jpush_notice_id" => $get["jpush_notice_id"]]);
            return $this->render("audit-log", ["data" => $result]);
        }
    }

    /**
     * 发送推送消息
     */
    public function actionSendPush()
    {
        $post = Yii::$app->request->post();
        $session = Yii::$app->session;
        $chatPush = new ChatPush();
        $chat_push_id = $post["chat_push_id"];
        $nowdate = date("Y-m-d H:i:s");
        if (empty($chat_push_id)) {
            return $this->jsonResult(109, "参数缺失！");
        }
        $res = ChatPush::find()->where(["chat_push_id" => $chat_push_id])->asArray()->one();
        //获取签名
        $sign = $this->createSign($res["title"], $res["content"]);
        //推送消息
        $data = json_encode([
            "title" => $res["title"],
            "create_date" => $nowdate,
            "desc" => $res["content"],
            "url" => $res["jump_url"],
            "logo" => $res["img_url"],
            "sendType" => (int)$res["send_type"],
            "msgType" => (int)$res["type"],
            "toUser" => $res["send_user"],
            "sign" => $sign
        ]);
        $pushRes = ApiSysService::sendChatPush($data);
        if ($pushRes["code"] == 600) {
            $status = 1;
        } else {
            $status = 2;
        }
        $push = ChatPush::updateAll(["status" => $status, "push_time" => $nowdate, "opt_name" => $session['nickname']], ["chat_push_id" => $chat_push_id]);
        if ($pushRes["code"] == 600) {
            return $this->jsonResult(600, "推送成功！");
        } else {
            return $this->jsonResult(109, "推送失败！");
        }
    }

    /**
     * 删除推送消息
     */
    public function actionDeletePush()
    {
        $post = Yii::$app->request->post();
        if (empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失！");
        }
        $push = ChatPush::deleteAll(["chat_push_id" => $post["id"]]);
        if ($push) {
            return $this->jsonResult(600, "操作成功！");
        } else {
            return $this->jsonResult(109, "操作失败！");
        }
    }

    /**
     * 编辑广告
     */
    public function actionEditChatPush()
    {
        if (Yii::$app->request->isGet) {
//            $this->layout = false;
            $get = Yii::$app->request->get();
            $type = PublicHelpers::PUSH_TYPE;
            $sendType = PublicHelpers::PUSH_USER_TYPE;
            $picJump = PublicHelpers::JUMP_TYPE;
            $id = $get["chat_push_id"];
            if (empty($id)) {
                return $this->jsonResult(109, '参数有误', '');
            }
            $chatPush = ChatPush::find()->where(["chat_push_id" => $id])->asArray()->one();
            return $this->render('edit', ["data" => $chatPush, "type" => $type, "sendType" => $sendType, "picJump" => $picJump]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $id = $post["chat_push_id"];
            $title = $post["title"];
            $content = $post["content"];
            $type = $post["type"];
            $jumpUrl = $post["jump_url"];
            $jumpType = $post["jumpType"];
            $timer = date('Y-m-d H:i:s');
            if ($title == "" || empty($type)) {
                return $this->jsonResult(109, '参数有误，请检查重新上传');
            }
            if (isset($_FILES['upfile']) && $_FILES['upfile'] != "Undefined") {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/bananer/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0, 99) . '.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir, $name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            } else {
                $chat = ChatPush::find()->where(["chat_push_id" => $id])->one();
                $picUrl = $chat->img_url;
            }
            $chatPush = ChatPush::find()->where(["chat_push_id" => $id])->one();
            $chatPush->title = $title;
            $chatPush->img_url = $picUrl;
            $chatPush->content = $content;
            $chatPush->type = $type;
            if ($jumpType != 1) {
                $chatPush->jump_url = $jumpUrl;
            }
            $chatPush->jump_type = $jumpType;
            $chatPush->opt_name = $session['nickname'];
            if ($chatPush->validate()) {
                $result = $chatPush->save();
                if ($type == 21 || $jumpType == 1) {
                    $id = $chatPush->attributes['chat_push_id'];
                    $url = \Yii::$app->params["userDomain"] . "/article/push_id_" . $id;
                    ChatPush::updateAll(["jump_url" => $url], ["chat_push_id" => $id]);
                }
                if ($result == false) {
                    return $this->jsonResult(109, '编辑失败');
                }
                return $this->jsonResult(600, '编辑成功');
            } else {
                return $this->jsonResult(109, '表单验证失败', $chatPush->errors);
            }
        }
    }

    /**
     * 推送消息签名
     */
    public function createSign($title, $desc)
    {
        $key = "FB579760AA4C4429A5FEE972B3DF5677";
        return md5($title . $desc . $key);
    }

    /**
     * 查看推送内容
     */
    public function actionReadChatPush()
    {
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $type = PublicHelpers::PUSH_TYPE;
            $sendType = PublicHelpers::PUSH_USER_TYPE;
            $picJump = PublicHelpers::JUMP_TYPE;
            $id = $get["chat_push_id"];
            if (empty($id)) {
                return $this->jsonResult(109, '参数有误', '');
            }
            $chatPush = ChatPush::find()->where(["chat_push_id" => $id])->asArray()->one();
            return $this->render('read', ["data" => $chatPush, "type" => $type, "sendType" => $sendType, "picJump" => $picJump]);

        }
    }
}
