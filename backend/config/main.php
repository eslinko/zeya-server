<?php
$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

return [
	'id' => 'app-backend',
	'name' => 'LCAPP',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'backend\controllers',
	'bootstrap' => ['log'],
	'language' => 'en-US',
	'modules' => [
		'yii2images' => [
			'class' => 'rico\yii2images\Module',
			//be sure, that permissions ok
			//if you cant avoid permission errors you have to create "images" folder in web root manually and set 777 permissions
			'imagesStorePath' => 'upload/all', //path to origin images
			'imagesCachePath' => 'upload/cache', //path to resized copies
			'graphicsLibrary' => 'GD', //but really its better to use 'Imagick'
			'placeHolderPath' => '@webroot/upload/all/no-image.png',
			// if you want to get placeholder when image not exists, string will be processed by Yii::getAlias
			'imageCompressionQuality' => 100, // Optional. Default value is 85.
		],
	],
	'components' => [
		'request' => [
			'csrfParam' => '_csrf-backend',
			'baseUrl' => '/admin'
		],
		'user' => [
			'identityClass' => 'common\models\User',
			//'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
		],
//        'session' => [
//            // this is the name of the session cookie used for login on the backend
//            'name' => 'advanced-backend',
//        ],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			//'suffix' => '.php',
			'rules' => [
				
				'hash-tag/view/<id:\d+>' => 'hash-tag/view',
				'hash-tag/update/<id:\d+>' => 'hash-tag/update',
				'hash-tag/<page:\d+>/' => 'hash-tag/index',
				'hash-tag/' => 'hash-tag/index',
				
				'teacher/view/<id:\d+>' => 'teacher/view',
				'teacher/update/<id:\d+>' => 'teacher/update',
				'teacher/<page:\d+>/' => 'teacher/index',
				'teacher/' => 'teacher/index',

                'events/view/<id:\d+>' => 'events/view',
                'events/update/<id:\d+>' => 'events/update',
                'events/<page:\d+>/' => 'events/index',
                'events/' => 'events/index',
				
				'teacher-outcome/view/<id:\d+>' => 'teacher-outcome/view',
				'teacher-outcome/update/<id:\d+>' => 'teacher-outcome/update',
				'teacher-outcome/<page:\d+>/' => 'teacher-outcome/index',
				'teacher-outcome/' => 'teacher-outcome/index',
				
				'settings/user/view/<id:\d+>' => 'user/view',
				'settings/user/update/<id:\d+>' => 'user/update',
				'settings/user/<page:\d+>/' => 'user/index',
				'settings/user/create' => 'user/create',
				'settings/user/' => 'user/index',

                'settings/languages/view/<id:\d+>' => 'languages/view',
                'settings/languages/update/<id:\d+>' => 'languages/update',
                'settings/languages/<page:\d+>/' => 'languages/index',
                'settings/languages/create' => 'languages/create',
                'settings/languages/' => 'languages/index',

				'partner/view/<id:\d+>' => 'partner/view',
				'partner/update/<id:\d+>' => 'partner/update',
				'partner/<page:\d+>/' => 'partner/index',
				'partner/' => 'partner/index',
				
				'partner-rule/view/<id:\d+>' => 'partner-rule/view',
				'partner-rule/update/<id:\d+>' => 'partner-rule/update',
				'partner-rule/<page:\d+>/' => 'partner-rule/index',
				'partner-rule/' => 'partner-rule/index',
				
				'partner-rule-action/view/<id:\d+>' => 'partner-rule-action/view',
				'partner-rule-action/<page:\d+>/' => 'partner-rule-action/index',
				'partner-rule-action/' => 'partner-rule-action/index',

				'lovestar/<page:\d+>/' => 'lovestar/index',
				'lovestar/' => 'lovestar/index',
				
				'teaching-transaction/view/<id:\d+>' => 'teaching-transaction/view',
				'teaching-transaction/update/<id:\d+>' => 'teaching-transaction/update',
				'teaching-transaction/<page:\d+>/' => 'teaching-transaction/index',
				'teaching-transaction/' => 'teaching-transaction/index',

                'settings/view/<id:\d+>' => 'settings/view',
                'settings/update/<id:\d+>' => 'settings/update',
                'settings/<page:\d+>/' => 'settings/index',
                'settings/' => 'settings/index',

                'invitation-codes/view/<id:\d+>' => 'invitation-codes/view',
//                'invitation-codes/update/<id:\d+>' => 'invitation-codes/update',
                'invitation-codes/<page:\d+>/' => 'invitation-codes/index',
                'invitation-codes/' => 'invitation-codes/index',

                'creative-types/view/<id:\d+>' => 'creative-types/view',
                'creative-types/update/<id:\d+>' => 'creative-types/update',
                'creative-types/<page:\d+>/' => 'creative-types/index',
                'creative-types/' => 'creative-types/index',

				'admin/no-access' => '/admin/site/no-access',
				'<action>' => 'site/<action>',

                'telegram-api/notifications/<ntId:\d+>/read' => 'telegram-api/notifications-read',
                'telegram-api/notifications/<ntId:\d+>' => 'telegram-api/notifications-delete',
                'telegram-api/notifications/unread-count' => 'telegram-api/notifications-unread-count',
                'telegram-api/notifications/read-all' => 'telegram-api/notifications-read-all',
                'telegram-api/notifications/<ntId:\d+>/details' => 'telegram-api/notifications-details',
                /*                [
                                    'pattern' => 'telegram-api/notifications/<messageId:\d+>/read',
                                    'route' => 'telegram-api/notifications'
                                ]*/
			],
		],
		'assetManager' => [
			'basePath' => '@webroot/assets',
			'baseUrl' => '@web/assets',
			'appendTimestamp' => true,
		],
	],
	'params' => $params,
];
