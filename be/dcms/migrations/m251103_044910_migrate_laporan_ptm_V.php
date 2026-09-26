<?php

use yii\db\Migration;

/**
 * Class m251103_044910_migrate_laporan_ptm_V
 */
class m251103_044910_migrate_laporan_ptm_V extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanptm_v");
        $laporanptm_v = file_get_contents(__DIR__ . '/definitions/laporanptm_v.sql');
        $this->execute($laporanptm_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251103_044910_migrate_laporan_ptm_V cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251103_044910_migrate_laporan_ptm_V cannot be reverted.\n";

        return false;
    }
    */
}
