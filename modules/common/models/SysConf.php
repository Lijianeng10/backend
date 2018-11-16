<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "sys_conf".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $value
 * @property integer $type
 * @property integer $status
 * @property string $remark
 * @property string $modify_time
 * @property string $create_time
 */
class SysConf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['type', 'status'], 'integer'],
            [['modify_time', 'create_time'], 'safe'],
            [['code', 'name', 'value','remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'value' => 'Value',
            'type' => 'Type',
            'status' => 'Status',
            'remark' => 'Remark',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
        ];
    }
}
