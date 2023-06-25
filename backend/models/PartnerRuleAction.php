<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "PartnerRuleAction".
 *
 * @property string $id
 * @property string $ruleId
 * @property string $timestamp
 * @property string $ruleTitle
 * @property integer $emissionCalculationBaseValue
 * @property string $emissionCalculationPercentage
 * @property string $triggerName
 * @property integer $emittedLovestars
 * @property integer $emittedLovestarsUser
 * @property integer $approvalQRCode
 * @property boolean $approvalStatus
 */
class PartnerRuleAction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PartnerRuleAction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          	[['ruleId', 'timestamp', 'ruleTitle', 'emissionCalculationBaseValue', 'emissionCalculationPercentage', 'triggerName', 'emittedLovestars', 'emittedLovestarsUser'], 'required'],
			[['emissionCalculationBaseValue'], 'integer'],
			[['emissionCalculationPercentage'], 'number'],
			[['approvalQRCode', 'approvalStatus'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ruleId' => 'Rule',
            'timestamp' => 'Date of action',
			'ruleTitle' => 'Rule Title',
            'triggerName' => 'Trigger Name',
            'emissionCalculationBaseValue' => 'Emission Calculation Base Value',
            'emissionCalculationPercentage' => 'Emission Calculation Percentage',
            'emittedLovestars' => 'Emitted Lovestars',
            'emittedLovestarsUser' => 'Emitted Lovestars User',
            'approvalQRCode' => 'Approval QR Code',
            'approvalStatus' => 'Approval Status',
        ];
    }
	
	static function filterPartnerRuleAction($params = []){
		$query = PartnerRuleAction::find()->where(['not', ['id' => 0]]);
		$params = !empty($params) ? $params : Yii::$app->request->get();
		
		foreach ($params as $param => $value){
			if(empty($value) || $param === 'sort' || $param === 'page') continue;
			
			switch ($param) {
				case 'ruleTitle':
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
	
	static function createAction($rule_id, $emittedLovestarsUser) {
		$rule = PartnerRule::findOne($rule_id);
		$user = User::findOne($emittedLovestarsUser);
		if(empty($rule)) return ['status' => false, 'message' => 'Rule by ID not found.'];
		
		if(empty($user)) return ['status' => false, 'message' => 'User by ID not found.'];
		
		$new_action = new PartnerRuleAction();
		$new_action->ruleId = $rule->id;
		$new_action->timestamp = time();
		$new_action->ruleTitle = $rule->title;
		$new_action->triggerName = $rule->triggerName;
		$new_action->emissionCalculationBaseValue = $rule->emissionCalculationBaseValue;
		$new_action->emissionCalculationPercentage = $rule->emissionCalculationPercentage;
		$new_action->emittedLovestarsUser = $emittedLovestarsUser;
		
		$new_action->emittedLovestars = PartnerRule::lovestarsCalculatingByRule($rule_id);
		
		$status = true;
		$error = 'Action was successfully created.';
		
		if ( !$new_action->save() ) {
			$status = false;
			$errors = [];
			foreach ($new_action->getErrors() as $temp_error) {
				$errors[] = $temp_error[0];
			}
			$error = implode(' ', $errors);
			return ['status' => $status, 'message' => $error];
		}
		
		Lovestar::createLovestars($new_action->id, $emittedLovestarsUser, $new_action->emittedLovestars );
		
		return ['status' => $status, 'message' => $error, 'action_id' => $new_action->id];
	}
}
