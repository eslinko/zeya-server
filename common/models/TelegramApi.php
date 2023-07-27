<?php

namespace common\models;

class TelegramApi {
    static function validateAction($getParams) {
        if(empty($getParams)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $getParams['telegram_id']])->one();

        if(empty($user)) return ['status' => 'error'];

        return $user;
    }

    static function sendNotificationToUsersTelegram($notification_text, $users) {
        foreach ($users as $user) {
            $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage?chat_id={$user->telegram}&text={$notification_text}";
            CurlHelper::curl($url);
        }
        return true;
    }

    static function sendNotificationToUserTelegram($notification_text, $user) {
        $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage?chat_id={$user->telegram}&text={$notification_text}";
        CurlHelper::curl($url);
        return true;
    }
}