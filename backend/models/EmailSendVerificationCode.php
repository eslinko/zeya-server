<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class EmailSendVerificationCode extends Model
{
    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($user, $email = '')
    {
        if(empty($email)) $email = $user->email;
        return Yii::$app->mailer->compose(
            ['html' => 'sendVerificationCodeToUser-html', 'text' => 'sendVerificationCodeToUser-text'],
            [
                'user' => $user,
            ])
            ->setTo($email)
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['siteName']])
            ->setSubject('Verification From LovestarBot')
            ->send();
    }
}
