<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "exgift_record".
 *
 * @property integer $exgift_record_id
 * @property integer $exchange_id
 * @property string $exch_code
 * @property string $gift_code
 * @property string $gift_name
 * @property integer $gift_nums
 * @property integer $exch_int
 * @property integer $all_int
 * @property string $send_gift
 * @property string $create_time
 * @property string $update_time
 */
class ExgiftRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exgift_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exchange_id', 'exch_code', 'gift_code'], 'required'],
            [['exchange_id', 'gift_nums', 'exch_int', 'all_int'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['exch_code', 'gift_code', 'gift_name'], 'string', 'max' => 50],
            [['send_gift'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exgift_record_id' => 'Exgift Record ID',
            'exchange_id' => 'Exchange ID',
            'exch_code' => 'Exch Code',
            'gift_code' => 'Gift Code',
            'gift_name' => 'Gift Name',
            'gift_nums' => 'Gift Nums',
            'exch_int' => 'Exch Int',
            'all_int' => 'All Int',
            'send_gift' => 'Send Gift',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
} 
