<?php

use yii\db\Migration;

/**
 * Class m171113_185005_add_data_to_settings_table
 */
class m180504_185005_create_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%config}}', [
            'id' => $this->primaryKey(),
            'group' => $this->string()->defaultValue('default'),
            'key' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'type' => "ENUM('string','boolean','number','file','password')",
            'position' => $this->integer()->defaultValue(100),
            'value' => $this->string(),
            'readonly' => $this->boolean()
        ], $tableOptions);
        $sql = "ALTER TABLE config ALTER type SET DEFAULT 'string'";
        $this->execute($sql);

        $this->batchInsert('{{%config}}', ['group', 'key', 'name', 'type', 'value'], [
            ['SMTP', 'use_smtp', 'Использовать SMTP', 'boolean', '0'],
            ['SMTP', 'smtp_server', 'Сервер SMTP', 'string', ''],
            ['SMTP', 'smtp_login', 'Логин SMTP', 'string', ''],
            ['SMTP', 'smtp_pass', 'Пароль SMTP', 'password', ''],
            ['SMTP', 'smtp_secure', 'Использовать SSL', 'boolean', '0'],
            ['SMTP', 'smtp_port', 'Порт SSL (по-умолчанию 465)', 'number', ''],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%config}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_185005_add_data_to_settings_table cannot be reverted.\n";

        return false;
    }
    */
}
