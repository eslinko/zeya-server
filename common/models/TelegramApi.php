<?php

namespace common\models;

class TelegramApi {
    static function validateAction($getParams) {
        if(empty($getParams)) return false;

        $user = User::find()->where(['telegram' => $getParams['telegram_id']])->one();

        if(empty($user)) return false;

        return $user;
    }

    static function validateWebAppRequest($url) {
        //get user_id
        parse_str($url, $url_arr);
        $user_arr = json_decode($url_arr['user'],true);
        $telegram_id = $user_arr['id'];

        //validation
        $initDataArray = explode('&', rawurldecode($url));
        $needle        = 'hash=';
        $hash          = '';

        foreach ($initDataArray as &$dataq) {
            if (substr($dataq, 0, \strlen($needle)) === $needle) {
                $hash = substr_replace($dataq, '', 0, \strlen($needle));
                $dataq = null;
            }
        }
        $initDataArray = array_filter($initDataArray);
        sort($initDataArray);
        $data_check_string = implode("\n", $initDataArray);
        $secret_key = hash_hmac('sha256', TelegramBotId,'WebAppData', true);
        $local_hash = bin2hex(hash_hmac('sha256', $data_check_string, $secret_key, true));
        if($local_hash !== $hash)
            return ['status' => false];

        $user = User::find()->where(['telegram' => $telegram_id])->one();

        if(empty($user)) return ['status' => false, 'message' => 'User not found'];

        return ['status' => true, 'user' => $user];
    }

    static function sendNotificationToUsersTelegram($notification_text, $users) {
        $notification_text_url = urlencode($notification_text);
        foreach ($users as $user) {
            if(empty($user->telegram)) continue;
            $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage?chat_id={$user->telegram}&text={$notification_text_url}";
//            echo $url . ' ';
            CurlHelper::curl($url);
        }
        return true;
    }

    static function sendNotificationToUserTelegram($notification_text, $user) {
        $url = "https://api.telegram.org/bot".TelegramBotId."/sendMessage?chat_id={$user->telegram}&text=".urlencode($notification_text);
        CurlHelper::curl($url);
        return true;
    }
}