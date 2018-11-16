<?php

namespace app\modules\expert\models;

use Yii;

/**
 * This is the model class for table "expert".
 *
 * @property integer $expert_id
 * @property integer $user_id
 * @property string $cust_no
 * @property string $introduction
 * @property integer $article_nums
 * @property integer $fans_nums
 * @property integer $read_nums
 * @property integer $lottery
 * @property integer $even_red_nums
 * @property integer $identity
 * @property integer $month_red_nums
 * @property integer $day_nums
 * @property integer $day_red_nums
 * @property integer $expert_status
 * @property integer $pact_status
 * @property integer $expert_type
 * @property string $expert_type_name
 * @property string $remark
 * @property integer $opt_id
 * @property string $review_time
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class Expert extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'cust_no'], 'required'],
            [['user_id', 'article_nums', 'fans_nums', 'read_nums', 'lottery', 'even_red_nums', 'identity', 'month_red_nums', 'day_nums', 'day_red_nums', 'expert_status', 'pact_status', 'expert_type', 'opt_id'], 'integer'],
            [['review_time', 'create_time', 'modify_time', 'update_time'], 'safe'],
            [['cust_no'], 'string', 'max' => 15],
            [['introduction'], 'string', 'max' => 1000],
            [['expert_type_name'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'expert_id' => 'Expert ID',
            'user_id' => 'User ID',
            'cust_no' => 'Cust No',
            'introduction' => 'Introduction',
            'article_nums' => 'Article Nums',
            'fans_nums' => 'Fans Nums',
            'read_nums' => 'Read Nums',
            'lottery' => 'Lottery',
            'even_red_nums' => 'Even Red Nums',
            'identity' => 'Identity',
            'month_red_nums' => 'Month Red Nums',
            'day_nums' => 'Day Nums',
            'day_red_nums' => 'Day Red Nums',
            'expert_status' => 'Expert Status',
            'pact_status' => 'Pact Status',
            'expert_type' => 'Expert Type',
            'expert_type_name' => 'Expert Type Name',
            'remark' => 'Remark',
            'opt_id' => 'Opt ID',
            'review_time' => 'Review Time',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
