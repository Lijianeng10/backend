<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "sys_admin".
 *
 * @property integer $admin_id
 * @property string $admin_name
 * @property string $password
 * @property string $admin_code
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $nickname
 * @property string $admin_tel
 * @property string $last_login
 * @property integer $admin_pid
 * @property string $update_time
 */
class SysAdmin extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sys_admin';
    }

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['admin_name', 'password', 'created_at', 'updated_at'], 'required'],
            [['type', 'status', 'admin_pid'], 'integer'],
            [['created_at', 'updated_at', 'last_login', 'update_time'], 'safe'],
            [['admin_name', 'nickname'], 'string', 'max' => 50],
            [['password', 'admin_code'], 'string', 'max' => 255],
            [['admin_tel'], 'string', 'max' => 20],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'admin_id' => 'Admin ID',
            'admin_name' => 'Admin Name',
            'password' => 'Password',
            'admin_code' => 'Admin Code',
            'type' => 'Type',
            'type_identity' => 'Type Identity',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'nickname' => 'Nickname',
            'admin_tel' => 'Admin Tel',
            'last_login' => 'Last Login',
            'admin_pid' => 'Admin Pid',
            'update_time' => 'Update Time',
        ]; 
    } 

    /**
     * 密码验证
     */
    public function validatePassword() {
        if ($this->password != null) {
            $adminInfo = $this->findOne(["admin_name" => $this->admin_name]);
            if ($adminInfo != null && $adminInfo->password == $this->password) {
                $this->admin_id = $adminInfo['admin_id'];
                $this->nickname = $adminInfo['nickname'];
                $this->last_login = $adminInfo['last_login'];
                $this->admin_code = $adminInfo['admin_code'];
                $this->type = $adminInfo['type'];
                $this->type_identity = $adminInfo['type_identity'];
                return true;
            } else {
                return false;
            }
            return Yii::$app->security->validatePassword($this->password, $adminInfo->password);
        }
    }

    public function deleteByids() {
        if ($this->validate(["ids"])) {
            $model = new Query();
            Yii::$app->db->createCommand()->delete('sys_admin_role', ['in', 'admin_id', $this->ids])->execute();
            $adminDel = $this->deleteAll(['in', 'admin_id', $this->ids]);
            return $adminDel;
        }
        return false;
    }

    public function deleteByid() {
        if ($this->validate(["admin_id"])) {
            Yii::$app->db->createCommand()->delete('sys_admin_role', ['admin_id' => $this->admin_id])->execute();
            $adminDel = $this->deleteAll(["admin_id" => $this->admin_id]);
            return $adminDel;
        }
        return false;
    }

}
