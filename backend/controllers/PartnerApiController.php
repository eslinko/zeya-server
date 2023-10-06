<?php

namespace backend\controllers;

use app\models\InvitationCodes;
use app\models\Partner;
use app\models\PartnerRule;
use app\models\PartnerRuleAction;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class PartnerApiController extends AppController
{
    private $createRuleActionParams = [
        'authKey', 'partnerRuleId', 'triggerName', 'inputValue'
    ];
    public function actionCreateRuleAction()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();

        foreach ($this->createRuleActionParams as $required) {
            if(!in_array($required, array_keys($data))) {
                return ['status' => 'error', 'message' => 'You must provide ' . $required];
            }
        }

        $partnerRule = PartnerRule::findOne($data['partnerRuleId']);

        if(empty($partnerRule)) {
            return ['status' => 'error', 'message' => 'Rule not found'];
        }

        if(!Partner::partnerPasswordVerify($partnerRule->partnerId, $data['authKey'])) {
            return ['status' => 'error', 'message' => 'Unauthorized'];
        }

        $inputValue = round((float) $data['inputValue']);

        if($inputValue < 1) {
            return ['status' => 'error', 'message' => 'inputValue must be bigger then 0'];
        }

        $ruleAction = PartnerRuleAction::createAction($data['partnerRuleId'], 0, $inputValue, $data['triggerName']);

        if(!$ruleAction['status']) {
            return ['status' => 'error', 'message' => $ruleAction['message']];
        }

        $code = InvitationCodes::createNewCode(NULL, $ruleAction['action_id']);

        return ['status' => 'success', 'message' => 'Success!', 'ruleActionId' => $ruleAction['action_id'], 'emittedLovestars' => $ruleAction['emittedLovestars'], 'invitationCode' => $code];
    }
}
