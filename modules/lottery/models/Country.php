<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $country_id
 * @property string $country_name
 * @property string $country_code
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_name', 'country_code'], 'required'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['country_name'], 'string', 'max' => 150],
            [['country_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country_id' => 'Country ID',
            'country_name' => 'Country Name',
            'country_code' => 'Country Code',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
     /**
     * 获取国家
     * @return array
     */
    public function getCountryList() {
        $result = $this->find()
                ->select("country_code,country_name")
                ->asArray()
                ->all();
        $ret['0'] = '请选择';
        foreach ($result as $val) {
            $ret[$val["country_code"]] = $val["country_name"];
        }
        return $ret;
    }
}
