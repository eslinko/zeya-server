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
          [['user_id'], 'required'],
          [['code'], 'unique'],
          [['registered_user_id', 'signup_date'], 'safe'],
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

    static function createNewCode($owner_id) {
        $code = new InvitationCodes();
        $code->user_id = $owner_id;
        $code->code = self::generateInvitationCode();
        $code->save();
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
                InvitationCodesLogs::addToLog($user_id, $string_code, 'There is no such code');
            }
            if(!empty($code->registered_user_id)) {
                InvitationCodesLogs::addToLog($user_id, $string_code, 'This code has already been redeemed');
            }
            return ['status' => 'error', 'text' => 'This code is not valid or has already been redeemed'];
        }

        $code->registered_user_id = $user_id;
        $code->signup_date = time();

        if(!$code->save()) {
            return ['status' => 'error', 'text' => 'Error! Try again later.'];
        }

        $user->invitation_code_id = $code->id;
        $user->save(false);

        for ($i = 1; $i <= 5; $i++) {
            self::createNewCode($user->id);
        }

        return ['status' => 'success'];
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
    static function getInvitationCodeOwnerUserId($code){
        $db_code = InvitationCodes::find()->where(['code' => $code])->one();
        if(empty($db_code)) return ['status' => 'error'];
        return $db_code->user_id;
    }
}
