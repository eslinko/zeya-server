<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "LovestarEmissions".
 *
 * @property int $id
 * @property int $lovestar_id
 * @property int $creative_expression_id
 * @property int $voter_id
 * @property int $vote_timestamp
 */
class LovestarEmissions extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'LovestarEmissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['lovestar_id', 'creative_expression_id', 'voter_id'], 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lovestar_id' => 'Lovestar id',
            'creative_expression_id' => 'Creative expression id',
            'voter_id' =>  'Voter id',
            'vote_timestamp' => 'Vote timestamp',
        ];
    }


    static function VotedAlready($user_id, $creative_expression_id) {
        $votedAlready = LovestarEmissions::find()->where(['voter_id' => $user_id, 'creative_expression_id' => $creative_expression_id])->one();
        if ($votedAlready === NULL) {
            return false;
        } else {
            return true;
        }
    }
/*	static function createLovestars($issing_action, $user_id, $count_of_lovestars ) {
		for ($i = 1; $i <= $count_of_lovestars; $i++) {
			$lovestar = new LovestarEmissions();
			$lovestar->issuingAction = $issing_action;
			$lovestar->currentOwner = $user_id;
			$lovestar->birthTimestamp = time();
			$lovestar->save();
		}
		
		User::addedLovestarsCount($user_id, $count_of_lovestars);
		return true;
	}*/
}
