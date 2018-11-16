<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "sys_role".
 *
 * @property integer $role_id
 * @property string $role_name
 * @property string $login_port
 * @property integer $admin_id
 * @property string $role_create_at
 * @property string $role_update_at
 * @property integer $role_status
 * @property string $update_time
 */
class SysRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_role';
    }

    /**
     * @inheritdoc
     */
    public function rules() 
    { 
        return [
            [['role_name', 'admin_id', 'role_create_at', 'role_update_at'], 'required'],
            [['admin_id', 'role_status'], 'integer'],
            [['role_create_at', 'role_update_at', 'update_time'], 'safe'],
            [['role_name'], 'string', 'max' => 50],
        ]; 
    } 

    /**
     * @inheritdoc
     */
    public function attributeLabels() 
    { 
        return [ 
            'role_id' => 'Role ID',
            'role_name' => 'Role Name',
            'admin_id' => 'Admin ID',
            'role_create_at' => 'Role Create At',
            'role_update_at' => 'Role Update At',
            'role_status' => 'Role Status',
            'update_time' => 'Update Time',
        ]; 
    } 
    
    public function roleList(){
        $session = Yii::$app->session;
        $roleAry=(new Query)->select("r.role_id")
                ->from("sys_admin_role as s")
                ->leftJoin("sys_role as r","r.role_id = s.role_id")
                ->where(["s.admin_id"=>$session["admin_id"]])
                ->one();
        if($roleAry["role_id"]!=24){
            $list = SysRole::find()->where(["or",["admin_id"=>$session["admin_id"]],["role_id"=>$roleAry["role_id"]]])->all();
        }else{
            $list = SysRole::find()->all();
        }
        return $list;
    }
}
