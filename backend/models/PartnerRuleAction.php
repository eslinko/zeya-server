<?php

namespace app\models;

use backend\models\UserConnections;
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
	static function actionRegistrationGivesCodeOwnerLovestar($code_owner) {
        //create BotPartner at first use
        $bot_partner = Partner::findOne(['id'=>1]);
        if($bot_partner === NULL) $bot_partner =  Partner::createBotPartner();
        if($bot_partner === NULL) return ['status' => false, 'message' => 'Error, cannot create BotPartner at first use'];

        //check do we have Registration by code rule
        $reg_rule = PartnerRule::findOne(['id'=>2]);
        if($reg_rule === NULL) $reg_rule =  PartnerRule::createRegistrationGivesCodeOwnerLovestarRule();
        if($reg_rule === NULL) return ['status' => false, 'message' => 'Error, cannot create rule at first use: Registration gives code owner 1 Lovestar'];

        return PartnerRuleAction::createAction(2, $code_owner);

    }

    static function actionRegistrationLovestar($user_id) {
        //create BotPartner at first use
        $bot_partner = Partner::findOne(['id'=>1]);
        if($bot_partner === NULL) $bot_partner =  Partner::createBotPartner();
        if($bot_partner === NULL) return ['status' => false, 'message' => 'Error, cannot create BotPartner at first use'];

        //check do we have Registration rule
        $reg_rule = PartnerRule::findOne(['id'=>1]);
        if($reg_rule === NULL) $reg_rule =  PartnerRule::createRegistrationLovestarRule();
        if($reg_rule === NULL) return ['status' => 'error','message' => 'Error, cannot create rule at first use: Registration rule'];

        return PartnerRuleAction::createAction(1, $user_id);
    }
	static function createAction($rule_id, $emittedLovestarsUser, $base_value = 0) {
/*        if($rule_id === 1 OR $rule_id === 2) {//built-in rule, create at first use
            //create BotPartner at first use
            $bot_partner = Partner::findOne(['id'=>1]);
            if($bot_partner === NULL) $bot_partner =  Partner::createBotPartner();
            if($bot_partner === NULL) return ['status' => false, 'message' => 'Error, cannot create BotPartner at first use'];

            //check do we have Registration rule
            $reg_rule = PartnerRule::findOne(['id'=>1]);
            if($reg_rule === NULL) $reg_rule =  PartnerRule::createRegistrationLovestarRule();
            if($reg_rule === NULL) return ['status' => 'error','message' => 'Error, cannot create rule at first use: Registration rule'];

            //check do we have Registration by code rule
            $reg_rule = PartnerRule::findOne(['id'=>2]);
            if($reg_rule === NULL) $reg_rule =  PartnerRule::createRegistrationGivesCodeOwnerLovestarRule();
            if($reg_rule === NULL) return ['status' => false, 'message' => 'Error, cannot create rule at first use: Registration gives code owner 1 Lovestar'];
        }*/

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
		
		$new_action->emittedLovestars = PartnerRule::lovestarsCalculatingByRule($rule_id, $base_value);
//        file_put_contents('log.txt',"new_action->emittedLovestars:".$new_action->emittedLovestars."\n",FILE_APPEND);
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
    static function actionRegistrationGivesLovestarToCodeOwnerConnections($code_owner){
        //create BotPartner at first use
        $bot_partner = Partner::findOne(['id'=>1]);
        if($bot_partner === NULL) $bot_partner =  Partner::createBotPartner();
        if($bot_partner === NULL) return ['status' => 'error', 'message' => 'Error, cannot create BotPartner at first use'];

        //check do we have Registration rule
        $reg_rule = PartnerRule::findOne(['id'=>3]);
        if($reg_rule === NULL) $reg_rule =  PartnerRule::createRuleRegistrationGivesLovestarToCodeOwnerConnections();
        if($reg_rule === NULL) return ['status' => 'error','message' => 'Error, cannot create rule at first use: Registration rule'];

        $connections = UserConnections::getUserConnections($code_owner);
        $return_connections=[];
        foreach ($connections as $con){
            $resp = PartnerRuleAction::createAction(3, $con['user_id']);
            if($resp['status'] === true)$return_connections[] = User::findOne([$con['user_id']]);
        }
        return $return_connections;
    }
}
