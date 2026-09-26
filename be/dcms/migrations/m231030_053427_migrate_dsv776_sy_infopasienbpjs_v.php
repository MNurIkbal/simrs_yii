<?php

use yii\db\Migration;

/**
 * Class m231030_053427_migrate_dsv776_sy_infopasienbpjs_v
 */
class m231030_053427_migrate_dsv776_sy_infopasienbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjs_v");
        $sy_kunjungan_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjs_v.sql');
        $this->execute($sy_kunjungan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_053427_migrate_dsv776_sy_infopasienbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_053427_migrate_dsv776_sy_infopasienbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
