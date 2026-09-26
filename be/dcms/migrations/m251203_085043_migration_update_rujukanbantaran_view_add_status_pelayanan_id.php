<?php

use yii\db\Migration;

/**
 * Class m251203_085043_migration_update_rujukanbantaran_view_add_status_pelayanan_id
 */
class m251203_085043_migration_update_rujukanbantaran_view_add_status_pelayanan_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS rujukanbantaran_v;");
        $rujukanbantaran_v = file_get_contents(__DIR__ . '/definitions/rujukanbantaran_v.sql');
        $this->execute($rujukanbantaran_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251203_085043_migration_update_rujukanbantaran_view_add_status_pelayanan_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251203_085043_migration_update_rujukanbantaran_view_add_status_pelayanan_id cannot be reverted.\n";

        return false;
    }
    */
}
