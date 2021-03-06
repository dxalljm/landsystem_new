<?php
return [
    'components' => [
    		
    		
		'assetManager' => [
			'bundles' => [
				'yii\bootstrap\BootstrapPluginAsset' => [
					'js'=>[]
				],
			],
		],
//          'db' => [
//              'class' => 'yii\db\Connection',
//              'dsn' => 'mysql:host=120.26.208.136;port=3306;dbname=landsystem',
//              'username' => 'root',
//              'password' => '123456',
//              'charset' => 'utf8',
//  			'tablePrefix' => 'land_',
//          ],
   	'db' => [
   			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;port=33060;dbname=landsystem3',
			'username' => 'homestead',
			'password' => 'secret',
   			'charset' => 'utf8',
   			'tablePrefix' => 'land_',
   	],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
		'authManager' => [
 			'class' => 'yii\rbac\DbManager',
 			'itemTable' => '{{%auth_item}}',
			'assignmentTable' => '{{%auth_assignment}}',
 			'itemChildTable' => '{{%auth_item_child}}',
 		],
    ],
];
