<?php

namespace mix8872\config\models;

use mix8872\config\components\Event;
use mix8872\config\Module;
use Yii;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property string $group
 * @property string $key
 * @property string $name
 * @property string $type
 * @property int $position
 * @property string $value
 */
class Config extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NUMBER = 'number';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE = 'file';
    const TYPE_DATE = 'date';

    public static $types = [
        self::TYPE_STRING => 'Строка',
        self::TYPE_BOOLEAN => 'Да/нет',
        self::TYPE_NUMBER => 'Число',
        self::TYPE_PASSWORD => 'Пароль',
        self::TYPE_FILE => 'Файл',
        self::TYPE_DATE => 'Дата',
    ];

    protected $module;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config';
    }

    public function init()
    {
        $this->module = Yii::$app->controller->module;
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['type'], 'string'],
            [['position', 'tabId'], 'integer'],
            [['readonly', 'protected'], 'boolean'],
            [['group', 'key', 'name', 'value', 'canChange', 'canEdit'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => Yii::t('config', 'Группа'),
            'key' => Yii::t('config', 'Ключ'),
            'name' => Yii::t('config', 'Название'),
            'type' => Yii::t('config', 'Тип'),
            'position' => Yii::t('config', 'Позиция'),
            'value' => Yii::t('config', 'Значение'),
            'readonly' => Yii::t('config', 'Только чтение'),
            'protected' => Yii::t('config', 'Защищенное'),
            'tabId' => Yii::t('config', 'Вкладка'),
            'canChange' => Yii::t('config', 'Право изменения'),
            'canEdit' => Yii::t('config', 'Право редактирования'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->module instanceof Module) {
            $event = new Event(['model' => &$this]);
            $this->module->trigger(Module::EVENT_BEFORE_SAVE, $event);
        }
        return parent::beforeSave($insert);
    }
}
