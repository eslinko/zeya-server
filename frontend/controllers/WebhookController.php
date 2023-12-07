<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Daemon;
use yii\web\Controller;

class WebhookController extends Controller
{
    /**
     *
     * Route for /webhook/send-ecosystem-growth-notification
     *
     * @return string
     */
    public function actionSendEcosystemGrowthNotification() {
        try {
            Daemon::sendEcosystemGrowthNotification();
        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK';
    }

    /**
     * Route for /webhook/match-users-by-interest
     *
     * @return string
     */
    public function actionMatchUsersByInterest() {
        try {
            Daemon::matchUsersByInterest();
        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK';
    }
    /**
     *
     * Route for /webhook/cron-at-8am-every-day
     *
     * @return string
     */
    public function actionCronAt8amEveryDay() {
        try {
            Daemon::unusedInvitationCodesReminder();
            Daemon::CE_expiration_reminder();
            //Daemon::sendEcosystemGrowthNotification();
        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK';
    }
}