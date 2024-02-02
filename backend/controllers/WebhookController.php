<?php

namespace backend\controllers;

use common\models\Daemon;
use Yii;


class WebhookController extends AppController
{
    /**
     * @inheritdoc
     */


    /**
     *
     * Route for admin/webhook/send-ecosystem-growth-notification
     *
     * @return string
     */
    public function actionSendEcosystemGrowthNotification() {
        try {
            Daemon::sendEcosystemGrowthNotification();
        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK ';
    }

    /**
     * Route for admin/webhook/match-users-by-interest
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
     * Route for admin/webhook/cron-every-15-minutes
     *
     * @return string
     */
    public function actionCronEvery15minutes() {
        try {
            //Daemon::unusedInvitationCodesReminder();
            Daemon::CE_expiration_reminder();

        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK';
    }
    /**
     *
     * Route for admin/webhook/cron-at-8am-every-day
     *
     * @return string
     */
    public function actionCronAt8amEveryDay() {
        try {
            Daemon::unusedInvitationCodesReminder();
            //Daemon::CE_expiration_reminder();

        } catch (\Throwable $t) {
            return sprintf('Error: (%d) %s. File: %s Line: %d', $t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine());
        }

        return 'OK';
    }

}
