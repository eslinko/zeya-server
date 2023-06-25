<?php

namespace app\models;

use app\models\CurlHelper;
use common\models\User;

class TelegramApi {
    static function validateAction($getParams) {
        if(empty($getParams)) return ['status' => 'error'];

        $user = User::find()->where(['telegram' => $getParams['telegram_id']])->one();

        if(empty($user)) return ['status' => 'error'];

        return $user;
    }

    static function sendNotificationToAdminTelegram($notification_text) {
        $admins = User::find()->where(['role' => 'admin'])->all();
        foreach ($admins as $admin) {
            $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage?chat_id={$admin->telegram}&text={$notification_text}";
            CurlHelper::curl($url);
        }
        return true;
    }
}