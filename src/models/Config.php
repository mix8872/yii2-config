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
            [['position'], 'integer'],
			[['readonly'], 'boolean'],
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
        ];
    }
}
