<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "league_team".
 *
 * @property integer $league_team_id
 * @property string $team_id
 * @property string $league_id
 */
class LeagueTeam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'league_team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'league_id'], 'required'],
            [['team_id', 'league_id'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'league_team_id' => 'League Team ID',
            'team_id' => 'Team ID',
            'league_id' => 'League ID',
        ];
    }
}
