<?php

namespace backend\models;

use common\models\CurlHelper;
use common\models\Translations;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Notifications".
 *
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $related_entity_id
 * @property string $message_code
 * @property string $params
 * @property string $read_status
 * @property string $created_at
 */
class Notifications extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    const NOTIFICATION_TEXT = [
        'CONNECTION_REQUEST' => "🚀 %s wants to vibe with you!",
        'CONNECTION_ACCEPTED' => "🎉 %s vibed with your request!",
        'CONNECTION_REJECTED' => "🥺 %s didn't vibe with your request",
        'NEW_MATCH' => "🔥 Hot news! You and %s are a cosmic match!",
        'INVITE_CODE_USED' => "🌟 Get a Lovestar! %s joined Zeya4Eve space using your invite code!",
        'INVITE_CODE_USED_CONNECTIONS' => "🌟 Get a Lovestar! %s joined Zeya4Eve space via the invitation of your connection %s!",
        'INVITE_CODE_UNUSED_REMINDER' => "💌 Hey, spread the love! You have %d invite codes chillin",
        'CE_EXPIRATION_WARNING' => "⏳ Quick! Your creative vibe fades in %d hours. Keep the fire alive and share your new expression!"
    ];
    const CONNECTION_REQUEST = 'CONNECTION_REQUEST';
    const CONNECTION_ACCEPTED = 'CONNECTION_ACCEPTED';
    const CONNECTION_REJECTED = 'CONNECTION_REJECTED';
    const NEW_MATCH = 'NEW_MATCH';
    const INVITE_CODE_USED = 'INVITE_CODE_USED';
    const INVITE_CODE_USED_CONNECTIONS = 'INVITE_CODE_USED_CONNECTIONS';
    const INVITE_CODE_UNUSED_REMINDER = 'INVITE_CODE_UNUSED_REMINDER';
    const CE_EXPIRATION_WARNING = 'CE_EXPIRATION_WARNING';

    public static function tableName()
    {
        return 'Notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['user_id','type','related_entity_id','message_code','params','read_status'], 'required'],

          [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user_id',
            'type' => 'type',
            'related_entity_id' => 'related_entity_id',
            'message_code' => 'message_code',
            'params' => 'params',
            'read_status' => 'read_status',
            'created_at' => 'created_at',
        ];
    }

    static function setAsRead($id){
        if($id === NULL) return false;
        $not = Notifications::find()->where(['id' => intval($id)])->one();
        if($not === NULL) {
            return false;
        }
        $not->read_status = 1;
        if($not->save(false)){
            return true;
        }
        else{
            return false;
        }
    }

    static function deleteOne($id){
        $not = Notifications::find()->where(['id' => $id])->one();
        if($not === NULL) {
            return false;
        }
        $not->delete();
        return true;
    }
    static function unreadCount($id){
        $not = Notifications::find()->where(['user_id' => $id, 'read_status' => false])->all();
        return count($not);
    }
    static function setAllAsRead($id){
        $not = Notifications::find()->where(['user_id' => $id, 'read_status' => false])->all();
        $flag = true;
        foreach ($not as $nt){
            $nt->read_status = 1;
            if($nt->save(false) == false){
                $flag = false;
            }
        }
        return $flag;
    }
    static function getDetails($id){
        $not = Notifications::find()->where(['id' => $id])->one();
        return count($not);
    }
    static function createNotification($type, $user_from, $user_to, $additional_data=NULL)
    {
        switch ($type)
        {
            case self::CONNECTION_REQUEST:
                $buttons = [
                    ['text' => 'Accept', 'callback_data' => 'accept_connection__'.$user_from->id.'__'.$user_to->id],
                    ['text' => 'Decline', 'callback_data' => 'decline_connection__'.$user_from->id.'__'.$user_to->id]
                ];
                break;
            case self::CONNECTION_ACCEPTED:
                $buttons = [];
                break;
            case self::CONNECTION_REJECTED:
                $buttons = [];
                break;
            case self::NEW_MATCH:
                $buttons =[];/* [
                    ['text' => 'Show_match_info', 'callback_data' => 'show_match_info__'.$user_from->id.'__'.$user_to->id],
                    ];*/
                break;
            case self::INVITE_CODE_USED:
                $buttons = [];
                break;
            case self::INVITE_CODE_USED_CONNECTIONS:
                $buttons = [];
                break;
            case self::INVITE_CODE_UNUSED_REMINDER:
                $buttons = [
                    ['text' => "Btn_My invitation codes", 'callback_data' => 'my_lovestars'/*'my_invitation_codes'*/],
                ];
                break;
            case self::CE_EXPIRATION_WARNING:
                $buttons = [
                    ['text' => 'Update_ce', 'callback_data' => 'update_creative_expression__'.$additional_data.'__'.$user_to->id],
                ];
                break;
            default: return false;
        }
        $nt = new Notifications();
        $nt->user_id = $user_to->id;
        $nt->type = $type;
        if($user_from == NULL)
            $nt->related_entity_id = '';
        else
            $nt->related_entity_id = $user_from->id;
        $nt->message_code = '';
        $nt->params = json_encode($buttons);
        $nt->read_status = 0;
        $nt->created_at = time();
        if ($nt->save(false) == false) return false;
        $nt->refresh();


        switch ($type) {
            case self::CONNECTION_REQUEST:
                if ($user_to->notify_connections == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::CONNECTION_ACCEPTED:
                if ($user_to->notify_connections == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::CONNECTION_REJECTED:
                if ($user_to->notify_connections == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::NEW_MATCH:
                if ($user_to->notify_connections == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::INVITE_CODE_USED:
                if ($user_to->notify_invite_codes == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::INVITE_CODE_USED_CONNECTIONS:
                if ($user_to->notify_invite_codes == 1) {
                    if(empty($user_from->telegram_alias))
                        $name_from = $user_from->publicAlias;
                    else
                        $name_from = $user_from->publicAlias.' (@'.$user_from->telegram_alias.')';
                    if(empty($additional_data->telegram_alias))
                        $name_via = $additional_data->publicAlias;
                    else
                        $name_via = $additional_data->publicAlias.' (@'.$additional_data->telegram_alias.')';

                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $name_from, $name_via);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::INVITE_CODE_UNUSED_REMINDER:
                if ($user_to->notify_invite_codes == 1) {
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $additional_data);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
            case self::CE_EXPIRATION_WARNING:
                if ($user_to->notify_invite_codes == 1) {
                    $text = self::NOTIFICATION_TEXT[$type];
                    $text = Translations::s($text, $user_to->language);
                    $buttons = self::translateButtons($buttons, $user_to->language);
                    $text = sprintf($text, $additional_data);
                    Notifications::pushMessage($user_to, $text, $buttons, $nt->id);
                }
                break;
        }

    }
    static function translateButtons($buttons, $user_language) {
        foreach ($buttons as $k=>$butt){
            $buttons[$k]['text'] = Translations::s($butt['text'], $user_language);
        }
        return $buttons;
    }
    static function pushMessage($user, $text, $buttons, $not_id) {
        $data = ['chat_id' => $user->telegram, 'text' => $text];
        if(count($buttons) != 0)
        {
            foreach ($buttons as $k=>$butt){
                $buttons[$k]['callback_data'] .= '__'.$not_id;
            }
            $reply_markup = [];
            $reply_markup['inline_keyboard'] = [];
            $reply_markup['inline_keyboard'][0] = [];//first line of butons
            foreach ($buttons as $butt){
                $reply_markup['inline_keyboard'][0][] = $butt;
            }
            $data['reply_markup'] = json_encode($reply_markup);
        }

        $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage";
        CurlHelper::curl($url, $data, 'JSON');
    }

}
