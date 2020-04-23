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

Configuration
-----

Edit `components` section of your application config file.

Common:

```php
'components' => [
    'config' => [
        'class' => 'mix8872\config\components\Config'
    ],

// other components
]
```

Edit `controllerMap` and `modules` section of your application config file.

Backend:

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
                    // 'uploadDeny' => ['pdf'],
                    'uploadOrder' => ['allow', 'deny'],
                    'uploadMaxSize' => '50M',
                    'disabled' => ['mkfile'],
                ],
            ]
        ]
    ],
    
    // other controllers
],
],
'modules' => [
    'config' => [
        'class' => 'mix8872\config\Module',
        'adminRole' => 'admin', // optional, defines all rights on options editing for role
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
    
    // other modules
]
```
If declared `adminRole` then other user which haven't declared permissions can't create 

Next run migration: 
```
yii migrate --migrationPath=@vendor/mix8872/yii2-config/src/migrations
```
Usage
-----
Now you can open url \config\ and manage you params

**To get param value:**
 * Yii::$app->config-><param_key>
 * or Yii::$app->config->g('param_key')

**To set param value from code:**
 * Yii::$app->config-><param_key> = <param_value> 
 * or Yii::$app->config->s(<param_key>, <param_value>) 
 * or Yii::$app->config->s('<param_key>,<param_value>') 
 * or Yii::$app->config->s([<param_key>,<param_value>]) 
 * or Yii::$app->config->s([<param_key> => <param_value>])

Events
------

Config module has next events:
* EVENT_AFTER_CREATE - fires on config option was added
* EVENT_AFTER_UPDATE - fires on config option was updated
* EVENT_AFTER_DELETE - fires on config option was deleted
* EVENT_BEFORE_SAVE - fires on each config option was saved

You can catch this events in config this way:

```php
'modules' => [
    'config' => [
        'class' => 'mix8872\config\Module',
        'on ' . \mix8872\config\Module::EVENT_AFTER_CREATE => function ($e) {
            $model = $e->model;
            // do something
        },
        'on ' . \mix8872\config\Module::EVENT_AFTER_UPDATE => function ($e) {
            $model = $e->model;
            // do something
        },
        'on ' . \mix8872\config\Module::EVENT_AFTER_DELETE => function ($e) {
            $model = $e->model;
            // do something
        },
        'on ' . \mix8872\config\Module::EVENT_BEFORE_SAVE => function ($e) {
            $model = $e->model;
            // do something
        },
    ]
]
```

In `$e->model` the event passes an object (or array of objects) of config items.

In `EVENT_BEFORE_SAVE` event the model is passed by reference  
and can be modified from event handler.


Access rules
------------

You can define access rules for config options by define adminRole in config  
or set rules inside config options. Access matrix you can find [here](access_matrix.txt).
