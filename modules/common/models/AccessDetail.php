<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "access_detail".
 *
 * @property integer $access_detail_id
 * @property string $date_time
 * @property string $param
 * @property string $value
 * @property integer $numbers_time
 * @property integer $numbers_people
 * @property string $info_type
 * @property string $create_time
 */
class AccessDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numbers_time', 'numbers_people'], 'integer'],
            [['create_time'], 'safe'],
            [['date_time', 'info_type'], 'string', 'max' => 50],
            [['param', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_detail_id' => 'Access Detail ID',
            'date_time' => 'Date Time',
            'param' => 'Param',
            'value' => 'Value',
            'numbers_time' => 'Numbers Time',
            'numbers_people' => 'Numbers People',
            'info_type' => 'Info Type',
            'create_time' => 'Create Time',
        ];
    }
}
