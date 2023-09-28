<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

// google geocoding api key
defined('GoogleMapApiKey') or define('GoogleMapApiKey', env('GOOGLE_MAP_API_KEY'));
// google vision json file name
defined('GoogleVisionFile') or define('GoogleVisionFile', env('GOOGLE_VISION_FILE'));
// telegram bot id
defined('TelegramBotId') or define('TelegramBotId', env('TELEGRAM_BOT_ID'));
// chat gpt api key
defined('ChatGPTApiKey') or define('ChatGPTApiKey', env('CHAT_GPT_API_KEY'));
// sendgrid api key
defined('SendGridApiKey') or define('SendGridApiKey', env('SENDGRID_API_KEY'));
// VH password
defined('ViralHelpPartnerPassword') or define('ViralHelpPartnerPassword', env('VH_PARTNER_PWD'));


$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();
