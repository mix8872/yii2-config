<?php

namespace mix8872\config\models;

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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config';
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
            [['group', 'key', 'name', 'value'], 'string', 'max' => 255],
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
        ];
    }
}
