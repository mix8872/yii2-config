<?php

namespace mix8872\config\models;

use Yii;

/**
 * This is the model class for table "config_tab".
 *
 * @property int $id
 * @property string $title
 * @property int $order
 */
class ConfigTab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_tab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['order'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'order' => 'Порядок',
        ];
    }

    public function getConfigs()
    {
        return $this->hasMany(Config::class, ['tabId' => 'id'])->orderBy(['group' => 'ASC', 'position' => 'ASC']);
    }

    public function delete()
    {
        Config::updateAll(['tabId' => 1], ['tabId' => $this->id]);
        return parent::delete();
    }

    public static function sort($items)
    {
        $rules = '';
        foreach ($items as $item) {
            $rules .= "WHEN {$item['id']} THEN {$item['i']} ";
        }
        return Yii::$app->db->createCommand('UPDATE `' . self::tableName() . "` SET `order` = CASE `id` $rules ELSE `order` END")->execute();

    }
}
