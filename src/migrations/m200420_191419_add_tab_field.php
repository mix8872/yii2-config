<?php
namespace mix8872\config\migrations;

use yii\db\Migration;

/**
 * Class m171113_185005_add_data_to_settings_table
 */
class m200420_191419_add_tab_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->alterColumn('config', 'type', "ENUM('string','boolean','number','file','password','date')");
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%config_tab}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'order' => $this->integer(3)->defaultValue(500)
        ], $tableOptions);

        $this->batchInsert('config_tab', ['title'], [
            ['Main'], ['SMTP']
        ]);

        $this->addColumn('config', 'tabId', $this->integer(11)->defaultValue(1));
        $this->update('config', ['tabId' => 2], ['group' => 'SMTP']);
        $this->createIndex('idx-config-tabId', 'config', 'tabId');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn('config', 'type', "ENUM('string','boolean','number','file','password')");
        $this->dropIndex('idx-config-tabId', 'config');
        $this->dropColumn('config', 'tabId');
        $this->dropTable('{{%config_tab}}');
    }
}
