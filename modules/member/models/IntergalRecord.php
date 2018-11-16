<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "intergal_record".
 *
 * @property integer $intergal_record_id
 * @property integer $user_id
 * @property string $user_name
 * @property string $cust_no
 * @property string $intergal_source
 * @property integer $intergal_value
 * @property integer $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class IntergalRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'intergal_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_name', 'cust_no', 'intergal_source', 'intergal_value'], 'required'],
            [['user_id', 'intergal_value', 'opt_id'], 'integer'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['user_name'], 'string', 'max' => 50],
            [['cust_no', 'intergal_source'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'intergal_record_id' => 'Intergal Record ID',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'cust_no' => 'Cust No',
            'intergal_source' => 'Intergal Source',
            'intergal_value' => 'Intergal Value',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
