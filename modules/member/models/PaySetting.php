<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "pay_setting".
 *
 * @property integer $pay_setting_id
 * @property string $ali_app_id
 * @property string $ali_merchant_private_key
 * @property string $ali_charset
 * @property string $ali_sign_type
 * @property string $ali_gatewayUrl
 * @property string $ali_alipay_public_key
 * @property integer $ali_switch
 * @property integer $wx_switch
 * @property string $wx_APPID
 * @property string $wx_MCHID
 * @property string $wx_KEY
 * @property string $wx_APPSECRET
 * @property integer $wxapp_switch
 * @property string $wxapp_APPID
 * @property string $wxapp_MCHID
 * @property string $wxapp_KEY
 */
class PaySetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ali_merchant_private_key', 'ali_alipay_public_key'], 'string'],
            [['ali_switch', 'wx_switch', 'wxapp_switch'], 'integer'],
            [['ali_app_id'], 'string', 'max' => 50],
            [['ali_charset', 'ali_sign_type'], 'string', 'max' => 20],
            [['ali_gatewayUrl'], 'string', 'max' => 120],
            [['wx_APPID', 'wx_MCHID', 'wx_KEY', 'wx_APPSECRET', 'wxapp_APPID', 'wxapp_MCHID', 'wxapp_KEY'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_setting_id' => 'Pay Setting ID',
            'ali_app_id' => 'Ali App ID',
            'ali_merchant_private_key' => 'Ali Merchant Private Key',
            'ali_charset' => 'Ali Charset',
            'ali_sign_type' => 'Ali Sign Type',
            'ali_gatewayUrl' => 'Ali Gateway Url',
            'ali_alipay_public_key' => 'Ali Alipay Public Key',
            'ali_switch' => 'Ali Switch',
            'wx_switch' => 'Wx Switch',
            'wx_APPID' => 'Wx  Appid',
            'wx_MCHID' => 'Wx  Mchid',
            'wx_KEY' => 'Wx  Key',
            'wx_APPSECRET' => 'Wx  Appsecret',
            'wxapp_switch' => 'Wxapp Switch',
            'wxapp_APPID' => 'Wxapp  Appid',
            'wxapp_MCHID' => 'Wxapp  Mchid',
            'wxapp_KEY' => 'Wxapp  Key',
        ];
    }
}
