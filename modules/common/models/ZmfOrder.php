<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "zmf_order".
 *
 * @property integer $zmf_order_id
 * @property string $order_code
 * @property string $version
 * @property string $command
 * @property string $messageId
 * @property string $status
 * @property string $bet_val
 * @property string $ret_sync_data
 * @property string $ret_async_data
 * @property string $create_time
 * @property string $modify_time
 */
class ZmfOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zmf_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_code', 'version', 'command', 'messageId', 'bet_val'], 'required'],
            [['bet_val', 'ret_sync_data', 'ret_async_data'], 'string'],
            [['create_time', 'modify_time'], 'safe'],
            [['order_code', 'messageId'], 'string', 'max' => 45],
            [['version', 'command'], 'string', 'max' => 15],
            //[['status'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'zmf_order_id' => '自增id',
            'order_code' => '咕啦订单编号',
            'version' => '版本号',
            'command' => '命令码',
            'messageId' => '消息流水号',
            'status' => '返回状态：0提交成功、1回调成功、2自动查询成功',
            'bet_val' => '投注内容',
            'ret_sync_data' => '同步返回消息',
            'ret_async_data' => '异步返回消息',
            'create_time' => '创建时间',
            'modify_time' => '回调修改时间',
        ];
    }
}
