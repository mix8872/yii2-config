<?php

use yii\db\Migration;

/**
 * Class m171113_185005_add_data_to_settings_table
 */
class m200422_192205_add_auth_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('config', 'canChange', $this->string(255));
        $this->addColumn('config', 'canEdit', $this->string(255));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('config', 'canChange');
        $this->dropColumn('config', 'canEdit');
    }
}
