<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "jd_url".
 *
 * @property integer $jd_url_id
 * @property string $url
 * @property integer $status
 * @property string $opt_name
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class JdUrl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_url';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['url'], 'string', 'max' => 250],
            [['opt_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jd_url_id' => 'Jd Url ID',
            'url' => 'Url',
            'status' => 'Status',
            'opt_name' => 'Opt Name',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
