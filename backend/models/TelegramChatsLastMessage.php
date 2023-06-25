<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Partner".
 *
 * @property string $id
 * @property string $chat_id
 * @property string $last_message
 */
class TelegramChatsLastMessage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TelegramChatsLastMessage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'last_message'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'last_message' => 'Last Message',
        ];
    }

    static function getLastMessage($chat_id) {
        return TelegramChatsLastMessage::find()->where(['chat_id' => $chat_id])->one();
    }

    static function createLastMessage($chat_id, $message) {
        $mes = new TelegramChatsLastMessage();
        $mes->chat_id = $chat_id;
        $mes->last_message = $message;

        return $mes->save();
    }

    static function updateLastMessage($chat_id, $message) {
        $mes = TelegramChatsLastMessage::find()->where(['chat_id' => $chat_id])->one();
        $mes->last_message = $message;

        return $mes->save();
    }
}
