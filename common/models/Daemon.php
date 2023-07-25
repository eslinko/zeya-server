<?php

namespace common\models;

use yii\db\Expression;

class Daemon {
    public static function sendEcosystemGrowthNotification() {
//        $all_users = User::find()->where(['telegram' => '476111864'])->all();
        $all_users = User::find()->all();
        $count_users = count($all_users);

        $new_day_users = array_filter($all_users, function ($a){
            return (time() - $a->created_at) <= 86400 && $a->invitation_code_id !== 0;
        });
        $new_day_users_count = count($new_day_users);

        foreach ($all_users as $user) {
            if(empty($user->telegram)) continue;
            $message = Translations::s('ecosystemGrowthNotification', $user->language ?? 'en');
            $message = str_replace('{newDayUsersCount}', $new_day_users_count, $message);
            $message = str_replace('{countUsers}', $count_users, $message);
            TelegramApi::sendNotificationToUserTelegram(urlencode($message), $user);
        }
    }
}