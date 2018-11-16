<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery".
 *
 * @property integer $lottery_id
 * @property string $lottery_code
 * @property string $lottery_name
 * @property string $description
 * @property integer $lottery_category_id
 * @property integer $status
 * @property integer $sale_status
 * @property string $lottery_pic
 * @property integer $lottery_sort
 * @property integer $result_status
 * @property integer $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class Lottery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_code', 'lottery_name'], 'required'],
            [['lottery_category_id', 'status', 'sale_status', 'lottery_sort', 'result_status', 'opt_id'], 'integer'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['lottery_code'], 'string', 'max' => 10],
            [['lottery_name'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 200],
            [['lottery_pic'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_id' => 'Lottery ID',
            'lottery_code' => 'Lottery Code',
            'lottery_name' => 'Lottery Name',
            'description' => 'Description',
            'lottery_category_id' => 'Lottery Category ID',
            'status' => 'Status',
            'sale_status' => 'Sale Status',
            'lottery_pic' => 'Lottery Pic',
            'lottery_sort' => 'Lottery Sort',
            'result_status' => 'Result Status',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * 获取全部彩种名称
     * @return array
     */
    public function getLotterynamelist($condition = "1=1") {
        $result = $this->find()
                ->select("lottery_code,lottery_name")
                ->where($condition)
                ->asArray()
                ->all();
        $ret = [];
        $ret[0] = '请选择';
        foreach ($result as $val) {
            $ret[$val["lottery_code"]] = $val["lottery_name"];
        }
        return $ret;
    }
}
