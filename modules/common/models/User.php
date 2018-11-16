<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_tel
 * @property string $user_land
 * @property integer $user_sex
 * @property string $cust_no
 * @property string $user_pic
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $invite_code
 * @property integer $is_inviter
 * @property integer $user_type
 * @property integer $is_operator
 * @property integer $spread_type
 * @property string $rebate
 * @property integer $status
 * @property integer $authen_status
 * @property string $authen_remark
 * @property integer $register_from
 * @property string $from_id
 * @property string $level_name
 * @property integer $level_id
 * @property integer $is_profit
 * @property string $p_tree
 * @property string $agent_code
 * @property string $agent_name
 * @property integer $agent_id
 * @property string $user_remark
 * @property string $my_inviter
 * @property integer $send_status
 * @property integer $opt_id
 * @property string $last_login
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'user_tel', 'p_tree'], 'required'],
            [['user_sex', 'is_inviter', 'user_type', 'is_operator', 'spread_type', 'status', 'authen_status', 'register_from', 'level_id', 'is_profit', 'agent_id', 'send_status', 'opt_id'], 'integer'],
            [['rebate'], 'number'],
            [['last_login', 'modify_time', 'create_time', 'update_time'], 'safe'],
            [['user_name', 'province', 'city', 'area', 'from_id'], 'string', 'max' => 50],
            [['user_tel', 'user_land'], 'string', 'max' => 12],
            [['cust_no', 'my_inviter'], 'string', 'max' => 15],
            [['user_pic'], 'string', 'max' => 256],
            [['address', 'invite_code', 'authen_remark', 'level_name'], 'string', 'max' => 100],
            [['p_tree', 'agent_code', 'agent_name', 'user_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_tel' => 'User Tel',
            'user_land' => 'User Land',
            'user_sex' => 'User Sex',
            'cust_no' => 'Cust No',
            'user_pic' => 'User Pic',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'invite_code' => 'Invite Code',
            'is_inviter' => 'Is Inviter',
            'user_type' => 'User Type',
            'is_operator' => 'Is Operator',
            'spread_type' => 'Spread Type',
            'rebate' => 'Rebate',
            'status' => 'Status',
            'authen_status' => 'Authen Status',
            'authen_remark' => 'Authen Remark',
            'register_from' => 'Register From',
            'from_id' => 'From ID',
            'level_name' => 'Level Name',
            'level_id' => 'Level ID',
            'is_profit' => 'Is Profit',
            'p_tree' => 'P Tree',
            'agent_code' => 'Agent Code',
            'agent_name' => 'Agent Name',
            'agent_id' => 'Agent ID',
            'user_remark' => 'User Remark',
            'my_inviter' => 'My Inviter',
            'send_status' => 'Send Status',
            'opt_id' => 'Opt ID',
            'last_login' => 'Last Login',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public function getUserFunds(){
        return $this->hasOne(UserFunds::className(), ['cust_no' => 'cust_no']);
    }
    public function getUserFollow(){
        return $this->hasOne(UserFollow::className(), ['cust_no' => 'cust_no']);
    }
}
