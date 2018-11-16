<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\SysAuth;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use app\modules\core\filters\AuthFilter;

class AuthController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
        parent::behaviors();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'Editauth' => ['get']
                ]
            ],
//            'auths' => [
//                'class' => AuthFilter::className(),
//                'only' => [
//                    'editauth',
//                    'deletebyid',
//                    'deletebyids',
//                ]
//            ]
        ];
    }

    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $model = new SysAuth();
        if (isset($get["auth_pid"])) {
            $id = $get["auth_pid"];
            $orders = $model->getChildrens($id);
        } else {
            $orders = $model->getAuthOrder();
        }
        $topmodel = new SysAuth();
        $topmodel->auth_pid = 0;
        $toporders = $topmodel->getAuthOrder();

        $tree = array();
        $tree["-1"] = "所有权限";
        $tree[0] = "顶级权限";
        $this->childtree($toporders, $tree, 0, "");

        foreach ($orders as &$value) {
            if ($value["auth_pid"] == 0) {
                $value["auth_pname"] = "顶级权限";
            } else {
                $value["auth_pname"] = $toporders[$value["auth_pid"]]["auth_name"];
            }
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $orders,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render("index", ["data" => $dataProvider, "topAuthames" => $tree]);
    }

    /**
     * 通过id批量删除
     * @return json
     */
    public function actionDeletebyids() {
        $post = Yii::$app->request->post();
        if ($post["type"] == "auth_delete_by_ids") {
            $model = new SysAuth();
            $model->ids = $post["data"];
            $result = $model->deleteByids();
            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 添加权限
     * @return json
     */
    public function actionAddauth() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            $topAuths = SysAuth::find()
                    ->where(["<", "auth_level", 3])
                    ->orderBy('auth_sort')
                    ->asArray()
                    ->all();
            $tree = array();

            $tree[0] = "顶级权限";
//           
            $this->childtree($topAuths, $tree, 0, "");
            return $this->render("addauth", ["model" => $tree]);
        }
        if (isset($post["auth_pid"]) && $post["auth_pid"] > 0) {
            $parentAuth = SysAuth::find()
                    ->where(["auth_pid" => $post["auth_pid"]])
                    ->asArray()
                    ->one();
            $level = $parentAuth["level"] + 1;
            $auth_pid = $post["auth_pid"];
        } else {
            $auth_pid = 0;
            $level = 1;
        }
        $format = 'y/m/d h:i:s';
        $post = Yii::$app->request->post();
        $model = new SysAuth();
        $model->auth_name = $post["auth_name"];
        $model->auth_url = $post["auth_url"];
        $model->auth_status = $post["auth_status"];
        $model->auth_pid = $post["auth_pid"];
        $model->auth_level = $level;
        $model->auth_create_at = date($format);
        $model->auth_update_at = date($format);
        if ($model->validate(["auth_name", "auth_url", "auth_create_at", "auth_update_at", "auth_pid"])) {
            $id = $model->save();
            if ($id != false) {
                return json_encode([
                    "msg" => "新增成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "输入格式错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 通过id删除权限
     * @return json
     */
    public function actionDeletebyid() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect('/admin/auth/index');
        }

        $post = Yii::$app->request->post();
        if ($post["type"] == "auth_delete_by_id") {
            $model = new SysAuth();
            $model->auth_id = $post["auth_id"];
            $result = $model->deleteByid();
            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 编辑权限
     */
    public function actionEditauth() {
        $get = Yii::$app->request->get();
        if (isset($get['auth_id']) && $get['auth_id'] != null) {
            $topAuths = SysAuth::find()
                    ->where(["<", "auth_level", 3])
                    ->orderBy('auth_sort')
                    ->asArray()
                    ->all();
            $tree = array();
            $tree[0] = "顶级权限";
            $this->childtree($topAuths, $tree, 0, "");

            $model = SysAuth::find()
                    ->where(["auth_id" => $get['auth_id']])
                    ->asArray()
                    ->one();
            $model["tree"] = $tree;
            return $this->render('editauth', ['model' => $model]);
        } else {
            echo '操作错误';
            exit();
        }
    }

    /**
     * 修改保存权限
     * @return json
     */
    public function actionSaveauth() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/admin/auth/index');
        }

        $format = 'y/m/d h:i:s';
        $post = Yii::$app->request->post();
        $model = SysAuth::findOne(["auth_id" => $post['auth_id']]);
        $model->auth_name = $post["auth_name"];
        $model->auth_url = $post["auth_url"];
        $model->auth_status = $post["auth_status"];
        $model->auth_pid = $post["auth_pid"];
        $model->auth_update_at = date($format);
        if ($model->validate(["auth_name", "auth_url", "auth_create_at", "auth_update_at", "auth_pid"])) {
            $id = $model->save();
            if ($id != false) {
                return json_encode([
                    "msg" => "修改成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "输入格式错误",
                "code" => 2
            ]);
        }
    }

    public function actionChangesort() {
        $post = Yii::$app->request->post();
        $model = SysAuth::findOne($post["auth_id"]);
        $model->auth_sort = $post["auth_sort"];
        if ($model->validate()) {
            $ret = $model->save();
            if ($ret === false) {
                return json_encode([
                    "msg" => "输入格式错误",
                    "code" => 2
                ]);
            } else {
                return json_encode([
                    "msg" => "修改成功",
                    "code" => 0
                ]);
            }
        } else {
            return json_encode([
                "msg" => "输入格式错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 生成权限子集效果
     */
    public function childtree($info, &$tree, $pid = 0, $str) {
        $str.="|--";
        if (!empty($info)) {
            foreach ($info as $k => &$v) {
                if ($v['auth_pid'] == $pid) {
                    $tree[$v["auth_id"]] = $str . $v["auth_name"];
                    $this->childtree($info, $tree, $v["auth_id"], $str);
                    unset($info[$k]);
                }
            }
        }
    }

}
