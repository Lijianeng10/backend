<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "picture".
 *
 * @property integer $picture_id
 * @property string $picture_type_code
 * @property string $picture_type_name
 * @property string $picture_url
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class Picture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picture';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['picture_type_code', 'picture_type_name', 'picture_url'], 'required'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['picture_type_code'], 'string', 'max' => 100],
            [['picture_type_name'], 'string', 'max' => 50],
            [['picture_url'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'picture_id' => 'Picture ID',
            'picture_type_code' => 'Picture Type Code',
            'picture_type_name' => 'Picture Type Name',
            'picture_url' => 'Picture Url',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
