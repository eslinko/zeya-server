<?php

namespace app\models;

use SendGrid\Mail\Mail;
use SendGrid;
use Yii;

class SendGridMailer {
    protected $sendGrid;

    public function __construct() {
        $this->sendGrid = new SendGrid(SendGridApiKey);
    }

    public function sendEmail($to, $subject, $content) {
        echo "<pre>";
        var_dump($to);
        echo "</pre>";
        exit;
        $email = new Mail();
        $email->setFrom(Yii::$app->params['supportEmail'], Yii::$app->params['siteName']);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/plain", $content);
//        $email->addContent(
//            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
//        );


        echo "<pre>";
        var_dump($this->sendGrid->send($email));
        echo "</pre>";
        exit;
        try {
            $response = $this->sendGrid->send($email);
            echo "<pre>";
            var_dump($response->statusCode());
            echo "</pre>";
            echo "<pre>";
            var_dump('-------------------');
            var_dump($response->headers());
            echo "</pre>";
            echo "<pre>";
            var_dump('-------------------');
            var_dump($response->body());
            echo "</pre>";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
        exit;
        return $response;
    }
}