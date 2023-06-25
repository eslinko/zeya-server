<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "PartnerRule".
 *
 * @property string $id
 * @property string $partnerId
 * @property string $title
 * @property string $triggerName
 * @property string $emissionCalculationBaseValue
 * @property string $emissionCalculationPercentage
 */
class PartnerRule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PartnerRule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          	[['partnerId', 'title', 'triggerName', 'emissionCalculationBaseValue', 'emissionCalculationPercentage'], 'required'],
			[['emissionCalculationBaseValue'], 'integer'],
			[['emissionCalculationPercentage'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partnerId' => 'Partner',
            'title' => 'Title',
            'triggerName' => 'Trigger Name',
            'emissionCalculationBaseValue' => 'Emission Calculation Base Value',
            'emissionCalculationPercentage' => 'Emission Calculation Percentage',
        ];
    }
	
	static function filterPartnerRule($params = []){
		$query = PartnerRule::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'title':
				case 'triggerName':
					$query = $query->andWhere(['like', $param, '%' . $value . '%', false]);
					break;
				default:
					$query = $query->andWhere([$param => $value]);
					break;
			}
		}
		
		return $query;
	}
	
	static function lovestarsCalculatingByRule($rule_id){
		$rule = PartnerRule::findOne($rule_id);
		if(empty($rule)) return ['status' => false, 'message' => 'Rule by ID not found.'];

		return ceil($rule->emissionCalculationBaseValue * $rule->emissionCalculationPercentage);
	}
}
