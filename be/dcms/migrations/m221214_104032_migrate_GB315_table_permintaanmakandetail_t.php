<?php

use yii\db\Migration;

/**
 * Class m221214_104032_migrate_GB315_table_permintaanmakandetail_t
 */
class m221214_104032_migrate_GB315_table_permintaanmakandetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE permintaanmakandetail_t ADD IF NOT EXISTS is_ditagihkan BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221214_104032_migrate_GB315_table_permintaanmakandetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221214_104032_migrate_GB315_table_permintaanmakandetail_t cannot be reverted.\n";

        return false;
    }
    */
}
