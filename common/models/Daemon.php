<?php

namespace common\models;

use app\models\ChatGPT;
use backend\models\UserConnections;
use backend\models\UsersWithSharedInterests;
use yii\db\Expression;

class Daemon {

    public static function sendEcosystemGrowthNotification() {
        $all_users = User::find()->all();
        $count_users = count($all_users);

        $new_day_users = array_filter($all_users, function ($a){
            return (time() - $a->created_at) <= 86400 && $a->invitation_code_id !== 0;
        });
        $new_day_users_count = count($new_day_users);

//        $test_notification_user = User::find()->where(['in', 'telegram', ['476111864', '534621965']])->all();
        foreach ($all_users as $user) {
//        foreach ($test_notification_user as $user) {
            // Secondary users calculated
            $secondary_users = UserConnections::getUserSecondaryUser($user->id);
            $secondary_users_count = count($secondary_users);
            $new_day_secondary_users = array_filter($secondary_users, function ($a){
                return (time() - $a['created_at_timestamp']) <= 86400;
            });
            $new_day_secondary_users_count = count($new_day_secondary_users);

            // users with shared interests calculated
            $user_interests = UsersWithSharedInterests::getUserWithSharedInterests($user->id);
            $user_interests_count = count($user_interests);
            $new_day_user_interests = array_filter($user_interests, function ($a){
                return (time() - $a['created_at_timestamp']) <= 86400;
            });
            $new_day_user_interests_count = count($new_day_user_interests);

            if(empty($user->telegram)) continue;
            $message = Translations::s('ecosystemGrowthNotification', $user->language ?? 'en');
            $message = str_replace('{newDayUsersCount}', $new_day_users_count, $message);
            $message = str_replace('{countUsers}', $count_users, $message);

            $message = str_replace('{newSecondaryUsers}', $new_day_secondary_users_count, $message);
            $message = str_replace('{secondaryUsers}', $secondary_users_count, $message);

            $message = str_replace('{newSecondaryUsersWithSharedInterests}', $new_day_user_interests_count, $message);
            $message = str_replace('{secondaryUsersWithSharedInterests}', $user_interests_count, $message);
            TelegramApi::sendNotificationToUserTelegram(urlencode($message), $user);
        }
    }

    public static function matchUsersByInterest() {
//        $users = User::find()->all();
        $users = User::find()->where(['in', 'telegram', ['476111864', '534621965']])->all();
        foreach ($users as $user) {
            $current_user_calculated_interests = unserialize($user->calculated_interests);

            if(empty($current_user_calculated_interests)) {
                continue;
            }

            $secondary_users = UserConnections::getUserSecondaryUser($user->id);

            foreach ($secondary_users as $secondary_user){
                $secondary_user = User::findOne($secondary_user['user_id']);

                if(empty($secondary_user)) {
                    continue;
                }

                $calculated_interests = unserialize($secondary_user->calculated_interests);

                if(empty($calculated_interests)) {
                    continue;
                }

//                echo "<pre>";
//                var_dump($current_user_calculated_interests, $calculated_interests);
//                echo "</pre>";
//                exit;
                $res = ChatGPT::compareInterests($current_user_calculated_interests['en'], $calculated_interests['en']);

                echo nl2br($res);
                exit;
            }
        }

        exit;
    }
}