<?php

use yii\db\Migration;

/**
 * Class m230708_215007_rpp278_migrate_konfigsystem_k
 */
class m230708_215007_rpp278_migrate_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_edit_cppt_coret bool NOT NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230708_215007_rpp278_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230708_215007_rpp278_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
