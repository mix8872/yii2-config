<?php
namespace mix8872\config;

use yii\filters\AccessControl;
/**
 * Shop module.
 */
class Module extends \yii\base\Module
{
    public const EVENT_BEFORE_SAVE = 'configBeforeSave';
    public const EVENT_AFTER_CREATE = 'configAfterCreate';
    public const EVENT_AFTER_UPDATE = 'configAfterUpdate';
    public const EVENT_AFTER_DELETE = 'configAfterDelete';

    public const ACTION_MANAGE = 'configManage';
    public const ACTION_CHANGE = 'configChange';
    public const ACTION_EDIT = 'configEdit';
    public const ACTION_DELETE = 'configDelete';

    public $adminRole = '';

	public function init()
    {
        parent::init();
        $this->controllerNamespace = 'mix8872\config\controllers';
        $this->setViewPath('@vendor/mix8872/yii2-config/src/views');
		$this->registerTranslations();
    }

    public function registerTranslations()
    {
         \Yii::$app->i18n->translations['config'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru-RU',
            'basePath' => '@vendor/mix8872/yii2-config/src/messages',
            ];

    }
}
