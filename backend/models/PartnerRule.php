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
	
	static function lovestarsCalculatingByRule($rule_id, $base_value = 0){
		$rule = PartnerRule::findOne($rule_id);
		if(empty($rule)) return ['status' => false, 'message' => 'Rule by ID not found.'];

        // rule for viral help
        if((int) $rule_id === 4) {
            $baseValueFromRule = $rule->emissionCalculationBaseValue;
            $percentage = $rule->emissionCalculationPercentage;

            if ($baseValueFromRule == 0) {
                $emittedLovestars = (int) $base_value;
            } elseif ($percentage > 1) {
                $emittedLovestars = ceil((int) $base_value + ((int) $base_value * ($percentage / 100)));
            } else {
                $emittedLovestars = $baseValueFromRule;
            }

            return $emittedLovestars;
        }

		return ceil($rule->emissionCalculationBaseValue * $rule->emissionCalculationPercentage);
	}
    static public function createRegistrationLovestarRule(){
        $new_rule = PartnerRule::find()->where(['id' => 1])->one();
        if($new_rule===NULL) {
            $new_rule = new PartnerRule();
            $new_rule->id = 1;
        }

        $new_rule->partnerId = 1;
        $new_rule->title = 'Registration 1 Lovestar';
        $new_rule->triggerName = 'Registration creates 1 lovestar';
        $new_rule->emissionCalculationBaseValue = 1;
        $new_rule->emissionCalculationPercentage = 1;
        if($new_rule->save(false)){
            return $new_rule;
        }
        else{
            return NULL;
        }
    }
    static public function createRegistrationGivesCodeOwnerLovestarRule(){
        $new_rule = PartnerRule::find()->where(['id' => 2])->one();
        if($new_rule===NULL) {
            $new_rule = new PartnerRule();
            $new_rule->id = 2;
        }

        $new_rule->partnerId = 1;
        $new_rule->title = 'Registration gives code owner 1 Lovestar';
        $new_rule->triggerName = 'Registration gives code owner  1 lovestar';
        $new_rule->emissionCalculationBaseValue = 1;
        $new_rule->emissionCalculationPercentage = 1;
        if($new_rule->save(false)){
            return $new_rule;
        }
        else{
            return NULL;
        }
    }
    static function createRuleRegistrationGivesLovestarToCodeOwnerConnections(){
        $new_rule = PartnerRule::find()->where(['id' => 3])->one();
        if($new_rule===NULL) {
            $new_rule = new PartnerRule();
            $new_rule->id = 3;
        }

        $new_rule->partnerId = 1;
        $new_rule->title = 'Registration gives 1 Lovestar to code owner conections';
        $new_rule->triggerName = 'Registration gives 1 lovestar to code owner connections';
        $new_rule->emissionCalculationBaseValue = 1;
        $new_rule->emissionCalculationPercentage = 1;
        if($new_rule->save(false)){
            return $new_rule;
        }
        else{
            return NULL;
        }
    }

    static function createRuleRegistrationForViralHelp(){
        $new_rule = PartnerRule::find()->where(['id' => 4])->one();
        if($new_rule===NULL) {
            $new_rule = new PartnerRule();
            $new_rule->id = 4;
        }

        $new_rule->partnerId = 2;
        $new_rule->title = 'ViralHelp4Zeya4Eve';
        $new_rule->triggerName = 'DirectMapping';
        $new_rule->emissionCalculationBaseValue = 0;
        $new_rule->emissionCalculationPercentage = 0;
        if($new_rule->save(false)){
            return $new_rule;
        }
        else{
            return NULL;
        }
    }
}
