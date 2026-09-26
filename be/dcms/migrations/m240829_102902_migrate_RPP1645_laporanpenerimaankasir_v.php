<?php

use yii\db\Migration;

/**
 * Class m240829_102902_migrate_RPP1645_laporanpenerimaankasir_v
 */
class m240829_102902_migrate_RPP1645_laporanpenerimaankasir_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpenerimaankasir_v");
        $laporanpenerimaankasir_v = file_get_contents(__DIR__ . '/definitions/laporanpenerimaankasir_v.sql');
        $this->execute($laporanpenerimaankasir_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240829_102902_migrate_RPP1645_laporanpenerimaankasir_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240829_102902_migrate_RPP1645_laporanpenerimaankasir_v cannot be reverted.\n";

        return false;
    }
    */
}
