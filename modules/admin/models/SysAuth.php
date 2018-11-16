<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "sys_auth".
 *
 * @property integer $auth_id
 * @property string $auth_name
 * @property string $auth_url
 * @property string $auth_create_at
 * @property string $auth_update_at
 */
class SysAuth extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sys_auth';
    }

    /**
     * @inheritdoc
     */
    public $ids;

    public function rules() {
        return [
            [['auth_name', 'auth_create_at', 'auth_update_at'], 'required'],
            [['auth_create_at', 'auth_update_at'], 'safe'],
            [['auth_name'], 'string', 'max' => 50],
            [['auth_url'], 'string', 'max' => 127],
            ['ids', 'each', 'rule' => ['integer']],
            ['auth_status', 'in', 'range' => [1, 0]],
            ['auth_pid', 'integer'],
            ['auth_sort', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'auth_id' => 'Auth ID',
            'auth_name' => 'Auth Name',
            'auth_url' => 'Auth Url',
            'auth_status' => 'Auth Status',
            'auth_create_at' => 'Auth Create At',
            'auth_update_at' => 'Auth Update At',
        ];
    }

    public function getAuthOrder() {
        if ($this->auth_pid != null && $this->validate(["auth_pid"])) {
            $authOrder = $this->find()->where(["auth_pid" => $this->auth_pid])->orderBy("auth_sort desc,auth_id asc")->indexBy("auth_id")->asArray()->all();
        } else {
            $authOrder = $this->find()->indexBy("auth_id")->asArray()->orderBy("auth_sort desc,auth_id asc")->all();
        }
        return $authOrder;
    }

    public function getChildrens($id) {
        $orders = $this->find()->where(["auth_id" => $id])->asArray()->all();
        $childOrders = $this->find()->where(["auth_pid" => $id])->orderBy("auth_sort desc,auth_id asc")->asArray()->all();
        $orders = array_merge($orders, $childOrders);
//        if (is_array($childOrders) && count($childOrders)) {
//            foreach ($childOrders as $key => $val) {
//                $childrensOrders = $this->getChildrens($val["auth_id"]);
//                if (is_array($childrensOrders) && count($childrensOrders)) {
//                    $orders = array_merge($orders, $childrensOrders);
//                }
//            }
//        }
        return $orders;
    }

    public function deleteByids() {
        if ($this->validate(["ids"])) {
            $idarr = $this->ids;
            foreach ($idarr as $val) {
                $ispid = $this->find()->where(['auth_pid' => $val])->asArray()->all();
                if ($ispid != null && $ispid != false) {
                    foreach ($ispid as $item) {
                        $idarr[] = $item['auth_id'];
                    }
                }
            }
            $authDel = $this->deleteAll(['in', 'auth_id', $idarr]);
            return $authDel;
        }
        return false;
    }

    public function deleteByid() {
        if ($this->validate(["auth_id"])) {
            $id = $this->auth_id;
            $ispid = $this->find()->where(['auth_pid' => $id])->asArray()->all();
            if ($ispid != null && $ispid != false) {
                $idarr[] = $id;
                foreach ($ispid as $item) {
                    $idarr[] = $item['auth_id'];
                }
                $authDel = $this->deleteAll(['in', 'auth_id', $idarr]);
            } else {
                $authDel = $this->deleteAll(["auth_id" => $this->auth_id]);
            }

            return $authDel;
        }
        return false;
    }

    /**
     * 获取权限url
     * @return array
     */
    public function getAuthurls() {
        $session = Yii::$app->session;
        $admin_role_model = new Query();
        $roleIds = $admin_role_model->select('admin_role.role_id')
                ->from(['admin_role' => 'sys_admin_role'])
                ->leftJoin(['role' => 'sys_role'], 'role.role_id = admin_role.role_id')
                ->where(['role.role_status' => '1'])
                ->andWhere(['admin_role.admin_id' => $session['admin_id']]);
        $role_auth_model = new Query();
        $authIds = $role_auth_model->select('auth_id')
                ->from('sys_role_auth')
                ->where(['in', 'role_id', $roleIds]);

        $auth_model = new Query();
        $authUrls = $auth_model->select('auth_url')
                ->from('sys_auth')
                ->where(['auth_status' => '1'])
                ->andWhere(['in', 'auth_id', $authIds])
                ->all();
        return $authUrls;
    }
    /**
     * 获取当前角色权限菜单
     * @return array
     */
    public function getNowAuthurls() {
        $session = Yii::$app->session;
        $admin_role_model = new Query();
        $roleIds = $admin_role_model->select('admin_role.role_id')
                ->from(['admin_role' => 'sys_admin_role'])
                ->leftJoin(['role' => 'sys_role'], 'role.role_id = admin_role.role_id')
                ->where(['role.role_status' => '1'])
                ->andWhere(['admin_role.admin_id' => $session['admin_id']]);
        $role_auth_model = new Query();
        $authIds = $role_auth_model->select('auth_id')
                ->from('sys_role_auth')
                ->where(['in', 'role_id', $roleIds]);

        $auth_model = new Query();
        $authUrls = $auth_model->select('*')
                ->from('sys_auth')
                ->where(['auth_status' => '1'])
                ->andWhere(['in', 'auth_id', $authIds])
                ->all();
        return $authUrls;
    }

}
