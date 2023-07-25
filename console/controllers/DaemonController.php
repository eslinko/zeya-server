<?php

namespace console\controllers;

use common\models\TelegramApi;
use common\models\User;
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
}