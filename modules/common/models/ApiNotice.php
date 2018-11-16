<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "api_notice".
 *
 * @property integer $notice_id
 * @property string $periods
 * @property integer $type
 * @property integer $status
 * @property string $remark
 */
class ApiNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_notice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['periods'], 'string', 'max' => 25],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notice_id' => 'Notice ID',
            'periods' => 'Periods',
            'type' => 'Type',
            'status' => 'Status',
            'remark' => 'Remark',
        ];
    }
}
