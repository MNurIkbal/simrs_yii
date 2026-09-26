<?php

use yii\db\Migration;

/**
 * Class m231122_033644_migrate_dsv840_rencanaoperasi_v
 */
class m231122_033644_migrate_dsv840_rencanaoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS rencanaoperasi_v");
        $rencanaoperasi_v = file_get_contents(__DIR__ . '/definitions/rencanaoperasi_v.sql');
        $this->execute($rencanaoperasi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231122_033644_migrate_dsv840_rencanaoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231122_033644_migrate_dsv840_rencanaoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
