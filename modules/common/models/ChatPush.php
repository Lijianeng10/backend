<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "chat_push".
 *
 * @property integer $chat_push_id
 * @property string $title
 * @property string $content
 * @property string $img_url
 * @property string $jump_url
 * @property integer $type
 * @property integer $send_type
 * @property string $send_user
 * @property integer $status
 * @property string $push_time
 * @property string $create_time
 * @property string $update_time
 */
class ChatPush extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_push';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'send_user'], 'string'],
            [['type', 'send_type', 'status'], 'integer'],
            [['push_time', 'create_time', 'update_time'], 'safe'],
            [['title','opt_name'], 'string', 'max' => 255],
            [['img_url', 'jump_url'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'chat_push_id' => 'Chat Push ID',
            'title' => 'Title',
            'content' => 'Content',
            'img_url' => 'Img Url',
            'jump_url' => 'Jump Url',
            'type' => 'Type',
            'send_type' => 'Send Type',
            'send_user' => 'Send User',
            'status' => 'Status',
            'push_time' => 'Push Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'opt_name' => 'Opt Name',
        ];
    }
}
