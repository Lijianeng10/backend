<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "gift_category".
 *
 * @property integer $gift_category_id
 * @property string $category_name
 * @property string $category_remark
 * @property integer $parent_id
 * @property integer $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class GiftCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gift_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_name'], 'required'],
            [['parent_id', 'opt_id'], 'integer'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['category_name'], 'string', 'max' => 50],
            [['category_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gift_category_id' => 'Gift Category ID',
            'category_name' => 'Category Name',
            'category_remark' => 'Category Remark',
            'parent_id' => 'Parent ID',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
