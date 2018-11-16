<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "bananer".
 *
 * @property integer $bananer_id
 * @property string $pic_name
 * @property string $pic_url
 * @property string $pc_pic_url
 * @property integer $jump_type
 * @property string $jump_title
 * @property string $jump_url
 * @property string $content
 * @property integer $area
 * @property integer $type
 * @property integer $info_type
 * @property integer $use_type
 * @property integer $status
 * @property string $league_code
 * @property string $create_time
 * @property string $update_time
 * @property integer $opt_id
 */
class Bananer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bananer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jump_type', 'area', 'type', 'info_type', 'use_type', 'status', 'opt_id'], 'integer'],
            [['content'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['pic_name', 'jump_title'], 'string', 'max' => 50],
            [['pic_url', 'pc_pic_url', 'jump_url'], 'string', 'max' => 255],
            [['league_code'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bananer_id' => 'Bananer ID',
            'pic_name' => 'Pic Name',
            'pic_url' => 'Pic Url',
            'pc_pic_url' => 'Pc Pic Url',
            'jump_type' => 'Jump Type',
            'jump_title' => 'Jump Title',
            'jump_url' => 'Jump Url',
            'content' => 'Content',
            'area' => 'Area',
            'type' => 'Type',
            'info_type' => 'Info Type',
            'use_type' => 'Use Type',
            'status' => 'Status',
            'league_code' => 'League Code',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'opt_id' => 'Opt ID',
        ];
    }
} 
