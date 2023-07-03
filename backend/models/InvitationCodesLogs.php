<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "InvitationCodesLogs".
 *
 * @property string $id
 * @property string $timestamp
 * @property string $user_id
 * @property string $inserted_code
 * @property string $error_type
 */
class InvitationCodesLogs extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'InvitationCodesLogs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['timestamp', 'user_id', 'inserted_code', 'error_type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timestamp' => 'Date',
            'user_id' => 'Telegram ID',
            'inserted_code' => 'Code',
            'error_type' => 'Error',
        ];
    }

    static function addToLog($user_id, $inserted_code, $error_type, $timestamp = '') {
        if(empty($timestamp)) $timestamp = time();
        $log = new InvitationCodesLogs();
        $log->timestamp = $timestamp;
        $log->user_id = $user_id;
        $log->error_type = $error_type;
        $log->inserted_code = $inserted_code;
        return $log->save(false);
    }
}
