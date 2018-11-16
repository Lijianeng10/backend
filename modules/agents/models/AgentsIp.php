<?php

namespace app\modules\agents\models;

use Yii;
class AgentsIp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agents_ip';
    }
     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agents_id','ip_address'], 'required'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'agents_ip_id' => 'Agents Ip Id',
            'agents_id' => 'Agents Id',
            'ip_address' => 'Ip Address',
            'status' => 'Status',
        ];
    }
}

