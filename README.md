Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mix8872/config
```

or add

```json
"mix8872/yii2-config": "dev-master"
```

to the `require` section of your `composer.json`.

Usage
-----

Edit `components` section of your application config file.

Controller map: 

```php
	'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['admin'], //глобальный доступ к фаил менеджеру @ - для авторизорованных , ? - для гостей , чтоб открыть всем ['@', '?']
            'disabledCommands' => ['netmount'], //отключение ненужных команд https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#commands
            'roots' => [
                [
                    'baseUrl'=>'@web',
                    'basePath'=>'@webroot',
                    'path' => 'uploads',
                    'name' => 'Uploads',
                    'options' => [
                        'uploadOverwrite' => false,
                        'uploadAllow' => ['*'],
//                        'uploadDeny' => ['pdf'],
                        'uploadOrder' => ['allow', 'deny'],
                        'uploadMaxSize' => '50M',
                        'disabled' => ['mkfile'],
                    ],
                ]
            ]
        ],
		
		...
    ],
```

Common:

```php
'components' => [
	'config' => [
		'class' => 'mix8872\config\components\Config'
	],
	
	...
]
```

Edit `modules` section of your application config file.

Backend:

```php
'modules' => [
	'config' => [
		'class' => 'mix8872\config\Module',
		'as access' => [
			'class' => 'yii\filters\AccessControl',
			'rules' => [
				[
					'allow' => true,
					'roles' => ['admin']
				],
			]
		]
	],
	'gridview' =>  [
		'class' => '\kartik\grid\Module'
		// enter optional module parameters below - only if you need to
		// use your own export download action or custom translation
		// message source
		// 'downloadAction' => 'gridview/export/download',
		// 'i18n' => []
	],
	
	...
]
```

Next run migration: yii migrate --migrationPath=@vendor/mix8872/yii2-config/src/migrations


Now you can open url \config\ and manage you params

** To get param value: **
 * Yii::$app->config-><param_key>
 * or Yii::$app->config->g('param_key')

** To set param value: **
 * Yii::$app->config-><param_key> = <param_value> 
 * or Yii::$app->config->s(<param_key>, <param_value>) 
 * or Yii::$app->config->s('<param_key>,<param_value>') 
 * or Yii::$app->config->s([<param_key>,<param_value>]) 
 * or Yii::$app->config->s([<param_key> => <param_value>])