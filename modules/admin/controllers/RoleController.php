<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\SysAuth;
use app\modules\admin\models\SysRole;
use app\modules\admin\models\SysAdmin;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\db\Exception;

/**
 * Default controller for the `admin` module
 */
class RoleController extends Controller {

    const ADMIN_ROLE = 24; //总管理员角色ID

    /**
     */

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $role = new SysRole;
        $detail = $role->roleList();
        $provider = new ArrayDataProvider([
            'allModels' => $detail,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['role_id', 'role_status'],
            ],
        ]);

        return $this->render('index', ['provider' => $provider]);
    }

    /**
     * 新增角色
     */
    public function actionAddrole() {
        $this->layout = false;
        if (!Yii::$app->request->isPost) {
            return $this->render('addrole');
        }

        $param = Yii::$app->request->post();
        $roleInfo = SysRole::find()->where(["role_name"=>$param["role_name"]])->one();
        if (!empty($roleInfo)) {
            return $this->jsonResult(2, "角色名不能重复，请检查");
        }
        $session = Yii::$app->session;
        $at_time = 'y/m/d h:i:s';
        $role = new SysRole();
        $role->role_name = $param["role_name"];
        $role->admin_id = $session["admin_id"];
        $role->role_create_at = date($at_time);
        $role->role_update_at = date($at_time);

        if ($role->validate(["role_name", "admin_id ", "role_create_at", "role_update_at", "role_status"])) {
            $r_id = $role->save();
            if ($r_id != false) {
                return $this->jsonResult(1, "添加成功");
            } else {
                return $this->jsonResult(2, "添加失败");
            }
        } else {
            return $this->jsonResult(2, "输入的数据格式有误");
        }
    }

    /**
     * 删除角色,通过id
     * @return json
     */
    public function actionDelbyid() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect('/admin/role/index');
        }

        $post = Yii::$app->request->post();
        if ($post["type"] == "role_delete_by_id") {
            if (!is_array($post['ids'])) {
                $id = array($post['ids']);
            } else {
                $id = $post['ids'];
            }
            $used = (new Query)->select('role_id')->from('sys_admin_role')->where(['in', 'role_id', $id])->all();
            if ($used != null) {
                return json_encode([
                    "msg" => '该角色已分配用户,不可被删除',
                    "code" => 2
                ]);
            }
            $result = SysRole::deleteAll(['in', 'role_id', $id]);

            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作失败",
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
     * 修改角色状态
     * @return json
     */
    public function actionEditsta() {

        $post = Yii::$app->request->post();
        $id = $post['id'];
        $status = $post['status'];

        if (empty($id) || $status == null) {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }

        $role = SysRole::find()->where(['role_id' => $id])->one();
        $role->role_status = $status;
        if ($role->save()) {
            return json_encode(["msg" => "操作成功", "code" => 1]);
        } else {
            return json_encode(["msg" => "操作错误", "code" => 2]);
        }
    }

    public function actionAccess() {
        $session = Yii::$app->session;
        if (!isset($session["role_ids"])) {//查询角色ID
            $roleIds = (new Query())->select('role_id')->from('sys_admin_role')->where(['admin_id' => $session["admin_id"]])->all();
            $roleIds = array_column($roleIds, 'role_id');
            $session["role_ids"] = $roleIds;
        }
        $get = Yii::$app->request->get();
        if (isset($get['role_id']) && $get['role_id'] != null) {
            $role_id = $get['role_id'];
            if (in_array(self::ADMIN_ROLE, $session["role_ids"])) { // 如果是总管理员
                $authData = (new Query())->select('*')->from('sys_auth')->where(["auth_status" => 1])->orderBy("auth_create_at asc")->all();
            } else {
                $authModel = new SysAuth();
                $authData = $authModel->getNowAuthurls();
            }
            $rows = (new Query())->select('auth_id')->from('sys_role_auth')->where(['role_id' => $role_id])->all();
            $rows = array_column($rows, 'auth_id');
            foreach ($authData as &$val) {
                if (in_array($val['auth_id'], $rows)) {
                    $val['ischecked'] = '1';
                } else {
                    $val['ischecked'] = '0';
                }
            }
        } else {
            echo '操作错误';
            exit();
        }
        $tree = array();
        $control = $this->actionTree($authData, $tree);
        return $this->render('access', ['provider' => $control]);
    }

    public function actionTree($info, $child, $pid = 0) {
        $child = array();
        if (!empty($info)) {
            foreach ($info as $k => &$v) {
                if ($v['auth_pid'] == $pid) {
                    $v['child'] = $this->actionTree($info, $child, $v['auth_id']);
                    $child[] = $v;
                    unset($info[$k]);
                }
            }
        }
        return $child;
    }

    public function actionUpauth() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/admin/role/index');
        }
        $post = $post = Yii::$app->request->post();
        $role_id = $post['role_id'];
        $role_auth = json_decode($post['authIds']);

        if (empty($role_id)) {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
        $rows = [];

        if ($role_auth != null) {
            foreach ($role_auth as $value) {
                $rows[] = [$role_id, $value];
            }
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $detail = (new Query)->select('role_id')->from('sys_role_auth')->where(array('role_id' => $role_id))->all();

            if (count($detail) > 0) {
                $deleteId = $db->createCommand()->delete('sys_role_auth', [ 'role_id' => $role_id])->execute();
                if ($deleteId == false) {
                    throw new Exception('操作删除已有数据失败！');
                }
            }

            $updateId = $db->createCommand()->batchInsert('sys_role_auth', [ 'role_id', 'auth_id'], $rows)->execute();

            if ($updateId === false) {
                throw new Exception('操作添加新数据失败！');
            }

            $transaction->commit();
            return json_encode([
                "msg" => "修改成功",
                "code" => 1
            ]);
        } catch (Exception $e) {
            $transaction->rollBack();
            return json_encode([
                "msg" => $e->getMessage(),
                "code" => 2
            ]);
        }
    }

    public function actionAdminrole() {
        $get = Yii::$app->request->get();
        if (isset($get['role_id']) && $get['role_id'] != null) {
            $role_id = $get['role_id'];
            $idArr = array();
            $rows = (new Query)->select('admin_id')->from('sys_admin_role')->where(array('role_id' => $role_id))->all();
            foreach ($rows as $val) {
                $idArr[] = $val['admin_id'];
            }
            // $sid = ['in',$idArr];
            $admin = (new Query)->select('*')->from('sys_admin')->where(['in', 'admin_id', $idArr])->all();
        } else {
            echo '操作错误';
            return $this->redirect('/admin/role/index');
        }
        foreach ($admin as &$val) {
            $val['role_id'] = $role_id;
        }
        $provider = new ArrayDataProvider([
            'allModels' => $admin,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => ['auth_id', 'auth_status'],
            ],
        ]);
        return $this->render('adminrole', ['provider' => $provider]);
    }

    public function actionDeladminrolebyid() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect('/admin/role/adminrole');
        }

        $post = Yii::$app->request->post();
        $admin_id = $post['adminId'];
        $role_id = $post['roleId'];
        if (empty($admin_id) || empty($role_id)) {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
        $session = Yii::$app->session;
        if ($admin_id == $session["admin_id"] || $admin_id == 1) {
            return json_encode([
                "msg" => "该用户不可删除！",
                "code" => 2
            ]);
        }
        if ($post["type"] == "admin_role_delete_by_id") {
            $result = Yii::$app->db->createCommand()->delete("sys_admin_role", "admin_id = :admin_id and role_id = :role_id", array(":admin_id" => $admin_id, ":role_id" => $role_id))->execute();

            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作失败",
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

}
