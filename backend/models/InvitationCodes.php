<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "InvitationCodes".
 *
 * @property string $id
 * @property string $user_id
 * @property string $code
 * @property string $registered_user_id
 * @property string $ruleActionId
 * @property string $signup_date
 */
class InvitationCodes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'InvitationCodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['code'], 'unique'],
          [['registered_user_id', 'signup_date', 'ruleActionId', 'user_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Code owner',
            'registered_user_id' => 'Invited person',
            'code' => 'Code',
            'signup_date' => 'Signup date',
            'ruleActionId' => 'ruleActionId',
        ];
    }

    static function generateInvitationCode($length = 5) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        do {
            $code = '';
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    $code .= $characters[rand(0, strlen($characters) - 1)];
                }
                if ($i < 2) {
                    $code .= '-';
                }
            }
        } while (!self::isUniqueInvitationCode($code));

        return $code;
    }

   static function isUniqueInvitationCode($code) {
        return !InvitationCodes::findOne([
            'code' => $code,
        ]);
    }

    static function createNewCode($owner_id = NULL, $ruleActionId = NULL) {
        $code = new InvitationCodes();
        $code->user_id = $owner_id;
        $code->ruleActionId = $ruleActionId;
        $code->code = self::generateInvitationCode();
        $code->save();
        return $code->code;
    }

    static function generateCodes($owner_id, $amount) {
        for ($i = 1; $i <= $amount; $i++) {
            self::createNewCode($owner_id);
        }
        return ['status' => 'success'];
    }
    static function useCodeForInvitation($string_code, $user_id) {
        $code = InvitationCodes::findOne([
            'code' => $string_code,
        ]);
         $user = User::findOne([
            'id' => $user_id
        ]);

        if(empty($user)) return ['status' => 'error', 'text' => 'Error! Try again later.'];

        if(empty($code) || !empty($code->registered_user_id)) {
            if(empty($code)) {
                InvitationCodesLogs::addToLog($user_id, substr($string_code,0,250), 'There is no such code');
            }
            if(!empty($code->registered_user_id)) {
                InvitationCodesLogs::addToLog($user_id, substr($string_code,0,250), 'This code has already been redeemed');
            }
            return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];
        }

        $code->registered_user_id = $user_id;
        $code->signup_date = time();

        if(!$code->save()) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $result = ['register_with_lovestars' => false];

        if(!empty($code->ruleActionId)) {
            $rule_action = PartnerRuleAction::findOne($code->ruleActionId);
            if(!empty($rule_action)) {
                $rule_action->emittedLovestarsUser = $user_id;
                $rule_action->save();

                $lovestars = Lovestar::find()->where(['issuingAction' => $code->ruleActionId])->all();
                foreach ($lovestars as $lovestar) {
                    $lovestar->currentOwner = $user_id;
                    $lovestar->save();
                }

                User::addedLovestarsCount($user_id, (int) $rule_action->emittedLovestars);

                $result['register_with_lovestars'] = true;
                $result['current_lovestars'] = (int) $rule_action->emittedLovestars + 1;
            }
        }

        $user->invitation_code_id = $code->id;
        $user->save(false);

        for ($i = 1; $i <= 8; $i++) {
            self::createNewCode($user->id);
        }

        $result['status'] = 'success';
        return $result;
    }

    static function getUserInvitationCodes($user_id) {
        $codes = InvitationCodes::find()->where(['user_id' => $user_id])->all();
        $result = [];
        foreach ($codes as $code) {
            $result[] = [
                'code' => $code->code,
                'signup_date' => $code->signup_date,
                'user' => User::findOne(['id' => $code->registered_user_id])
            ];
        }

        return $result;
    }
    static function getUserNotUsedInvitationCodes($user_id) {
        return InvitationCodes::find()->where(['user_id' => $user_id, 'registered_user_id' => NULL])->all();
    }
    static function getInvitationCodeOwnerUserId($code){
        $db_code = InvitationCodes::find()->where(['code' => $code])->one();
        if(empty($db_code)) return ['status' => 'error'];
        return $db_code->user_id;
    }
    static function getInvitationCodeOwnerTelegramId($code){
        $db_code = InvitationCodes::find()->where(['code' => $code])->one();
        if(empty($db_code)) return ['status' => 'error'];
        return $db_code->telegram;
    }
}
