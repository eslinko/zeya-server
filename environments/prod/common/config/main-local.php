<?php
// telegram bot id
defined('TelegramBotId') or define('TelegramBotId', env('TELEGRAM_BOT_ID'));
// chat gpt api key
defined('ChatGPTApiKey') or define('ChatGPTApiKey', env('CHAT_GPT_API_KEY'));

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASS'),
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
