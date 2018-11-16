<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "ticket_dispenser".
 *
 * @property integer $ticket_dispenser_id
 * @property integer $type
 * @property string $dispenser_code
 * @property string $vender_id
 * @property string $sn_code
 * @property integer $store_no
 * @property integer $pre_out_nums
 * @property integer $mod_nums
 * @property integer $status
 * @property string $out_lottery
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class TicketDispenser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_dispenser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'store_no', 'pre_out_nums', 'mod_nums', 'status'], 'integer'],
            [['dispenser_code', 'store_no'], 'required'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['dispenser_code'], 'string', 'max' => 100],
            [['vender_id', 'sn_code'], 'string', 'max' => 50],
            [['out_lottery'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_dispenser_id' => 'Ticket Dispenser ID',
            'type' => 'Type',
            'dispenser_code' => 'Dispenser Code',
            'vender_id' => 'Vender ID',
            'sn_code' => 'Sn Code',
            'store_no' => 'Store No',
            'pre_out_nums' => 'Pre Out Nums',
            'mod_nums' => 'Mod Nums',
            'status' => 'Status',
            'out_lottery' => 'Out Lottery',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
