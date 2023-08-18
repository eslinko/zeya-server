<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Daemon;

/**
 * Daemon controller
 */
class DaemonController extends Controller
{
    /*
     *  /usr/local/php81/bin/php /home/realmart/realmart.com.ua/skoryk/yii daemon/send-ecosystem-growth-notification
     * 0 8 * * * /usr/local/bin/cron "/usr/local/php81/bin/php /home/realmart/realmart.com.ua/skoryk/yii daemon/send-ecosystem-growth-notification" :::0
     * каждый день в 8 утра
     * */
    public function actionSendEcosystemGrowthNotification() {
        Daemon::sendEcosystemGrowthNotification();
    }

    /*
     *  /usr/local/php81/bin/php /home/realmart/realmart.com.ua/skoryk/yii daemon/match-users-by-interest
     * *\/5 * * * * /usr/local/bin/cron "/usr/local/php81/bin/php /home/realmart/realmart.com.ua/skoryk/yii daemon/match-users-by-interest" :::0
     * каждые 5 минут
     * */
    public function actionMatchUsersByInterest() {
        Daemon::matchUsersByInterest();
    }
}