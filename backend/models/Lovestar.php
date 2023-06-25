<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Lovestar".
 *
 * @property string $id
 * @property string $issuingAction
 * @property string $currentOwner
 * @property string $birthTimestamp
 */
class Lovestar extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Lovestar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['issuingAction', 'currentOwner', 'birthTimestamp'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'issuingAction' => 'Issuing action',
            'currentOwner' => 'Current owner',
            'birthTimestamp' => 'Date of birth',
        ];
    }
	
	static function filterLovestar($params = []){
		$query = Lovestar::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}
		
		return $query;
	}
	
	static function createLovestars($issing_action, $user_id, $count_of_lovestars ) {
		for ($i = 1; $i <= $count_of_lovestars; $i++) {
			$lovestar = new Lovestar();
			$lovestar->issuingAction = $issing_action;
			$lovestar->currentOwner = $user_id;
			$lovestar->birthTimestamp = time();
			$lovestar->save();
		}
		
		User::addedLovestarsCount($user_id, $count_of_lovestars);
		return true;
	}
}
