<?php
namespace mix8872\config;

use yii\filters\AccessControl;
/**
 * Shop module.
 */
class Module extends \yii\base\Module
{
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