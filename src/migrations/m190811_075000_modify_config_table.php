<?php

use yii\db\Migration;

/**
 * Class m171113_185005_add_data_to_settings_table
 */
class m190811_075000_modify_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->alterColumn('config', 'type', $this->string(50));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn('config', 'type', "ENUM('string','boolean','number','file','password')");
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
